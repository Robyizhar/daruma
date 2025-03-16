<?php include("../inc/design/head.php"); ?>
<?php include("../inc/design/header.php"); ?>
<?php include("../inc/design/nav.php"); ?>
<?php include("./sql/db.php"); ?>

<?php
    $Model = new Model($conn);

    // Make sure ID valid
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header("Location: products.php");
        exit;
    }
    $product = $Model->getProductById($id);    
?>

<div class="container">
    <div class="product-description" style="overflow:auto; margin: 20px 0;">
        <?php if($product): ?>
        <!-- Left column: Product image -->
        <div class="product-image" style="float: left; width: 40%; text-align: center;">
            <!-- Since the images haven't been added yet, we use a placeholder -->
            <img src="<?= base_url(htmlspecialchars($product['image'])) ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width:100%;">
        </div>
        <!-- Right column: Product details and add to cart button -->
        <div class="product-info" style="float: right; width: 55%; text-align: left;">
            <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="edition"><?= htmlspecialchars($product['edition'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="price">$<?= number_format($product['price']); ?></p>
            <p class="description"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></p>
            <button class="add-to-cart">Add to Cart</button>
        </div>
        <?php else: ?>
        <div><p>The product you are looking for is no longer available or has recently been removed.</p></div>
        <?php endif; ?>
        <div style="clear:both;"></div>
    </div>
</div>

<?php include("../inc/design/footer.php"); ?>