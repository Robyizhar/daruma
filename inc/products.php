<?php 
    include("../inc/design/head.php");
    include("../inc/design/header.php");
    include("../inc/design/nav.php");
    include("./sql/db.php");
    session_start();
    
    $Model = new Model();

    $productsPerPage = 12;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;

    $minPrice_formatted = isset($_GET['min_price']) ? $_GET['min_price'] : '$ 0';
    $maxPrice_formatted = isset($_GET['max_price']) ? $_GET['max_price'] : '$ 1.000.000.000';
    $category_id = isset($_GET['category']) ? $_GET['category'] : null;
    
    /* Make sure the price input is only numbers */
    $minPrice = isset($_GET['min_price']) ? (int) preg_replace("/[^0-9]/", "", $_GET['min_price']) : 0;
    $maxPrice = isset($_GET['max_price']) ? (int) preg_replace("/[^0-9]/", "", $_GET['max_price']) : 10000000;
    
    /* Make sure minPrice is not greater than maxPrice */
    if ($minPrice > $maxPrice) {
        $temp = $minPrice;
        $minPrice = $maxPrice;
        $maxPrice = $temp;
    }
    
    /* Get total products based on price range */
    $totalProducts = $Model->getTotalProducts($minPrice, $maxPrice, $category_id);
    $totalPages = ceil($totalProducts / $productsPerPage);
    
    /* Retrieve product data based on price filter */
    $products = $Model->getProducts($productsPerPage, $offset, $minPrice, $maxPrice, $category_id);

    $categories = $Model->getCategory();
?>

<div class="container">
    <h1 class="text-center">Our Products</h1>
    <p class="text-center">Browse our collection of exclusive items.</p>

    <div class="row">
        <!-- Filter Form -->
        <form method="GET" action="" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-12 col-md-3">
                    <label for="category" class="form-label" style="text-align: left;">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>" 
                                <?= ($category['id'] == $category_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-3">
                    <label for="min_price" class="form-label" style="text-align: left;">Min Price</label>
                    <input type="text" class="form-control number-filter" id="min_price" name="min_price" value="<?= $minPrice_formatted ?>">
                </div>
                <div class="col-12 col-sm-12 col-md-3">
                    <label for="max_price" class="form-label" style="text-align: left;">Max Price</label>
                    <input type="text" class="form-control number-filter" id="max_price" name="max_price" value="<?= $maxPrice_formatted ?>">
                </div>
                <div class="col-12 col-sm-12 col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-50 m-0">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $row): 
                    $product_id = $row['id'];
                    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                    $edition = htmlspecialchars($row['edition'], ENT_QUOTES, 'UTF-8');
                    $price = number_format($row['price'], 2);
                    $image = $row['image'] ? htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') : 'images/products/default_image.png';
                    $product_url = "product.php?id=" . $product_id;
                ?>
                    <div class="product">
                        <a href="<?= $product_url ?>">
                            <img 
                                src="<?= base_url($image) ?>" 
                                onerror="this.onerror=null; this.src='<?= base_url('images/products/default_image.png') ?>';" 
                                alt="<?= $name ?>"
                            >
                        </a>
                        <h3><a href="<?= $product_url ?>"><?= $name ?></a></h3>
                        <p class="product-edition"><?= $edition ?></p>
                        <p>$<?= $price ?></p>
                        <a href="<?= $product_url ?>" class="buy-now">View Product</a>
                    </div>
                <?php endforeach; ?>
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
    include("../inc/design/footer.php"); 
?>
