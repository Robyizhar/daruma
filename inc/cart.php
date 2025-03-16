<?php
    session_start();
    include("./sql/db.php");
    $Model = new Model();

    if (!isset($_SESSION['user_id'])) {
        header("Location: account.php");
        exit;
    }

    /* Insert To Cart */
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_SESSION['user_id'];

        $insert = $Model->addToCart($user_id, $product_id, $quantity);
        if ($insert) {
            echo json_encode(["success" => true, "message" => "Product successfully added to cart!", "data" => $insert]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add to cart!"]);
        }
        exit;
    }
    /* Insert To Cart */

    $data_carts = $Model->getCartByUser($_SESSION['user_id']);
    $count_carts = count($data_carts);

    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    include("../inc/design/footer.php");
?>
<style>
    #cart-table-body tr {
        transition: background-color 0.3s ease-in-out;
    }

    #cart-table-body tr:hover {
        background-color: rgba(222, 222, 222, 0.5);
    }
</style>
<div class="container mt-5">
    <h2 class="mb-4">Keranjang Belanja</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Added Time</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody id="cart-table-body">
                <?php 
                    $index = 1; 
                    $all_quantities = 0;
                    $all_prices = 0;
                ?>
                <?php foreach($data_carts as $row): ?>
                    <tr data-id="<?= $row['id'] ?>" onclick="goToProduct(<?= $row['product_id'] ?>)" style="cursor: pointer;">
                        <td class="text-white"><?= $index; ?></td>
                        <td class="text-white"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-white">$<?= number_format($row['price'], 2) ?></td>
                        <td class="text-white"><?= htmlspecialchars($row['added_at']) ?></td>
                        <td class="text-white"><?= htmlspecialchars($row['quantity']) ?></td>
                        <td class="text-white text-end">$ <?= number_format(htmlspecialchars($row['quantity']) *  $row['price'], 2) ?></td>
                    </tr>
                <?php 
                    $index++;
                    $all_quantities += $row['quantity'];
                    $all_prices += $row['quantity'] * $row['price'];
                ?>
                <?php endforeach; ?>
                <tr class="table-dark">
                    <td class="text-center" colspan="4"><strong>Total</strong></td>
                    <td><strong><?= $all_quantities ?></strong></td>
                    <td class="text-end"><strong>$ <?= number_format($all_prices, 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <a href="payment.php" class="btn btn-success btn-lg">
            <i class="fas fa-shopping-cart"></i> Proceed to Payment
        </a>
    </div>

</div>

<script>
    let COUNT_CART = <?php echo $count_carts; ?>;
    document.addEventListener("DOMContentLoaded", function () {
        updateCartCount(COUNT_CART);
    });
    
    function goToProduct(productId) {
        window.location.href = `product.php?id=${productId}`;
    }

</script>