<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="nav-menu">
    <a href="<?= base_url('index.php') ?>" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
    <a href="<?= base_url('inc/products.php') ?>" class="<?= ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
    <a href="<?= base_url('inc/about.php') ?>" class="<?= ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    <a href="<?= base_url('inc/account.php') ?>" class="<?= ($current_page == 'account.php') ? 'active' : ''; ?>">Account</a>
    <a href="<?= base_url('inc/register.php') ?>" class="<?= ($current_page == 'register.php') ? 'active' : ''; ?>">Register</a>
</nav>
