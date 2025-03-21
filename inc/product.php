<?php include("./sql/db.php"); ?>

<?php
    session_start();
    /* Make sure ID valid */
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header("Location: products.php");
        exit;
    }
    /* Make sure ID valid */

    $Model = new Model();
    $product = $Model->getProductById($id);

    include("../inc/design/head.php");
    include("../inc/design/header.php");
    include("../inc/design/nav.php");
?>

<div class="container">
    <div class="product-description" style="overflow:auto; margin: 20px 0;">
        <?php if($product): ?>
        <!-- Left column: Product image -->
        <div class="product-image" style="float: left; width: 40%; text-align: center;">
            <img 
                src="<?= base_url(htmlspecialchars($product['image'])) ?>" 
                onerror="this.onerror=null; this.src='<?= base_url('images/products/default_image.png') ?>';" 
                alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                style="max-width:100%;"
            >
        </div>
        <!-- Right column: Product details and add to cart button -->
        <div class="product-info" style="float: right; width: 55%; text-align: left;">
            <h2>
                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <p class="edition">
                <?= htmlspecialchars($product['edition'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p class="price">
                $<?= number_format($product['price']); ?>
            </p>
            <!-- Stock Information -->
            <p class="stock" style="color: <?= ($product['stock'] > 0) ? 'green' : 'red' ?>;">
                Stock: <?= $product['stock'] > 0 ? $product['stock'] . ' available' : 'Out of stock' ?>
            </p>
            <p class="description">
                <?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?>
            </p>
            <!-- Quantity Input -->
            <?php if($product['stock'] > 0): ?>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="number-only" style="width: 60px; padding: 5px; text-align: center; margin-right: 10px;" <?= $product['stock'] > 0 ? '' : 'disabled' ?>>
            <?php endif; ?>
            <!-- Add to Cart Button -->
            <button class="add-to-cart" data-product="<?= $product['id'] ?>" <?= $product['stock'] > 0 ? '' : 'disabled' ?>>
                <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
            </button>
        </div>
        <?php else: ?>
        <div><p>The product you are looking for is no longer available or has recently been removed.</p></div>
        <?php endif; ?>
        <div style="clear:both;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.add-to-cart').click(function (e) { 
            e.preventDefault();
            const data = {
                product_id: $(this).data("product"),
                quantity: $("#quantity").val() || 1, 
                action: 'add-cart'
            };
            $.ajax({
                url: "cart.php", 
                type: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        Swal.fire({ title: "Success!", text: response.message, icon: "success", timer: 2000, showConfirmButton: true }).then(() => {
                            updateCartCount(response.data.length);
                        });
                    } else {
                        Swal.fire({ title: "Failed!", text: response.message, icon: "warning", timer: 2000,   showConfirmButton: true }).then(() => {
                            window.location.assign(response.data_redirect ? response.data_redirect : "account.php");
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({ title: "Failed!", text: xhr.responseText, icon: "error", showConfirmButton: true }).then(() => {
                        location.reload();
                    });
                }
            });
        });
    });
</script>

<?php include("../inc/design/footer.php"); ?>