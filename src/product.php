<?php
session_start();
require_once 'config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    $sql = "SELECT * FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
            } else {
                echo "Product not found.";
                exit();
            }
        } else {
            echo "Error: Unable to execute SQL query.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error: Unable to prepare SQL statement.";
        exit();
    }
} else {
    echo "Error: Product ID not provided.";
    exit();
}

function addToCart($conn, $productId) {
    $sql = "INSERT INTO cart (product_id) VALUES (?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $productId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (addToCart($conn, $product_id)) {
        echo "<script>alert('Product added to cart!');</script>";
    } else {
        echo "<script>alert('Error adding product to cart.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

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

    <div class="container mx-auto mt-40">
        <div class="grid grid-cols-2 gap-8">
            <div>
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
            </div>
            <div>
                <h2 class="text-4xl font-semibold mb-2"><?php echo $product['name']; ?></h2>
                <h2 class="text-2xl font-semibold mb-2">$<?php echo $product['price']; ?></h2>
                <p class="text-gray-700"><?php echo $product['description']; ?></p>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" name="add_to_cart" class="bg-blue-800 hover:bg-blue-950 text-white font-bold py-2 px-4 rounded mt-4">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
