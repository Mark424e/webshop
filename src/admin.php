<?php
session_start();
require_once 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users to a restricted area message page
    header("Location: restricted.php");
    exit();
}

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

// Initialize variables for form submission
$name = $price = $description = $category = $image_url = "";
$name_err = $price_err = $description_err = $category_err = $image_url_err = "";

// Process form submission & Validators
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the product name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the product price.";
    } else {
        $price = trim($_POST["price"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the product description.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please enter the product category.";
    } else {
        $category = trim($_POST["category"]);
    }

    if (empty(trim($_POST["subcategory"]))) {
        $subcategory_err = "Please select the product subcategory.";
    } else {
        $subcategory = trim($_POST["subcategory"]);
    }
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Check if the file is an image
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $file_info = pathinfo($_FILES["image"]["name"]);
        $file_extension = strtolower($file_info['extension']);
        if (in_array($file_extension, $allowed_types)) {
            // Generate a unique filename to prevent overwriting existing files
            $image_filename = uniqid() . '.' . $file_extension;
            // Move the uploaded file to a designated folder
            move_uploaded_file($_FILES["image"]["tmp_name"], "assets/uploads/" . $image_filename);
            // Set the image URL to the path of the uploaded file
            $image_url = "assets/uploads/" . $image_filename;
        } else {
            $image_url_err = "Please upload a valid image file (jpg, jpeg, png, gif).";
        }
    }

    // Check if all fields are valid
    if (empty($name_err) && empty($price_err) && empty($description_err) && empty($category_err) && empty($subcategory_err) && empty($image_url_err)) {
        // Prepare SQL statement for inserting data into products table
        $sql = "INSERT INTO products (name, price, description, category, subcategory, image_url) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ssssss", $name, $price, $description, $category, $subcategory, $image_url);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to admin page after successful insertion
                header("Location: admin.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare SQL statement to delete product from the database
    $sql = "DELETE FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind product ID parameter
        $stmt->bind_param("i", $product_id);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Redirect back to admin page after successful deletion
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: Unable to delete the product.";
        }

        // Close statement
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl">WebShop Admin</h1>
            <div>
                <a href="index.php" class="text-white mx-2">Home</a>
                <a href="logout.php" class="text-white mx-2">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Add Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $name_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-bold mb-2">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo $price; ?>" class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $price_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="border rounded w-full py-2 px-3"><?php echo $description; ?></textarea>
                <span class="text-red-500"><?php echo $description_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="category" class="block text-gray-700 font-bold mb-2">Category:</label>
                <select id="category" name="category" class="border rounded w-full py-2 px-3 mb-3" required>
                    <option value="">Select Category</option>
                    <option value="clothes">Clothes</option>
                    <option value="jewellery">Jewellery</option>
                    <option value="posters">Posters</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div id="subcategoryField" class="mb-4" style="display: none;">
                <label for="subcategory" class="block text-gray-700 font-bold mb-2">Subcategory:</label>
                <select id="subcategory" name="subcategory" class="border rounded w-full py-2 px-3 mb-3" required>
                    <option value="">Select Subcategory</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 font-bold mb-2">Image:</label>
                <input type="file" id="image" name="image" accept="image/*" class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $image_url_err; ?></span>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Product</button>
        </form>
    </div>

    <div class="container mx-auto mt-10">
        <h2 class="text-2xl font-semibold mb-6">Manage Products</h2>
        <div class="grid grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="border p-4 rounded shadow-md product-card">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="mb-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                        <p class="text-gray-600 mb-2">$<?php echo $product['price']; ?></p>
                    </div>
                    <div>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">Edit</a>
                        <a href="admin.php?id=<?php echo $product['id']; ?>" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.getElementById('category').addEventListener('change', function() {
            var category = this.value;
            var subcategoryField = document.getElementById('subcategoryField');
            var subcategoryDropdown = document.getElementById('subcategory');
            
            // Toggle visibility of subcategory field based on selected category
            if (category === 'clothes' || category === 'jewellery') {
                subcategoryField.style.display = 'block';
            } else {
                subcategoryField.style.display = 'none';
            }

            // Clear existing options
            subcategoryDropdown.innerHTML = '';
            var defaultOption = document.createElement('option');
            defaultOption.text = 'Select Subcategory';
            defaultOption.value = '';
            subcategoryDropdown.appendChild(defaultOption);

            if (category === 'clothes') {
                var clothesSubcategories = ['t-shirts', 'hoodies'];
                clothesSubcategories.forEach(function(subcategory) {
                    var option = document.createElement('option');
                    option.text = subcategory.charAt(0).toUpperCase() + subcategory.slice(1); // Capitalize first letter
                    option.value = subcategory;
                    subcategoryDropdown.appendChild(option);
                });
            } else if (category === 'jewellery') {
                var jewellerySubcategories = ['necklaces', 'bracelets'];
                jewellerySubcategories.forEach(function(subcategory) {
                    var option = document.createElement('option');
                    option.text = subcategory.charAt(0).toUpperCase() + subcategory.slice(1); // Capitalize first letter
                    option.value = subcategory;
                    subcategoryDropdown.appendChild(option);
                });
            }
        });
    </script>

</body>
</html>

