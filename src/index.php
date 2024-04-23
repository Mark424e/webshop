<?php
session_start();
require_once 'config.php';

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

$query_categories = "SELECT DISTINCT category FROM products";
$result_categories = mysqli_query($conn, $query_categories);
$query_subcategories = "SELECT DISTINCT subcategory FROM products";
$result_subcategories = mysqli_query($conn, $query_subcategories);

$category = $_GET['category'] ?? null;
$subcategory = $_GET['subcategory'] ?? null;
$result_products = fetchProducts($conn, $category, $subcategory);

function fetchProducts($conn, $category = null, $subcategory = null) {
  $query = "SELECT * FROM products";
  if ($category && $subcategory) {
      $query .= " WHERE category='$category' AND subcategory='$subcategory'";
  } elseif ($category) {
      $query .= " WHERE category='$category'";
  } elseif ($subcategory) {
      $query .= " WHERE subcategory='$subcategory'";
  }
  $result = mysqli_query($conn, $query);
  return $result;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Fishsticks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="./output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    
    <?php include 'header.php'; ?>

    <div id="hero" class="bg-cover bg-center py-40 mb-16">
        <div class="container mx-auto px-4 mt-20">
            <div class="text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Welcome to Fishsticks Merchandise</h1>
            <p class="text-lg text-white mb-8">Get the coolest fish-themed products here!</p>
            <a href="#products" class="bg-white text-gray-800 font-semibold py-2 px-6 rounded-full inline-block transition hover:scale-105 ease-in-out">Shop Now</a>
            </div>
        </div>
    </div>

    <div id="products" class="container mx-auto p-4">
        <h2 class="text-lg font-bold mb-2">Products</h2>
        <div class="grid grid-cols-4 gap-4">
            <?php while ($product = mysqli_fetch_assoc($result_products)) : ?>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="flex flex-col justify-between bg-white border rounded p-4">
                    <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                    <div>
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="my-4">
                        <p class="text-gray-700 font-semibold">$<?php echo $product['price']; ?></p>
                    </div>
                    <div class="flex justify-center">
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="bg-blue-800 hover:bg-blue-950 text-white font-bold py-2 px-4 rounded">Add to Cart</button>
                        </form>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
<script src="script.js"></script>
</body>
</html>