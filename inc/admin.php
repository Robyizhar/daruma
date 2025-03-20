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
    if ($page_type == 'orders') {
        $orders = $Model->getOrders();
    }

    /* Pagination */
    $productsPerPage = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;
    $totalProducts = $Model->getTotalProducts();
    $totalPages = ceil($totalProducts / $productsPerPage);
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
    $current_page = basename($_SERVER['PHP_SELF']);

    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 

?>

<div class="container-admin">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a class="<?= ($page_type == 'product' || $page_type == '') ? 'bg-dark' : ''; ?>" href="<?= base_url('inc/admin.php?list=product') ?>">List Product</a>
        <a class="<?= ($page_type == 'orders') ? 'bg-dark' : ''; ?>" href="<?= base_url('inc/admin.php?list=orders') ?>">List Orders</a>
    </div>

    <!-- Main Content -->
    <?php if($page_type == 'orders'): ?>
    <div class="content">
        <div class="container mt-5">
            <h2 class="mb-4">My Order List</h2>
            <?php if (count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                    <td class="text-white">Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                                    <td class="text-white">
                                        <?php
                                            $status_colors = [
                                                "pending" => "warning",
                                                "shipped" => "primary",
                                                "delivered" => "success",
                                                "canceled" => "danger",
                                                "processing" => "info"
                                            ];

                                            $badge_color = $status_colors[$row['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badge_color ?>">
                                            <?= htmlspecialchars(ucwords($row['status'])) ?>
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
    </div>
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
                    <th class="text-white">Edition</th>
                    <th class="text-white">Description</th>
                    <th class="text-white">Price</th>
                    <th class="text-white">Image</th>
                    <th class="text-white" width="15%">Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                <?php foreach ($result as $row): ?>
                    <tr data-id="<?= $row['id'] ?>">
                        <td class="text-white"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-white"><?= htmlspecialchars($row['edition']) ?></td>
                        <td class="text-white"><?= htmlspecialchars($row['description']) ?></td>
                        <td class="text-white">$<?= number_format($row['price'], 2) ?></td>
                        <td class="text-white"><img src="<?= base_url(htmlspecialchars($row['image'])) ?>" width="50"></td>
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
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

</div>

<div class="modal fade" id="add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <input type="text" class="form-control mb-2" id="addName" name="name" placeholder="Product Name" required>
                    <input type="text" class="form-control mb-2" id="addEdition" name="edition" placeholder="Product Edition" required>
                    <textarea class="form-control mb-2" id="addDescription" name="description" placeholder="Product Description" required></textarea>
                    <input type="number" class="form-control mb-2" id="addPrice" name="price" placeholder="Price" required>
                    <input type="file" class="form-control mb-2" id="addImage" name="image" required>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveAdd">Save</button>
            </div>
        </div>
    </div>
</div>

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
                    <input type="text" class="form-control mb-2" id="editEdition" name="edition" placeholder="Product Edition" required>
                    <textarea class="form-control mb-2" id="editDescription" name="description" placeholder="Product Description" required></textarea>
                    <input type="text" class="form-control mb-2 number-filter" id="editPrice" name="price" placeholder="Price" required>
                    <label>Current Image:</label>
                    <img id="editImagePreview" src="" width="100" class="mb-2">
                    <input type="file" class="form-control mb-2" id="editImage" name="image">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEdit">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-black" id="orderModalLabel">Detail Pesanan #<span id="order-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-black">Delivery Information</h6>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Recipient:</strong> <span id="received-name"></span></li>
                    <li class="list-group-item"><strong>Address:</strong> <span id="shipping-address"></span></li>
                    <li class="list-group-item"><strong>No. Cellphone:</strong> <span id="phone-number"></span></li>
                    <li class="list-group-item"><strong>Total Price:</strong> Rp <span id="total-price"></span></li>
                    <li class="list-group-item"><strong>Status:</strong> <span id="status"></span></li>
                    <li class="list-group-item"><strong>Order Date:</strong> <span id="created-at"></span></li>
                    <li class="list-group-item">
                        <strong>Update Status:</strong><p class="text-danger">Select status to update order status</p>
                        <select class="form-select" id="change-status" data-order="" aria-label="Default select example"></select>
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
                    <tbody id="order-details">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {

        /* Add Product (Open Modal) */
        $(".add-btn").click(function () {
            $("#add-modal").modal("show");
        });

        /* Add Product */
        $("#saveAdd").click(function () {
            let formData = new FormData($("#addProductForm")[0]);
            $.ajax({
                url: "add_product.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,

                success: function (response) {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        $("#add-modal").removeClass("show");
                        Swal.fire({ 
                            title: "Success!", text: res.message, icon: "success", timer: 2000,   showConfirmButton: true
                        }).then(() => {
                            $("#add-modal").modal("hide");
                            location.reload();  
                        });
                    } else {
                        Swal.fire({ 
                            title: "Failed!", text: res.message, icon: "warning", timer: 2000,   showConfirmButton: true
                        }).then(() => {
                            $("#add-modal").modal("hide");
                            location.reload();
                        });
                    }
                },
                error: function () {
                    Swal.fire({ 
                        title: "Error!", text: "Failed to Add product.", icon: "error", confirmButtonText: "OK"
                    });
                }
            });
        });

        /* Delete Product */
        $(".delete-btn").click(function () {
            let id = $(this).closest("tr").data("id");
            if (confirm("Are you sure?")) {
                $.post("admin.php", { delete_product: 1, id: id }, function () {
                    location.reload();
                });
            }
        });

        /* Edit Product (Open Modal) */
        $(".edit-btn").click(function () {
            let row = $(this).closest("tr");
            $("#editId").val(row.data("id"));
            $("#editName").val(row.find("td:eq(0)").text());
            $("#editEdition").val(row.find("td:eq(1)").text());
            $("#editDescription").val(row.find("td:eq(2)").text());
            $("#editPrice").val(row.find("td:eq(3)").text().replace("$", ""));
            let imageUrl = row.find("img").attr("src");
            $("#editImagePreview").attr("src", imageUrl);
            $("#edit-modal").modal("show");
        });

        $("#saveEdit").click(function () {
            let formData = new FormData($("#editProductForm")[0]);
            $.ajax({
                url: "edit_product.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        $("#edit-modal").removeClass("show");
                        Swal.fire({ 
                            title: "Success!", text: res.message, icon: "success", timer: 2000, showConfirmButton: true
                        }).then(() => {
                            $("#edit-modal").modal("hide");
                            location.reload();  
                        });
                    } else {
                        Swal.fire({ 
                            title: "Failed!", text: res.message, icon: "warning", timer: 2000,   showConfirmButton: true
                        }).then(() => {
                            $("#edit-modal").modal("hide");
                            location.reload();  
                        });
                    }
                },
                error: function () {
                    Swal.fire({ 
                        title: "Error!", text: "Failed to update product.", icon: "error", confirmButtonText: "OK"
                    });
                }
            });
        });

        $(document).on('change', '#change-status', function (params) {
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
                    console.log('response', response);
                    
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        Swal.fire({ 
                            title: "Success!", text: 'Status updated successfully!', icon: "success", timer: 2000, showConfirmButton: true
                        }).then(() => {
                            $("#orderModal").modal("hide"); 
                            location.reload();
                        });
                    } else {
                        Swal.fire({ 
                            title: "Warning!", text: 'Status updated Failed!', icon: "warning", timer: 2000, showConfirmButton: true
                        }).then(() => {
                            $("#orderModal").modal("hide"); 
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({ 
                        title: "Error!", text: 'Something went wrong!', icon: "Error", timer: 2000, showConfirmButton: true
                    }).then(() => {
                        $("#orderModal").modal("hide"); 
                    });
                }
            });
        })

    });

    function detailOrder(id){
        
        $.ajax({
            url: "order.php",
            type: "GET",
            data: {id: id},
            success: function (response) {
                $('#change-status').empty();
                $("#change-status").attr('data-order', id);
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                $("#order-id").text(res.id);
                $("#received-name").text(res.received_name);
                $("#shipping-address").text(res.shipping_address);
                $("#phone-number").text(res.phone_number);
                $("#total-price").text(parseFloat(res.total_price).toLocaleString("id-ID"));
                $("#status").text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
                $("#created-at").text(res.created_at);

                let detailsHtml = "";
                let totalQuantity = 0;
                let totalPrice = 0;
                const status = ['pending','processing','shipped','delivered','canceled'];
                
                status.forEach(stat => {
                    $('#change-status').append(`<option ${ res.status === stat ? 'selected' : ''} value="${stat}">${stat}</option>`);
                });
                res.detail.forEach(item => {
                    detailsHtml += `
                        <tr>
                            <td>${item.name}</td>
                            <td>$ ${parseFloat(item.price).toLocaleString("id-ID")}</td>
                            <td>${item.quantity}</td>
                            <td class="text-end">$ ${(item.quantity * parseFloat(item.price)).toLocaleString("en-US")}</td>
                        </tr>
                    `;
                    totalPrice += parseFloat(item.price) * item.quantity;
                    totalQuantity += parseInt(item.quantity);
                });

                detailsHtml += `
                    <tr>
                        <td colspan="2">Total</td>
                        <td>${totalQuantity}</td>
                        <td class="text-end">$ ${totalPrice.toLocaleString("en-US")}</td>
                    </tr>
                `;

                $("#order-details").html(detailsHtml);
                $("#orderModal").modal("show");
            },
            error: function () {
                Swal.fire({ 
                    title: "Error!", text: "Failed to update product.", icon: "error", confirmButtonText: "OK"
                });
            }
        });
    }
</script>
<?php include("../inc/design/footer.php"); ?>