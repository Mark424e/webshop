<?php
session_start();
require_once 'config.php';

// Fetch categories and subcategories from the database
$sql = "SELECT DISTINCT category, subcategory FROM products";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $subcategory = $row['subcategory'];
        if (!isset($categories[$category])) {
            $categories[$category] = [];
        }
        if (!in_array($subcategory, $categories[$category])) {
            $categories[$category][] = $subcategory;
        }
    }
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
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

    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto my-8">
        <h2 class="text-2xl font-semibold mb-6">Products</h2>

        <!-- Categories and Subcategories Menu -->
        <nav class="mb-6">
            <ul class="space-y-2">
                <?php foreach ($categories as $category => $subcategories): ?>
                    <li class="py-2">
                        <a href="#" class="text-blue-500 hover:underline"><?php echo $category; ?></a>
                        <?php if (!empty($subcategories)): ?>
                            <ul class="ml-4 space-y-2">
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <li class="py-2">
                                        <a href="#" class="text-gray-600 hover:underline"><?php echo $subcategory; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Product Display Grid -->
        <div class="grid grid-cols-4 gap-8">
            <?php foreach ($products as $product): ?>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="bg-white border p-4 rounded shadow-md product-card">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                        <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
