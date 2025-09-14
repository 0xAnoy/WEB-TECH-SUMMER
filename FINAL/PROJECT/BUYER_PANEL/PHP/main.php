<?php
// main.php - include at top of pages (after session_start if session needed)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>eShop</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="site-body">

<header class="site-header">
  <div class="container header-inner">
  <a href="../index.html" class="brand">eShop</a>
    <nav class="nav-main">
      <a href="../index.html">Home</a>
      <a href="products.php">Products</a>
      <a href="cart.php">Cart</a>
      <?php
        // Determine logged state: session or remember cookies
        $loggedIn = isset($_SESSION['user_id']);
        if (!$loggedIn && isset($_COOKIE['remember_user_id']) && $_COOKIE['remember_user_id'] !== '') {
            $_SESSION['user_id'] = intval($_COOKIE['remember_user_id']);
            if (!isset($_SESSION['username']) && isset($_COOKIE['remember_username'])) {
                $_SESSION['username'] = $_COOKIE['remember_username'];
            }
            $loggedIn = true;
        }
        if ($loggedIn): ?>
          <a href="profile.php"><?=htmlspecialchars($_SESSION['username'] ?? 'Account')?></a>
          <a href="logout.php" class="logout">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Global toast container (for all pages) -->
<div class="toast-container" id="toastContainer"></div>

<!-- Category Navigation Bar -->
<nav class="category-bar">
  <ul class="category-list">
    <li><a href="products.php" class="category-link">All</a></li>
    <li><a href="products.php?category=Laptops" class="category-link">Laptops</a></li>
    <li><a href="products.php?category=Accessories" class="category-link">Accessories</a></li>
    <li><a href="products.php?category=Monitors" class="category-link">Monitors</a></li>
    <li><a href="products.php?category=Printers" class="category-link">Printers</a></li>
    <li><a href="products.php?category=Networking" class="category-link">Networking</a></li>
    <li><a href="products.php?category=Chairs" class="category-link">Chairs</a></li>
    <li><a href="products.php?category=Tablets" class="category-link">Tablets</a></li>
  </ul>
</nav>

<main class="container main-content">
        </body>
</html>