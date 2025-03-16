<footer>
    &copy; <?php echo date("Y"); ?> Daruma! - All rights reserved.
</footer>
<?php
    if (isset($_SESSION['user_id'])) {
        $Model = new Model();
        $data_carts = $Model->getCartByUser($_SESSION['user_id']);
        $count_carts = count($data_carts);
    }
?>
<script>
    const DATA_CART = <?php echo isset($data_carts) ? json_encode($data_carts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : '[]'; ?>;
    document.addEventListener("DOMContentLoaded", function () {
        console.info(DATA_CART.length, DATA_CART);
        updateCartCount(DATA_CART.length);
    });
</script>
</body>
</html>
