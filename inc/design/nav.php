<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="nav-menu">
    <a href="/index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
    <a href="/inc/products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
    <a href="/inc/about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    <a href="/inc/account.php" class="<?php echo ($current_page == 'account.php') ? 'active' : ''; ?>">Account</a>
    <a href="/inc/register.php" class="<?php echo ($current_page == 'register.php') ? 'active' : ''; ?>">Register</a>
</nav>
