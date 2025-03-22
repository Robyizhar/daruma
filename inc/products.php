<?php
    session_start();
    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    include("./sql/db.php");

    $Model = new Model();

    $productsPerPage = 12;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;

    // 1) Collect category
    $category_id = (!empty($_GET['category'])) ? (int)$_GET['category'] : null;

    // 2) Collect search text (for name/edition)
    // Trim to avoid spaces messing up the query
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;

    // 3) Format price filters
    $minPrice_formatted = isset($_GET['min_price']) ? $_GET['min_price'] : '$ 0';
    $maxPrice_formatted = isset($_GET['max_price']) ? $_GET['max_price'] : '$ 5000';

    // Convert the price strings to integers
    $minPrice = isset($_GET['min_price'])
        ? (int) preg_replace("/[^0-9]/", "", $_GET['min_price'])
        : 0;
    $maxPrice = isset($_GET['max_price'])
        ? (int) preg_replace("/[^0-9]/", "", $_GET['max_price'])
        : 5000;

    // Clamp both to 5000
    if ($minPrice > 5000) $minPrice = 5000;
    if ($maxPrice > 5000) $maxPrice = 5000;

    // Ensure minPrice <= maxPrice
    if ($minPrice > $maxPrice) {
        $temp = $minPrice;
        $minPrice = $maxPrice;
        $maxPrice = $temp;
    }

    // 4) Count total products with all filters (including $search)
    $totalProducts = $Model->getTotalProducts($minPrice, $maxPrice, $category_id, $search);
    $totalPages = ceil($totalProducts / $productsPerPage);

    // 5) Retrieve the actual products
    $products = $Model->getProducts($productsPerPage, $offset, $minPrice, $maxPrice, $category_id, $search);
    $categories = $Model->getCategory();
?>

<div class="container">
    <h1 class="text-center">Our Products</h1>
    <p class="text-center">Browse our collection of exclusive items.</p>

    <div class="row">
        <!-- Filter Form -->
        <form method="GET" action="" class="mb-4">
            <div class="row g-3 align-items-end">
                <!-- Category Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="category" class="form-label" style="text-align: left;">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option 
                                value="<?= htmlspecialchars($category['id']) ?>" 
                                <?= ($category['id'] == $category_id) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Min Price (whole number) -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="min_price" class="form-label" style="text-align: left;">Min Price</label>
                    <input 
                        type="text" 
                        class="form-control number-filter" 
                        id="min_price" 
                        name="min_price"
                        value="<?= $minPrice_formatted ?>"
                        oninput="enforcePriceRules()"
                    >
                </div>

                <!-- Max Price (whole number) -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="max_price" class="form-label" style="text-align: left;">Max Price</label>
                    <input 
                        type="text" 
                        class="form-control number-filter" 
                        id="max_price" 
                        name="max_price"
                        value="<?= $maxPrice_formatted ?>"
                        oninput="enforcePriceRules()"
                    >
                </div>

                <!-- Search Bar (Name / Edition) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="search" class="form-label" style="text-align: left;">Search</label>
                    <input 
                        type="text"
                        class="form-control"
                        id="search"
                        name="search"
                        placeholder="Product name or edition"
                        value="<?= htmlspecialchars($search ?? '', ENT_QUOTES) ?>"
                    >
                </div>

                <!-- Submit Button -->
                <div class="col-12 col-sm-12 col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-50 m-0">Apply Filter</button>
                </div>
            </div>
        </form>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $row):
                    $product_id = $row['id'];
                    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                    $edition = htmlspecialchars($row['edition'], ENT_QUOTES, 'UTF-8');
                    $price = number_format($row['price'], 2, '.', ','); 
                    $image = $row['image'] 
                        ? htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') 
                        : 'images/products/default_image.png';
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
                <p>No products available for your search/filter.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <?php 
                // Build the base query string for pagination
                $qs = http_build_query([
                    'min_price' => $minPrice, 
                    'max_price' => $maxPrice,
                    'category'  => $category_id,
                    'search'    => $search
                ]);
            ?>

            <!-- Previous Page -->
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a 
                        class="page-link" 
                        href="?page=<?= $page - 1 ?>&<?= $qs ?>" 
                        aria-label="Previous"
                    >
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a 
                        class="page-link" 
                        href="?page=<?= $i ?>&<?= $qs ?>"
                    >
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next Page -->
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a 
                        class="page-link" 
                        href="?page=<?= $page + 1 ?>&<?= $qs ?>" 
                        aria-label="Next"
                    >
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script>
function enforcePriceRules() {
    const minField = document.getElementById('min_price');
    const maxField = document.getElementById('max_price');

    // Strip out everything except digits
    let minVal = minField.value.replace(/[^\d]/g, '');
    let maxVal = maxField.value.replace(/[^\d]/g, '');

    // Convert to integer
    let minPrice = parseInt(minVal, 10) || 0;
    let maxPrice = parseInt(maxVal, 10) || 0;

    // Clamp both to 5000
    if (minPrice > 5000) minPrice = 5000;
    if (maxPrice > 5000) maxPrice = 5000;

    // Ensure max >= min
    if (maxPrice < minPrice) {
        maxPrice = minPrice;
    }

    // Re-assign to field values, with $ prefix, no decimals
    minField.value = '$ ' + minPrice;
    maxField.value = '$ ' + maxPrice;
}
</script>

<?php 
    include("../inc/design/footer.php"); 
?>
