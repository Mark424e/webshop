<?php
session_start();
require_once 'config.php';

// Check if product ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Retrieve product details from the database
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Details</title>
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
        <div class="border p-4 rounded shadow-md">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
            <h2 class="text-2xl font-semibold mb-2"><?php echo $product['name']; ?></h2>
            <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?></p>
            <p class="text-gray-700"><?php echo $product['description']; ?></p>
        </div>
    </div>
</body>
</html>
