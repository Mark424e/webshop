<?php
session_start();
require_once 'config.php';

// Retrieve products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check if there are products in the database
if ($result->num_rows > 0) {
    // Fetch products as associative array
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = []; // Initialize empty array if no products found
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl">WebShop</h1>
            <div>
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
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        <h2 class="text-2xl font-semibold mb-6">Products</h2>
        <div class="grid grid-cols-4 gap-10">
            <?php foreach ($products as $product): ?>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="block border p-4 rounded shadow-md">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
                    <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                    <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
