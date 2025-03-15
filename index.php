<?php include("inc/design/head.php"); ?>
<?php include("inc/design/header.php"); ?>
<?php include("inc/design/nav.php"); ?>

<style>
    .product-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
    .product {
        text-align: center;
        /* width: 150px; */
    }
    /* .product img {
        width: 100%;
        max-width: 112px;
        height: auto;
    } */
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col text-center">
            <h1 class="text-center">Welcome to Daruma!</h1>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col">
            <h3 class="text-center">New Products</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=2">
                    <img src="<?= base_url('images/products/iphone_16_pro.png') ?>" alt="iPhone 16 Pro - White Titanium" class="img-fluid">
                </a>
                <h3><a href="product.php?id=2">iPhone 16 Pro</a></h3>
                <p class="text-muted">White Titanium</p>
                <p class="fw-bold">From $1,599</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=3">
                    <img src="<?= base_url('images/products/iphone_16.png') ?>" alt="iPhone 16 - Teal" class="img-fluid">
                </a>
                <h3><a href="product.php?id=3">iPhone 16</a></h3>
                <p class="text-muted">Teal</p>
                <p class="fw-bold">From $1,299</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=5">
                    <img src="<?= base_url('images/products/iphone_15.png') ?>" alt="iPhone 15 Pro - Natural Titanium" class="img-fluid">
                </a>
                <h3><a href="product.php?id=5">iPhone 15 Pro</a></h3>
                <p class="text-muted">Natural Titanium</p>
                <p class="fw-bold">From $1,399</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3 class="text-center">Best Sellers</h3>
        </div>
    </div>
    <div class="row">
    <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=2">
                    <img src="<?= base_url('images/products/iphone_16_pro.png') ?>" alt="iPhone 16 Pro - White Titanium" class="img-fluid">
                </a>
                <h3><a href="product.php?id=2">iPhone 16 Pro</a></h3>
                <p class="text-muted">White Titanium</p>
                <p class="fw-bold">From $1,599</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=3">
                    <img src="<?= base_url('images/products/iphone_16.png') ?>" alt="iPhone 16 - Teal" class="img-fluid">
                </a>
                <h3><a href="product.php?id=3">iPhone 16</a></h3>
                <p class="text-muted">Teal</p>
                <p class="fw-bold">From $1,299</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product text-center pb-4">
                <a href="product.php?id=5">
                    <img src="<?= base_url('images/products/iphone_15.png') ?>" alt="iPhone 15 Pro - Natural Titanium" class="img-fluid">
                </a>
                <h3><a href="product.php?id=5">iPhone 15 Pro</a></h3>
                <p class="text-muted">Natural Titanium</p>
                <p class="fw-bold">From $1,399</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3 class="text-center">News</h3>
            <p class="text-center">Apple launches new line of flagship phones in March</p>
            <p class="text-center">New Apple stock available at our online store</p>
        </div>
    </div>
</div>


<?php include("inc/design/footer.php"); ?>
