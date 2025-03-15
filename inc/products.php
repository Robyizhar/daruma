<?php 
    include("../inc/design/head.php");
    include("../inc/design/header.php");
    include("../inc/design/nav.php");

    /* Set default values */
    $productsPerPage = 12; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;
    $minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000;

    /* Query to get total number of products with price filtering */
    $totalStmt = $conn->prepare("SELECT COUNT(id) AS total FROM products WHERE price BETWEEN ? AND ?");
    $totalStmt->bind_param("ii", $minPrice, $maxPrice);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalProducts = $totalRow['total'];
    $totalPages = ceil($totalProducts / $productsPerPage);
    $totalStmt->close();

    /* Query to retrieve products by page with price filtering */
    $stmt = $conn->prepare("SELECT id, name, edition, price, image FROM products WHERE price BETWEEN ? AND ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("iiii", $minPrice, $maxPrice, $productsPerPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="container">
    <h1 class="text-center">Our Products</h1>
    <p class="text-center">Browse our collection of exclusive items.</p>

    <div class="row">
        <!-- Filter Form -->
        <form method="GET" action="" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-12 col-md-4">
                    <label for="min_price" class="form-label" style="text-align: left;">Min Price</label>
                    <input type="text" class="form-control" id="min_price" name="min_price" value="<?= $minPrice ?>" min="0">
                </div>
                <div class="col-12 col-sm-12 col-md-4">
                    <label for="max_price" class="form-label" style="text-align: left;">Max Price</label>
                    <input type="text" class="form-control" id="max_price" name="max_price" value="<?= $maxPrice ?>" min="0">
                </div>
                <div class="col-12 col-sm-12 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-50 m-0">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="products-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $product_id = $row['id'];
                    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                    $edition = htmlspecialchars($row['edition'], ENT_QUOTES, 'UTF-8');
                    $price = number_format($row['price'], 2);
                    $image = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');
                    $product_url = "product.php?id=" . $product_id;
                ?>
                    <div class="product">
                        <a href="<?= $product_url ?>">
                            <img src="<?= base_url($image) ?>" alt="<?= $name ?>">
                        </a>
                        <h3><a href="<?= $product_url ?>"><?= $name ?></a></h3>
                        <p class="product-edition"><?= $edition ?></p>
                        <p>$<?= $price ?></p>
                        <a href="<?= $product_url ?>" class="buy-now">View Product</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available in this price range.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php 
    $stmt->close();
    include("../inc/design/footer.php"); 
?>
