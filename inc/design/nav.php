<?php
    // session_start();
    $current_page = basename($_SERVER['PHP_SELF']);
    $data_carts = [];
    $count_carts = 0;


?>

<nav id="nav-menu">
    <a href="<?= base_url('index.php') ?>" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
    <a href="<?= base_url('inc/products.php') ?>" class="<?= ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
    <a href="<?= base_url('inc/about.php') ?>" class="<?= ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    <a href="<?= base_url('inc/account.php') ?>" class="<?= ($current_page == 'account.php') ? 'active' : ''; ?>">Account</a>
    <a href="<?= base_url('inc/register.php') ?>" class="<?= ($current_page == 'register.php') ? 'active' : ''; ?>">Register</a>
    <a href="<?= base_url('inc/cart.php') ?>" class="btn btn-outline-light position-relative">
        <i class="fas fa-shopping-cart"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
            <?= $count_carts ?>
        </span>
    </a>
</nav>
