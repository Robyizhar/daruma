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

    /* Insert To Cart */
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_SESSION['user_id'];

        $insert = $Model->addToCart($user_id, $product_id, $quantity);
    
        if ($insert) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menambahkan ke keranjang"]);
        }
    }
    /* Insert To Cart */

    include("../inc/design/head.php");
    include("../inc/design/header.php");
    include("../inc/design/nav.php");
?>



<div class="container">
    <div class="product-description" style="overflow:auto; margin: 20px 0;">
        <?php if($product): ?>
        <!-- Left column: Product image -->
        <div class="product-image" style="float: left; width: 40%; text-align: center;">
            <img src="<?= base_url(htmlspecialchars($product['image'])) ?>" 
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
            <p class="description">
                <?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?>
            </p>
            <!-- Quantity Input -->
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" style="width: 60px; padding: 5px; text-align: center; margin-right: 10px;">
            <!-- Add to Cart Button -->
            <button class="add-to-cart" data-product="<?= $product['id'] ?>">Add to Cart</button>
        </div>
        <?php else: ?>
        <div><p>The product you are looking for is no longer available or has recently been removed.</p></div>
        <?php endif; ?>
        <div style="clear:both;"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.add-to-cart').click(function (e) { 
        e.preventDefault();
        const data = {
            product_id: $(this).data("product"),
            quantity: $("#quantity").val() || 1
        };

        $.ajax({
            url: "cart.php", 
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
                if(response.success) {
                    Swal.fire({ title: "Success!", text: response.message, icon: "success", timer: 2000, showConfirmButton: true }).then(() => {
                        $("#add-modal").modal("hide");
                        updateCartCount(response.data.length);
                        // location.reload();
                    });
                } else {
                    Swal.fire({ title: "Failed!", text: response.message, icon: "warning", timer: 2000,   showConfirmButton: true }).then(() => {
                        $("#add-modal").modal("hide");
                        // location.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({ title: "Failed!", text: "Failed to add cart!", icon: "error", showConfirmButton: true }).then(() => {
                    $("#add-modal").modal("hide");
                    // location.reload();
                });
                console.error(xhr.responseText);
            }
        });
    });

</script>

<?php include("../inc/design/footer.php"); ?>