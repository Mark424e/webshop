<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $hashed_password = $user['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid email or password";
            }
        } else {
            $error_message = "Invalid email or password";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./output.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded shadow-md relative">
        <h2 class="text-2xl font-semibold mb-6">Login</h2>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" required class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" required class="border rounded w-full py-2 px-3 pr-10">
            </div>
            <div class="flex items-center justify-between">
                <a href="index.php" class="block text-gray-500">Back</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
