<?php
    session_start();
    include("./sql/db.php");
    $Model = new Model();

    // Redirect if not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: account.php");
        exit;
    }

    // Get cart items
    $data_carts = $Model->getCartByUser($_SESSION['user_id']);
    $count_carts = count($data_carts);

    // Include site design/layout files
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
            
            <!-- Full Name -->
            <input 
                name="name" 
                type="text" 
                class="form-control mb-2" 
                placeholder="Full Name" 
                required
            >
            
            <!-- Delivery Address -->
            <input 
                name="shipping_address" 
                type="text" 
                class="form-control mb-2" 
                placeholder="Delivery Address" 
                required
            >
            
            <!-- Credit Card Number --> 
            <!-- 4 digits + space + 4 digits + space + 4 digits + space + 4 digits (total 16 digits, 3 spaces) -->
            <input 
                name="cc_number" 
                type="text" 
                class="form-control mb-2" 
                placeholder="Credit Card Number" 
                required
                pattern="^[0-9]{4}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}$"
                maxlength="19"
            >
            
            <!-- CVV (exactly 3 digits) -->
            <input 
                name="cvv_number" 
                type="tel" 
                class="form-control mb-2"
                placeholder="CVV" 
                required
                pattern="^[0-9]{3}$"
                maxlength="3"
            >
            
            <!-- Contact Number (exactly 8 digits) -->
            <input 
                name="phone_number" 
                type="tel" 
                class="form-control mb-2" 
                placeholder="Contact Number" 
                required
                pattern="^[0-9]{8}$"
                maxlength="8"
            >

            <!-- Hidden fields for user info and order type -->
            <input 
                type="hidden" 
                name="user_id" 
                value="<?= $_SESSION['user_id'] ?>"
            >
            <input 
                type="hidden" 
                name="type" 
                value="store_order"
            >

            <button 
                type="submit" 
                class="btn btn-primary btn-lg w-100"
            >
                Pay Now
            </button>
        </form>

        <div 
            id="payment-processing" 
            class="d-none text-center mt-3"
        >
            <h4>Processing Payment...</h4>
            <div class="spinner-border text-success"></div>
        </div>

        <div 
            id="payment-success" 
            class="d-none text-center mt-3"
        >
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

<!-- SweetAlert2 for nicer alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const user_name = "<?= $_SESSION['fname'] ?>";
    const user_email = "<?= $_SESSION['femail'] ?>";

    // Automatically insert spaces every 4 digits in CC field
    $(document).on("input", "input[name='cc_number']", function() {
        // Remove all spaces, keep only digits
        let rawVal = this.value.replace(/\s+/g, '').replace(/\D/g, '');
        // Break into chunks of up to 4 digits
        let chunks = rawVal.match(/\d{1,4}/g);
        // Join them with a space
        let spacedVal = chunks ? chunks.join(' ') : '';
        this.value = spacedVal;
    });

    $(document).ready(function() {
        $("#payment-form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData($("#payment-form")[0]);

            // Hide form and title, show "Processing"
            $("#payment-form").hide();
            $(".title-payment").hide();
            $("#payment-processing").removeClass("d-none");

            // Simulate a delay before sending the request
            setTimeout(function() {
                $.ajax({
                    url: "order.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Parse response if it's a JSON string
                        const res = typeof response === 'string' ? JSON.parse(response) : response;

                        if (res.success) {
                            // Simulate sending an email after payment
                            setTimeout(() => {
                                sendEmail(user_name, user_email)
                            }, 2000);

                            // Hide "Processing" and show success message
                            $("#payment-processing").addClass("d-none");
                            $("#payment-success").removeClass("d-none");

                            // Clear or reset cart count on the UI (if you have that function)
                            updateCartCount(0);
                        } else {
                            // Show error/warning in SweetAlert
                            Swal.fire({ 
                                title: "Warning!",
                                text: res.message,
                                icon: "warning",
                                showConfirmButton: true
                            }).then(() => {
                                // Re-show form upon dismissal
                                $("#payment-form").show();
                                $(".title-payment").show();
                                $("#payment-processing").addClass("d-none"); 
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({ 
                            title: "Error!",
                            text: "Payment failed. Please try again.",
                            icon: "error",
                            showConfirmButton: true
                        });
                    }
                });
            }, 5000);
        });
    });

    // Send a confirmation email (adjust as needed in order.php)
    function sendEmail(user_name, user_email) {
        $.ajax({
            url: "order.php",
            type: "POST",
            data: {
                user_name: user_name,
                user_email: user_email,
                type: 'send_email'
            },
            success: function(response) {
                console.log("Email response:", response);
            },
            error: function(error) {
                console.error("Email error:", error);
            }
        });
    }
</script>
