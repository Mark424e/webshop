

<header class="bg-gradient-to-tr from-blue-950 via-blue-800 to-cyan-500 top-0 p-4 fixed w-full">
  <nav>
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center gap-4">
        <h1 class="text-white text-2xl"><a href="./">Fishsticks</a></h1>
      
        <div class="relative">
          <button id="dropdown" class=" text-white py-2 px-4 rounded inline-flex items-center">
            <span>Products</span>
            <svg class="fill-current w-4 h-4 ml-2" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 14l-7-7h14l-7 7z"/>
            </svg>
          </button>
          <div id="dropdown-content" class="absolute left-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10 hidden">
            <ul>
              <?php while ($row = mysqli_fetch_assoc($result_categories)) : ?>
              <li class="bg-gray-200">
                <a href="?category=<?php echo $row['category']; ?>" class="pl-4 hover:underline"><?php echo $row['category']; ?></a>
                  <ul class="bg-white pl-4">
                    <?php
                    $category = $row['category'];
                    $query_subcategories = "SELECT DISTINCT subcategory FROM products WHERE category='$category'";
                    $result_subcategories = mysqli_query($conn, $query_subcategories);
                    while ($subcat_row = mysqli_fetch_assoc($result_subcategories)) :
                    ?>
                    <li><a href="?category=<?php echo $row['category']; ?>&subcategory=<?php echo $subcat_row['subcategory']; ?>" class="hover:underline"><?php echo $subcat_row['subcategory']; ?></a></li>
                    <?php endwhile; ?>
                  </ul>
                </li>
              <?php endwhile; ?>
              </ul>
          </div>
        </div>
      </div>

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