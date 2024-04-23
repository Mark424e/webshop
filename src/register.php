<?php
session_start();
require_once 'config.php';

$name = $phone_number = $address = $email = $password = "";
$name_err = $phone_number_err = $address_err = $email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter your phone number.";
    } elseif (!preg_match("/^\d{8}$/", trim($_POST["phone_number"]))) {
        $phone_number_err = "Phone number must be 8 digits.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($name_err) && empty($phone_number_err) && empty($address_err) && empty($email_err) && empty($password_err)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, phone_number, address, email, password) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $param_name, $param_phone_number, $param_address, $param_email, $param_password);

            $param_name = $name;
            $param_phone_number = $phone_number;
            $param_address = $address;
            $param_email = $email;
            $param_password = $password_hash;

            if ($stmt->execute()) {
                header("location: login.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Register</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
                <input type="text" id="name" name="name" required class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $name_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-gray-700 font-bold mb-2">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{8}" required class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $phone_number_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-bold mb-2">Address:</label>
                <input type="text" id="address" name="address" required class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $address_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" required class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $email_err; ?></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" required class="border rounded w-full py-2 px-3">
                <span class="text-red-500"><?php echo $password_err; ?></span>
            </div>
            <div class="flex items-center justify-between">
                <a href="index.php" class="block text-gray-500">Back</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
