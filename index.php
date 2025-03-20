<?php include("inc/design/head.php"); ?>
<?php include("inc/design/header.php"); ?>
<?php include("inc/design/nav.php"); ?>
<?php include("inc/sql/db.php"); ?>
<?php session_start(); ?>

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
</style>

<?php  

    $Model = new Model();
    $productsPerPage = 3;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;
    $totalProducts = $Model->getTotalProducts();
    $totalPages = ceil($totalProducts / $productsPerPage);
    $resultNewProducts = $Model->getProducts($productsPerPage, $offset);

?>

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
        <?php while ($rowNewProducts = $resultNewProducts->fetch_assoc()): ?>
            <?php $product_url = "inc/product.php?id=" . $rowNewProducts['id']; ?>
            <div class="col-md-4">
                <div class="product text-center pb-4">
                    <a href="<?= $product_url ?>">
                        <img 
                            src="<?= base_url(htmlspecialchars($rowNewProducts['image'])) ?>" 
                            onerror="this.onerror=null; this.src='<?= base_url('images/products/default_image.png') ?>';" 
                            alt="<?= htmlspecialchars($rowNewProducts['name']) ?>" 
                            class="img-fluid"
                        >
                    </a>
                    <h3><a href="<?= $product_url ?>"><?= htmlspecialchars($rowNewProducts['name']) ?></a></h3>
                    <p class="text-muted"><?= htmlspecialchars($rowNewProducts['edition']) ?></p>
                    <p class="fw-bold">From $<?= number_format($rowNewProducts['price'], 2) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3 class="text-center">Best Sellers</h3>
        </div>
    </div>
    <div class="row">
        <?php while ($rowNewProducts = $resultNewProducts->fetch_assoc()): ?>
            <?php $product_url = "inc/product.php?id=" . $rowNewProducts['id']; ?>
            <div class="col-md-4">
                <div class="product text-center pb-4">
                    <a href="<?= $product_url ?>">
                        <img src="<?= base_url(htmlspecialchars($rowNewProducts['image'])) ?>" alt="iPhone 16 Pro - White Titanium" class="img-fluid">
                    </a>
                    <h3><a href="<?= $product_url ?>"><?= htmlspecialchars($rowNewProducts['name']) ?></a></h3>
                    <p class="text-muted"><?= htmlspecialchars($rowNewProducts['edition']) ?></p>
                    <p class="fw-bold">From $<?= number_format($rowNewProducts['price'], 2) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
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
