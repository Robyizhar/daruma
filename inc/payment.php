<?php
    session_start();
    include("./sql/db.php");
    $Model = new Model();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $data_carts = $Model->getCartByUser($_SESSION['user_id']);
    $count_carts = count($data_carts);

    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    include("../inc/design/footer.php");

?>


<div class="container mt-5">
    <?php if($count_carts > 0): ?>

    <h2 class="title-payment">Payment Form</h2>
    <p class="title-payment">Please fill in the payment and shipping information.</p>

    <form id="payment-form">
        <h4>Billing & Delivery Details</h4>
        <input name="name" type="text" class="form-control mb-2" placeholder="Nama" required>
        <input name="shipping_address" type="text" class="form-control mb-2" placeholder="Delivery Address" required>
        <input name="cc_number" type="text" class="form-control mb-2" placeholder="Credit Card Number (dummy)" required>
        <input type="text" name="phone_number" class="form-control mb-2" placeholder="Nomor HP" required>
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="type" value="store_order">
        <button type="submit" class="btn btn-primary btn-lg w-100">Pay Now</button>
    </form>

    <div id="payment-processing" class="d-none text-center mt-3">
        <h4>Processing Payment...</h4>
        <div class="spinner-border text-success"></div>
    </div>

    <div id="payment-success" class="d-none text-center mt-3">
        <h4 class="text-success">Thank you for your purchase!</h4>
        <p><a href="account.php">View order status in Account</a></p>
    </div>

    <?php else: ?>
    <div id="payment-success" class="text-center mt-3">
        <h4 class="text-warning">You don't have any orders yet!</h4>
        <p><a href="products.php">Please visit the product page to add an order.</a></p>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $("#payment-form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData($("#payment-form")[0]);
            
            $("#payment-form").hide();
            $(".title-payment").hide();
            $("#payment-processing").removeClass("d-none");

            setTimeout(function() {
                $.ajax({
                    url: "order.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.success) {
                            $("#payment-processing").addClass("d-none");
                            $("#payment-success").removeClass("d-none");
                            updateCartCount(0);
                        } else {
                            Swal.fire({ 
                                title: "Warning!", text: res.message, icon: "warning", showConfirmButton: true
                            }).then(() => {
                                $("#payment-form").show();
                                $(".title-payment").show();
                                $("#payment-processing").addClass("d-none"); 
                            });
                        }
                    },
                    error: function() {
                        alert("Payment failed. Please try again.");
                    }
                });
            }, 5000);
        });
    });
</script>
