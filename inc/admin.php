<?php
session_start();
include("../inc/design/head.php"); 
include("../inc/design/header.php"); 
include("../inc/design/nav.php"); 

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: account.php");
    exit();
}

// Konfigurasi Pagination
$productsPerPage = 10; // Ubah jumlah produk per halaman sesuai kebutuhan
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;

// Ambil total jumlah produk untuk pagination
$totalProductsQuery = $conn->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $totalProductsQuery->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $productsPerPage);

// Ambil data produk sesuai halaman
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT $productsPerPage OFFSET $offset");

?>

<div class="container mt-4">
    <h2 class="text-center">Manage Products</h2>
    
    <!-- Tambah Produk -->
    <form id="addProductForm" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="name" placeholder="Product Name" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="price" placeholder="Price" step="0.01" required>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="image" placeholder="Image URL" required>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-success w-100">Add</button>
            </div>
        </div>
    </form>

    <!-- Daftar Produk -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-white">Name</th>
                <th class="text-white">Price</th>
                <th class="text-white">Image</th>
                <th class="text-white" width="15%">Actions</th>
            </tr>
        </thead>
        <tbody id="productTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-id="<?= $row['id'] ?>">
                    <td class="text-white"><?= htmlspecialchars($row['name']) ?></td>
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

<!-- Modal Edit Produk -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <input type="text" class="form-control mb-2" id="editName" placeholder="Product Name">
                <input type="number" class="form-control mb-2" id="editPrice" placeholder="Price">
                <input type="text" class="form-control mb-2" id="editImage" placeholder="Image URL">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEdit">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Tambah Produk
        $("#addProductForm").submit(function (e) {
            e.preventDefault();
            $.post("admin.php", $(this).serialize() + "&add_product=1", function () {
                location.reload();
            });
        });

        // Hapus Produk
        $(".delete-btn").click(function () {
            let id = $(this).closest("tr").data("id");
            if (confirm("Are you sure?")) {
                $.post("admin.php", { delete_product: 1, id: id }, function () {
                    location.reload();
                });
            }
        });

        // Edit Produk (Buka Modal)
        $(".edit-btn").click(function () {
            let row = $(this).closest("tr");
            $("#editId").val(row.data("id"));
            $("#editName").val(row.find("td:eq(0)").text());
            $("#editPrice").val(row.find("td:eq(1)").text().replace("$", ""));
            $("#editImage").val(row.find("img").attr("src"));
            $("#editModal").modal("show");
        });

        // Simpan Edit Produk
        $("#saveEdit").click(function () {
            $.post("admin.php", {
                edit_product: 1,
                id: $("#editId").val(),
                name: $("#editName").val(),
                price: $("#editPrice").val(),
                image: $("#editImage").val()
            }, function () {
                location.reload();
            });
        });
    });
</script>
