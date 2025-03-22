<?php
session_start();
include("./sql/db.php");

/* Check if the user is logged in as admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: account.php");
    exit();
}

$page_type = isset($_GET['list']) ? $_GET['list'] : '';
$Model = new Model();
$orders = [];

// If admin is viewing the 'orders' page, fetch orders
if ($page_type == 'orders') {
    $orders = $Model->getOrders();
}

/* Pagination */
$productsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;
$totalProducts = $Model->getTotalProducts();
$totalPages = ceil($totalProducts / $productsPerPage);

// Get all products including the stock information
$result = $Model->getProducts($productsPerPage, $offset);

/* Handle product delete requests */
if (isset($_POST['delete_product'])) {
    $id = (int) $_POST['id'];
    if ($Model->deleteProduct($id)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit();
}

$categories = $Model->getCategory();

include("../inc/design/head.php"); 
include("../inc/design/header.php"); 
include("../inc/design/nav.php"); 
?>

<div class="container-admin">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a class="<?= ($page_type == 'product' || $page_type == '') ? 'bg-dark' : ''; ?>" 
           href="<?= base_url('inc/admin.php?list=product') ?>">
           Products
        </a>
        <a class="<?= ($page_type == 'orders') ? 'bg-dark' : ''; ?>" 
           href="<?= base_url('inc/admin.php?list=orders') ?>">
           Orders
        </a>
    </div>

    <!-- Main Content: ORDERS -->
    <?php if($page_type == 'orders'): ?>
        <div class="content">
            <div class="container mt-5">
                <h2 class="mb-4">Order List</h2>
                <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Recipient</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <!-- CHANGED FOR $ AND 2DP -->
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
                                        <!-- CHANGED FOR $ AND 2DP -->
                                        <td class="text-white">
                                            $<?= number_format($row['total_price'], 2) ?>
                                        </td>
                                        <td class="text-white">
                                            <?php
                                                $status_colors = [
                                                    "pending" => "warning",
                                                    "processing" => "info",
                                                    "shipped" => "primary",
                                                    "delivered" => "success",
                                                    "canceled" => "danger",
                                                ];
                                                $badge_color = $status_colors[$row['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badge_color ?>">
                                                <?= htmlspecialchars(ucwords($row['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-white">
                                            <?= date('M d Y, H:i', strtotime($row['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">There are no orders.</div>
                <?php endif; ?>
            </div>
        </div>

    <!-- Main Content: PRODUCTS -->
    <?php else: ?>
        <div class="content">
            <h2 class="text-center">Manage Products</h2>
            
            <div class="row g-3">
                <div class="col-md-1">
                    <button type="button" class="add-btn btn btn-success w-100">Add</button>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-white">Name</th>
                        <th class="text-white">Type</th>
                        <th class="text-white">Edition</th>
                        <th class="text-white">Description</th>
                        <th class="text-white">Price</th>
                        <th class="text-white">Stock</th>
                        <th class="text-white">Image</th>
                        <th class="text-white" width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody id="productTable">
                    <?php foreach ($result as $row): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td class="text-white"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="text-white" data-id="<?= $row['category_id'] ?>">
                                <?= htmlspecialchars($row['category_name']) ?>
                            </td>
                            <td class="text-white"><?= htmlspecialchars($row['edition']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($row['description']) ?></td>
                            <td class="text-white">$<?= number_format($row['price'], 2) ?></td>
                            <td class="text-white"><?= (int)$row['stock'] ?></td>
                            <td class="text-white">
                                <img src="<?= base_url(htmlspecialchars($row['image'])) ?>" 
                                     onerror="this.onerror=null; this.src='<?= base_url('images/products/default_image.png') ?>';" 
                                     width="50">
                            </td>
                            <td class="text-white">
                                <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL: ADD PRODUCT -->
<div class="modal fade" id="add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <input type="text" class="form-control mb-2" name="name" placeholder="Product Name" required>
                    
                    <select class="form-select mb-2" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="text" class="form-control mb-2" name="edition" placeholder="Product Edition" required>
                    <textarea class="form-control mb-2" name="description" placeholder="Product Description" required></textarea>
                    <input type="number" class="form-control mb-2" name="price" step="0.01" placeholder="Price" required>
                    <input type="number" class="form-control mb-2" name="stock" placeholder="Stock" min="0" required>
                    <input type="file" class="form-control mb-2" name="image" required>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveAdd">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDIT PRODUCT -->
<div class="modal fade" id="edit-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="id">
                    
                    <input type="text" class="form-control mb-2" id="editName" name="name" placeholder="Product Name" required>
                    
                    <select class="form-select mb-2" name="category" id="editCategory" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="text" class="form-control mb-2" id="editEdition" name="edition" placeholder="Product Edition" required>
                    <textarea class="form-control mb-2" id="editDescription" name="description" placeholder="Product Description" required></textarea>
                    <input type="number" class="form-control mb-2" id="editPrice" name="price" step="0.01" placeholder="Price" required>
                    <input type="number" class="form-control mb-2" id="editStock" name="stock" min="0" placeholder="Stock" required>
                    
                    <label>Current Image:</label>
                    <img id="editImagePreview" src="" width="100" class="mb-2 d-block">
                    <input type="file" class="form-control mb-2" id="editImage" name="image">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEdit">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ORDER DETAIL -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-black" id="orderModalLabel">
                    Detail Order #<span id="order-id"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-black">Delivery Information</h6>
                <ul class="list-group mb-3">
                    <li class="list-group-item">
                        <strong>Recipient:</strong> <span id="received-name"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Address:</strong> <span id="shipping-address"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>No. Cellphone:</strong> <span id="phone-number"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Total Price:</strong> $ <span id="total-price"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong> <span id="status"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Order Date:</strong> <span id="created-at"></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Update Status:</strong>
                        <p class="text-danger">Select status to update order status</p>
                        <select class="form-select" id="change-status" data-order="" 
                                aria-label="Default select example">
                        </select>
                    </li>
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
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End MODAL -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    /* ADD PRODUCT (Open Modal) */
    $(".add-btn").click(function () {
        $("#add-modal").modal("show");
    });

    /* ADD PRODUCT (Save) */
    $("#saveAdd").click(function () {
        let formData = new FormData($("#addProductForm")[0]);
        $.ajax({
            url: "add_product.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = (typeof response === 'string') ? JSON.parse(response) : response;
                if (res.success) {
                    Swal.fire({ 
                        title: "Success!", 
                        text: res.message, 
                        icon: "success", 
                        timer: 2000, 
                        showConfirmButton: true
                    }).then(() => {
                        $("#add-modal").modal("hide");
                        location.reload();
                    });
                } else {
                    Swal.fire({ 
                        title: "Failed!", 
                        text: res.message, 
                        icon: "warning", 
                        timer: 5000, 
                        showConfirmButton: true
                    });
                }
            },
            error: function () {
                Swal.fire({ 
                    title: "Error!", 
                    text: "Failed to Add product.", 
                    icon: "error", 
                    confirmButtonText: "OK"
                });
            }
        });
    });

    /* DELETE PRODUCT */
    $(".delete-btn").click(function () {
        let id = $(this).closest("tr").data("id");
        if (confirm("Are you sure to delete product ID " + id + "?")) {
            $.post("admin.php", { delete_product: 1, id: id }, function (res) {
                location.reload();
            });
        }
    });

    /* EDIT PRODUCT (Open Modal) */
    $(".edit-btn").click(function () {
        let row = $(this).closest("tr");
        $("#editId").val(row.data("id"));
        $("#editName").val(row.find("td:eq(0)").text());
        $("#editCategory").val(row.find("td:eq(1)").data("id"));
        $("#editEdition").val(row.find("td:eq(2)").text());
        $("#editDescription").val(row.find("td:eq(3)").text());
        $("#editPrice").val(row.find("td:eq(4)").text().replace("$", ""));
        $("#editStock").val(row.find("td:eq(5)").text());

        let imageUrl = row.find("img").attr("src");
        $("#editImagePreview").attr("src", imageUrl);

        $("#edit-modal").modal("show");
    });

    /* EDIT PRODUCT (Save) */
    $("#saveEdit").click(function () {
        let formData = new FormData($("#editProductForm")[0]);
        $.ajax({
            url: "edit_product.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = (typeof response === 'string') ? JSON.parse(response) : response;
                if (res.success) {
                    Swal.fire({ 
                        title: "Success!", 
                        text: res.message, 
                        icon: "success", 
                        timer: 2000, 
                        showConfirmButton: true
                    }).then(() => {
                        $("#edit-modal").modal("hide");
                        location.reload();
                    });
                } else {
                    Swal.fire({ 
                        title: "Failed!", 
                        text: res.message, 
                        icon: "warning", 
                        timer: 5000, 
                        showConfirmButton: true
                    });
                }
            },
            error: function () {
                Swal.fire({ 
                    title: "Error!", 
                    text: "Failed to update product.", 
                    icon: "error", 
                    confirmButtonText: "OK"
                });
            }
        });
    });

    /* CHANGE ORDER STATUS */
    $(document).on('change', '#change-status', function () {
        const selectedValue = $(this).val();
        let orderId = $("#order-id").text();
        $.ajax({
            url: "order.php",
            type: "POST",
            data: { 
                order_id: orderId,
                status: selectedValue, 
                type: 'update_status'
            },
            success: function (response) {
                const res = (typeof response === 'string') ? JSON.parse(response) : response;
                if (res.success) {
                    Swal.fire({ 
                        title: "Success!", 
                        text: 'Status updated successfully!', 
                        icon: "success", 
                        timer: 2000, 
                        showConfirmButton: true
                    }).then(() => {
                        $("#orderModal").modal("hide"); 
                        location.reload();
                    });
                } else {
                    Swal.fire({ 
                        title: "Warning!", 
                        text: 'Status update failed!', 
                        icon: "warning", 
                        timer: 2000, 
                        showConfirmButton: true
                    }).then(() => {
                        $("#orderModal").modal("hide"); 
                    });
                }
            },
            error: function () {
                Swal.fire({ 
                    title: "Error!", 
                    text: 'Something went wrong!', 
                    icon: "error", 
                    timer: 2000, 
                    showConfirmButton: true
                }).then(() => {
                    $("#orderModal").modal("hide"); 
                });
            }
        });
    });
});

/* VIEW ORDER DETAIL (Open Modal) */
function detailOrder(id){
    $.ajax({
        url: "order.php",
        type: "GET",
        data: {id: id},
        success: function (response) {
            $('#change-status').empty();
            $("#change-status").attr('data-order', id);

            const res = (typeof response === 'string') ? JSON.parse(response) : response;
            $("#order-id").text(res.id);
            $("#received-name").text(res.received_name);
            $("#shipping-address").text(res.shipping_address);
            $("#phone-number").text(res.phone_number);

            // Keep the detail currency in $
            const formattedTotal = parseFloat(res.total_price)
                .toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            $("#total-price").text(formattedTotal);

            $("#status").text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
            $("#created-at").text(res.created_at);

            // Populate status dropdown
            const statuses = ['pending','processing','shipped','delivered','canceled'];
            statuses.forEach(stat => {
                $('#change-status').append(`
                    <option ${ res.status === stat ? 'selected' : '' } value="${stat}">
                        ${stat}
                    </option>`);
            });

            // Order items
            let detailsHtml = "";
            let totalQuantity = 0;
            let totalPrice = 0;

            res.detail.forEach(item => {
                const priceNum = parseFloat(item.price);
                const lineTotal = item.quantity * priceNum;

                // Format each item line in $ with 2 decimals
                const formattedItemPrice = priceNum
                    .toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const formattedLineTotal = lineTotal
                    .toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                detailsHtml += `
                    <tr>
                        <td>${item.name}</td>
                        <td>$${formattedItemPrice}</td>
                        <td>${item.quantity}</td>
                        <td class="text-end">$${formattedLineTotal}</td>
                    </tr>
                `;
                totalPrice += lineTotal;
                totalQuantity += parseInt(item.quantity);
            });

            // Final summary row
            const formattedOverall = totalPrice
                .toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            detailsHtml += `
                <tr>
                    <td colspan="2">Total</td>
                    <td>${totalQuantity}</td>
                    <td class="text-end">$${formattedOverall}</td>
                </tr>
            `;
            $("#order-details").html(detailsHtml);

            $("#orderModal").modal("show");
        },
        error: function () {
            Swal.fire({ 
                title: "Error!", 
                text: "Failed to retrieve order data.", 
                icon: "error", 
                confirmButtonText: "OK"
            });
        }
    });
}
</script>

<?php include("../inc/design/footer.php"); ?>
