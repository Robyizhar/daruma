<?php 
session_start();
include("../inc/design/head.php"); 
include("../inc/design/header.php"); 
include("../inc/design/nav.php"); 
include("./sql/db.php");

$Model = new Model();
$orders = [];

if (!isset($_SESSION['user_id']) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $user = $Model->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fname'] = $user['name'];
            $_SESSION['femail'] = $email;
            $_SESSION['role'] = $user['role'];
            header("Location: account.php");
            exit();
        } else {
            $error = "Incorrect email or password.";
        }
    }
} else if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    // If it's a non-admin user already logged in, fetch their orders
    $orders = $Model->getOrders($_SESSION['user_id']);
}
?>

<style>
    #order-table-body tr {
        transition: background-color 0.3s ease-in-out;
    }

    #order-table-body tr:hover {
        background-color: rgba(222, 222, 222, 0.5);
    }
</style>

<div class="container">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <h1>Your Account</h1>
        <p>Please log in to view your account details and orders.</p>
        
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="account.php">
                <div class="form-row">
                    <label for="email">Email (Required):</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-row">
                    <label for="password">Password (Required):</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-row">
                    <button type="submit" name="login_submit">Log In</button>
                </div>
            </form>
        </div>
        <p>Don't have an account? <a href="register.php" class="login-link">Register here</a>.</p>
        
    <?php else: ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h1>
        <p>
            <?php if($_SESSION['role'] !== 'admin'): ?>
                <!-- For non-admin users, show this sentence -->
                You are now logged in. Here you can view your account details and order history.
            <?php endif; ?>

            <?php if($_SESSION['role'] === 'admin'): ?>
                <!-- For admins only, show the admin link -->
                You can access the admin page as 
                <a href="<?= base_url('inc/admin.php') ?>">Administrator</a>.
            <?php endif; ?>
        </p>

        <?php if ($_SESSION['role'] !== 'admin'): ?>
            <div class="container mt-5">
                <h2 class="mb-4">Your Orders</h2>
                <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped" id="order-table-body">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Recipient</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $row): ?>
                                    <tr style="cursor: pointer;" onclick="detailOrder(<?= $row['id'] ?>)">
                                        <td class="text-white">#<?= htmlspecialchars($row['id']) ?></td>
                                        <td class="text-white"><?= htmlspecialchars($row['received_name']) ?></td>
                                        <td class="text-white"><?= htmlspecialchars($row['shipping_address']) ?></td>
                                        <td class="text-white"><?= htmlspecialchars($row['phone_number']) ?></td>
                                        <td class="text-white">
                                            $ <?= number_format((float)$row['total_price'], 2, '.', ',') ?>
                                        </td>
                                        <td class="text-white">
                                            <span class="badge bg-<?= $row['status'] == 'pending' 
                                                ? 'warning' 
                                                : ($row['status'] == 'delivered' ? 'success' : 'danger') ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-white"><?= date('M d Y, H:i', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">You don't have any orders yet.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p><a href="logout.php" class="login-link">Log Out</a></p>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-black" id="orderModalLabel">
                    Detail Order #<span id="order-id"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-black">Delivery Information</h6>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Recipient:</strong> <span id="received-name"></span></li>
                    <li class="list-group-item"><strong>Address:</strong> <span id="shipping-address"></span></li>
                    <li class="list-group-item"><strong>No. Cellphone:</strong> <span id="phone-number"></span></li>
                    <li class="list-group-item"><strong>Total Price:</strong> $<span id="total-price"></span></li>
                    <li class="list-group-item"><strong>Status:</strong> <span id="status"></span></li>
                    <li class="list-group-item"><strong>Order Date:</strong> <span id="created-at"></span></li>
                </ul>
                <h6 class="text-black">Ordered Products</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="order-details"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button 
                    type="button" 
                    class="btn btn-secondary" 
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function detailOrder(id){
    $.ajax({
        url: "order.php",
        type: "GET",
        data: { id: id },
        success: function (response) {
            const res = typeof response === 'string' ? JSON.parse(response) : response;

            $("#order-id").text(res.id);
            $("#received-name").text(res.received_name);
            $("#shipping-address").text(res.shipping_address);
            $("#phone-number").text(res.phone_number);

            const formattedTotalPrice = parseFloat(res.total_price).toLocaleString(
                "en-US",
                { minimumFractionDigits: 2, maximumFractionDigits: 2 }
            );
            $("#total-price").text(formattedTotalPrice);
            $("#status").text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
            $("#created-at").text(res.created_at);

            let detailsHtml = "";
            let totalQuantity = 0;
            let totalPrice = 0;

            res.detail.forEach(item => {
                const itemPrice = parseFloat(item.price);
                const itemSubTotal = item.quantity * itemPrice;

                detailsHtml += `
                    <tr>
                        <td>${item.name}</td>
                        <td>$${itemPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}</td>
                        <td>${item.quantity}</td>
                        <td class="text-end">$${itemSubTotal.toLocaleString("en-US", { minimumFractionDigits: 2 })}</td>
                    </tr>
                `;
                totalPrice += itemSubTotal;
                totalQuantity += parseInt(item.quantity);
            });

            detailsHtml += `
                <tr>
                    <td colspan="2">Total</td>
                    <td>${totalQuantity}</td>
                    <td class="text-end">$${totalPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}</td>
                </tr>
            `;
            $("#order-details").html(detailsHtml);
            $("#orderModal").modal("show");
        },
        error: function () {
            Swal.fire({ 
                title: "Error!",
                text: "Failed to retrieve order details.",
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
}
</script>

<?php include("../inc/design/footer.php"); ?>
