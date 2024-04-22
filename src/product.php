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
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include 'header.php'; ?>

    <div class="container mx-auto mt-10">
        <div class="border p-4 rounded shadow-md">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
            <h2 class="text-2xl font-semibold mb-2"><?php echo $product['name']; ?></h2>
            <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?></p>
            <p class="text-gray-700"><?php echo $product['description']; ?></p>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
