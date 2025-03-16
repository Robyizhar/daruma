<?php
    session_start();
    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    include("./sql/db.php");

    /* Check if the user is logged in as admin */
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: account.php");
        exit();
    }

    $productModel = new Model($conn);

    /* Pagination */
    $productsPerPage = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;
    $totalProducts = $productModel->getTotalProducts();
    $totalPages = ceil($totalProducts / $productsPerPage);
    $result = $productModel->getProducts($productsPerPage, $offset);

    /* Handle product delete requests */
    if (isset($_POST['delete_product'])) {
        $id = (int) $_POST['id'];
        if ($productModel->deleteProduct($id)) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error"]);
        }
        exit();
    }
    $current_page = basename($_SERVER['PHP_SELF']);

?>

<div class="container-admin">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a class="<?= ($current_page == 'admin.php') ? 'bg-dark' : ''; ?>" href="<?= base_url('inc/admin.php') ?>">List Product</a>
        <a class="<?= ($current_page == 'orders.php') ? 'bg-dark' : ''; ?>" href="#">List Orders</a>
    </div>

    <!-- Main Content -->
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
                <?php while ($row = $result->fetch_assoc()): ?>
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
                <?php endwhile; ?>
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
                            timer: 2000,  
                            showConfirmButton: true
                        }).then(() => {
                            $("#add-modal").modal("hide");
                            location.reload();
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
                            timer: 2000,  
                            showConfirmButton: true
                        }).then(() => {
                            $("#edit-modal").modal("hide");
                            location.reload();  
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
    });
</script>
