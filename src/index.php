<?php
session_start();
require_once 'config.php';

// Fetch distinct categories from the products table
$query_categories = "SELECT DISTINCT category FROM products";
$result_categories = mysqli_query($conn, $query_categories);

// Fetch distinct subcategories from the products table
$query_subcategories = "SELECT DISTINCT subcategory FROM products";
$result_subcategories = mysqli_query($conn, $query_subcategories);

// Function to fetch products based on category and/or subcategory
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

// Fetch products based on URL parameters
$category = $_GET['category'] ?? null;
$subcategory = $_GET['subcategory'] ?? null;
$result_products = fetchProducts($conn, $category, $subcategory);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Nested Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="./output.css" rel="stylesheet">
</head>

<?php include 'header.php'; ?>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-1">
                <h2 class="text-lg font-bold mb-2">Categories</h2>
                <ul class="space-y-2">
                    <?php while ($row = mysqli_fetch_assoc($result_categories)) : ?>
                        <li>
                            <a href="?category=<?php echo $row['category']; ?>" class="text-blue-500 hover:underline"><?php echo $row['category']; ?></a>
                            <ul class="pl-4">
                                <?php
                                // Fetch subcategories for each category
                                $category = $row['category'];
                                $query_subcategories = "SELECT DISTINCT subcategory FROM products WHERE category='$category'";
                                $result_subcategories = mysqli_query($conn, $query_subcategories);
                                while ($subcat_row = mysqli_fetch_assoc($result_subcategories)) :
                                ?>
                                    <li><a href="?category=<?php echo $row['category']; ?>&subcategory=<?php echo $subcat_row['subcategory']; ?>" class="text-gray-500 hover:underline"><?php echo $subcat_row['subcategory']; ?></a></li>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="col-span-2">
                <h2 class="text-lg font-bold mb-2">Products</h2>
                <div class="grid grid-cols-3 gap-4">
                    <?php while ($product = mysqli_fetch_assoc($result_products)) : ?>
                        <div class="flex flex-col justify-between border rounded p-4">
                            <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                            <div>
                                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mt-2">
                                <p class="text-gray-700 font-semibold">$<?php echo $product['price']; ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer.php'; ?>

</html>