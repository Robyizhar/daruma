<?php
    session_start();
    include("./sql/db.php");
    $Model = new Model();

    /* Redirect if user is not logged in */
    if (!isset($_SESSION['user_id'])) {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            header("Location: account.php");
            exit;
        } 
        echo json_encode([ 
            "data_redirect" => 'account.php',
            "message" => 'You must login first'
        ]);
        exit;
    }

    /* Prevent admin from accessing cart.php */
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            header("Location: account.php");
            exit;
        } 
        echo json_encode([ 
            "data_redirect" => 'admin.php',
            "message" => 'Admins may not add carts'
        ]);
        exit;
    }

    /* Handle POST requests for carts */
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['action'] ?? null;
        $user_id = $_SESSION['user_id'];

        switch ($action) {
            case 'add-cart':
                $product_id = $_POST['product_id'];
                $quantity = $_POST['quantity'];
                $insert = $Model->addToCart($user_id, $product_id, $quantity);
                echo json_encode([
                    "success" => (bool) $insert,
                    "message" => $insert ? "Product successfully added to cart!" : "Failed to add to cart!",
                    "data" => $insert
                ]);
            break;

            case 'remove-cart':
                $deleted = $Model->deleteCartById($_POST['id'], $user_id);
                echo json_encode([
                    "success" => (bool) $deleted,
                    "message" => $deleted ? "Item successfully removed from cart!" : "Failed to remove from cart!",
                    "data" => $deleted
                ]);
            break;

            case 'update-cart':
                $updated = $Model->updateCartById($_POST['id'], $user_id, $_POST['quantity']);
                echo json_encode([
                    "success" => (bool) $updated,
                    "message" => $updated ? "Item successfully updated in cart!" : "Failed to update cart!",
                    "data" => $updated
                ]);
            break;

            default:
                echo json_encode(["success" => false, "message" => "Invalid action!"]);
            break;
        }
        exit;
    }

    $data_carts = $Model->getCartByUser($_SESSION['user_id']);

    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
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
    <h2 class="mb-4">Your Cart</h2>
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
                    <th>Actions</th>
                    </tr>
            </thead>
            <tbody id="cart-table-body">
                <?php 
                    $index = 1; 
                    $all_quantities = 0;
                    $all_prices = 0;
                ?>
                <?php foreach($data_carts as $row): ?>
                    <tr data-id="<?= $row['id'] ?>" style="cursor: pointer;">
                        <td onclick="goToProduct(<?= $row['product_id'] ?>)" class="text-white"><?= $index; ?></td>
                        <td onclick="goToProduct(<?= $row['product_id'] ?>)" class="text-white"><?= htmlspecialchars($row['name']) ?></td>
                        <td onclick="goToProduct(<?= $row['product_id'] ?>)" class="text-white">$ <?= number_format($row['price'], 2) ?></td>
                        <td onclick="goToProduct(<?= $row['product_id'] ?>)" class="text-white"><?= htmlspecialchars($row['added_at']) ?></td>
                        <td class="text-white">
                            <input style="max-width: 100px;" type="number" min="1" max="<?= $row['stock'] ?>" data-id="<?= $row['id'] ?>" disabled class="form-control number-only cart-quantity" value="<?= htmlspecialchars($row['quantity']) ?>">
                        </td>
                        <td onclick="goToProduct(<?= $row['product_id'] ?>)" class="text-white text-end">$ <?= number_format(htmlspecialchars($row['quantity']) *  $row['price'], 2) ?></td>
                        <td class="text-white text-end">
                            <button type="button" data-id="<?= $row['id'] ?>" class="delete-cart btn btn-danger btn-sm" style="max-width: 40px;"> 
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                <?php 
                    $index++;
                    $all_quantities += $row['quantity'];
                    $all_prices += $row['quantity'] * $row['price'];
                ?>
                <?php endforeach; ?>
                <tr class="table-dark">
                    <td class="text-center" colspan="4"><strong>Total</strong></td>
                    <td>
                        <strong id="total-quantity"><?= $all_quantities ?></strong>
                        <input type="hidden" value="<?= $all_quantities ?>" id="total-quantity-hide">
                        <input type="hidden" value="<?= $all_prices ?>" id="total-prices-hide">
                        </td>
                    <td class="text-end">
                        <strong id="total-prices">$ <?= number_format($all_prices, 2) ?></strong>
                    </td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>    
    function goToProduct(productId) {
        window.location.href = `product.php?id=${productId}`;
    }

    $(document).ready(function () {
        $('.delete-cart').click(function (e) { 
            e.preventDefault();

            Swal.fire({
                title: "Are you sure want to remove this item from the cart?",
                showDenyButton: true,
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        id: $(this).data("id"),
                        action: 'remove-cart'
                    };
                    $.ajax({
                        url: "cart.php", 
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({ title: "Success!", text: response.message, icon: "success", timer: 2000, showConfirmButton: true }).then(() => {
                                    window.location.assign(response.data_redirect ? response.data_redirect : "cart.php");
                                });
                            } else {
                                Swal.fire({ title: "Failed!", text: response.message, icon: "warning", timer: 2000,   showConfirmButton: true }).then(() => {
                                    window.location.assign(response.data_redirect ? response.data_redirect : "cart.php");
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({ title: "Failed!", text: xhr.responseText, icon: "error", showConfirmButton: true }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        $(".cart-quantity").on("keyup", function () {
            let id = $(this).data("id");  
            let quantity = $(this).val();     

            if (quantity > 1) {
                $.ajax({
                    url: "cart.php",
                    type: "POST",
                    data: { id: id, quantity: quantity, action: 'update-cart' },
                    success: function (response) {
                        console.log("Update Successfully:", response);
                        // updateTotal();
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({ title: "Failed!", text: xhr.responseText, icon: "error", showConfirmButton: true });
                    }
                });
            }
        });
    });

    function updateTotal() {
        let total = 0;

        $(".cart-quantity").each(function () {
            let row = $(this).closest("tr");
            let quantity = parseInt($(this).val()) || 0;
            let price = parseFloat(row.find("td:nth-child(3)").text().replace("$", "")) || 0;
            
            let subtotal = quantity * price;
            row.find("td:nth-child(6)").text("$ " + subtotal.toFixed(2)); // Update subtotal
            
            total += subtotal;
        });

        console.log('total', total);
        

        $("#total-prices").text("$ " + total.toFixed(2)); // Update total cart
    }

</script>
<?php include("../inc/design/footer.php"); ?>