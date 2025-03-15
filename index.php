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
        width: 150px;
    }
    .product img {
        width: 100%;
        max-width: 112px;
        height: auto;
    }
</style>

<div class="container">
    <h1>Welcome to Daruma!</h1>
</div>

<div class="container">
    <h3>New Products</h3>
</div>

<div class="product-container">
    <div class="product">
        <a href="product.php?id=2">
            <img src="../images/iphone16prowhitetitanium.png" alt="iPhone 16 Pro - White Titanium">
        </a>
        <h3><a href="product.php?id=2">iPhone 16 Pro</a></h3>
        <p class="product-color">White Titanium</p>
        <p>From $1,599</p>
    </div>

    <div class="product">
    <a href="product.php?id=3">
                <img src="../images/iphone16teal.png" alt="iPhone 16 - Teal">
            </a>
            <h3><a href="product.php?id=3">iPhone 16</a></h3>
            <p class="product-color">Teal</p>
            <p>From $1,299</p>
    </div>

    <div class="product">
    <a href="product.php?id=5">
                <img src="../images/iphone15pronaturaltitanium.png" alt="iPhone 15 Pro - Natural Titanium">
            </a>
            <h3><a href="product.php?id=5">iPhone 15 Pro</a></h3>
            <p class="product-color">Natural Titanium</p>
            <p>From $1,399</p>
    </div>
</div>

<br>
<br>
<div class="container">
    <h3>Best Sellers</h3>
</div>


<div class="product-container">
    <div class="product">
    <a href="product.php?id=3">
                <img src="../images/iphone16teal.png" alt="iPhone 16 - Teal">
            </a>
            <h3><a href="product.php?id=3">iPhone 16</a></h3>
            <p class="product-color">Teal</p>
            <p>From $1,299</p>
    </div>

    <div class="product">
        <a href="product.php?id=2">
            <img src="../images/iphone16prowhitetitanium.png" alt="iPhone 16 Pro - White Titanium">
        </a>
        <h3><a href="product.php?id=2">iPhone 16 Pro</a></h3>
        <p class="product-color">White Titanium</p>
        <p>From $1,599</p>
    </div>

    <div class="product">
    <a href="product.php?id=4">
                <img src="../images/iphone16pink.png" alt="iPhone 16 - Pink">
            </a>
            <h3><a href="product.php?id=4">iPhone 16</a></h3>
            <p class="product-color">Pink</p>
            <p>From $1,299</p>
    </div>
</div>


<br>
<br>
<div class="container">
    <h3>News</h3>
    <p>Apple launches new line of flagship phones in March</p>
    <p>New Apple stock available at our online store</p>
</div>

<?php include("inc/design/footer.php"); ?>
