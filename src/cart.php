<?php
session_start();
require_once 'config.php';

// Check if the form is submitted for removing a product from the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_product'])) {
    // Validate product ID
    if (isset($_POST["product_id"]) && is_numeric($_POST["product_id"])) {
        $product_id = $_POST["product_id"];

        // Remove one entry of the product from the cart table
        $sql = "DELETE FROM cart WHERE product_id = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $product_id);
            if ($stmt->execute()) {
                // Product successfully removed from the cart
                header("Location: cart.php"); // Refresh the page
                exit();
            } else {
                // Error handling
                echo "Error removing product from cart.";
            }
            $stmt->close();
        } else {
            // Error handling
            echo "Error preparing statement.";
        }
    } else {
        // Error handling
        echo "Invalid product ID.";
    }
}

// Fetch products from the cart table and group them by product_id
$sql = "SELECT products.*, COUNT(*) AS quantity FROM products INNER JOIN cart ON products.id = cart.product_id GROUP BY products.id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = [];
}

$totalPrice = 0;
foreach ($products as $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="./output.css" rel="stylesheet">
</head>
<body>

    <header class="bg-gradient-to-tr from-blue-950 via-blue-800 to-cyan-500 top-0 p-4 fixed w-full">
        <nav>
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <h1 class="text-white text-2xl"><a href="./">Fishsticks</a></h1>   
                </div>
                <div class="flex">
                    <a href="index.php" class="text-white mx-2">Home</a>
                        <?php if (isset($_SESSION['email'])): ?>
                    <a href="logout.php" class="text-white mx-2">Logout</a>
                        <?php else: ?>
                    <a href="login.php" class="text-white mx-2">Login</a>
                    <a href="register.php" class="text-white mx-2">Register</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="text-white mx-2">Admin</a>
                        <?php endif; ?>
                    <a href="cart.php" class="text-white ms-8 me-2">
                        <img src="./assets/images/cart-shopping-solid.svg" class="min-w-7"/>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <h1 class="text-2xl font-bold mb-4">Shopping Cart</h1>
    <div class="container mx-auto mt-40">
        <div class="grid grid-cols-2 gap-8">
            <div>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="flex bg-white shadow-md rounded p-4 mb-4">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="w-24 h-24 mr-4">
                            <div>
                                <h2 class="text-lg font-semibold"><?php echo $product['name']; ?></h2>
                                <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?> (Quantity: <?php echo $product['quantity']; ?>)</p>
                                <form action="" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="remove_product" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-auto">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600">Your cart is empty.</p>
                <?php endif; ?>
            </div>
            <div class="bg-white shadow-md rounded p-4 mb-4">
                <p class="text-lg font-bold">Total Price: $<?php echo $totalPrice; ?></p>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
