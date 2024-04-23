<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: restricted.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_GET['id'] ?? null;

    if (!$product_id || !is_numeric($product_id)) {
        header("Location: admin.php");
        exit();
    }

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $sql = "UPDATE products SET name=?, price=?, description=? WHERE id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sdsi", $name, $price, $description, $product_id);
        
        if ($stmt->execute()) {
          header("Location: admin.php");
          exit();
        } else {
            echo "Error updating product: " . $stmt->error;
            exit();
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
}

$product_id = $_GET['id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    echo "Invalid product ID.";
    exit();
}

$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $product = $result->fetch_assoc();
            $name = $product['name'];
            $price = $product['price'];
            $description = $product['description'];
        } else {
            header("Location: admin.php");
            exit();
        }
    } else {
        echo "Error: Unable to fetch product details.";
        exit();
    }
    $stmt->close();
} else {
    echo "Error: Unable to prepare SQL statement.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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

  <div class="max-w-md mx-auto p-6 bg-white rounded shadow-md mt-32">
    <h2 class="text-2xl font-semibold mb-6">Edit Product</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>" class="border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Price:</label>
            <input type="number" id="price" name="price" value="<?php echo $price; ?>" class="border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description:</label>
            <textarea id="description" name="description" class="border rounded w-full py-2 px-3"><?php echo $description; ?></textarea>
        </div>
        <button type="submit" class="bg-blue-800 hover:bg-blue-950 text-white font-bold py-2 px-4 rounded">Update Product</button>
    </form>
</div>

</body>
</html>
