

<header class="bg-gray-800 p-4 mb-16">
  <nav>
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-white text-2xl"><a href="./">Fishsticks</a></h1>
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
</header>