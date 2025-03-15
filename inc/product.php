<?php include("../inc/design/head.php"); ?>
<?php include("../inc/design/header.php"); ?>
<?php include("../inc/design/nav.php"); ?>

<?php
// Hardcoded MySQL connection details
$host = "localhost";
$user = "inf1005-sqldev";
$pass = "r2Qr3YjS";
$dbname = "daruma_db";

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get product ID from the query string and sanitize it
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Query the product details based on the provided product_id
$query = "SELECT product_id, product_name, edition, price, product_desc, product_image FROM products WHERE product_id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    echo "<p>Product not found.</p>";
    exit;
}
?>

<div class="container">
    <div class="product-description" style="overflow:auto; margin: 20px 0;">
        <!-- Left column: Product image -->
        <div class="product-image" style="float: left; width: 40%; text-align: center;">
            <!-- Since the images haven't been added yet, we use a placeholder -->
            <img src="/images/.png" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width:100%;">
        </div>
        <!-- Right column: Product details and add to cart button -->
        <div class="product-info" style="float: right; width: 55%; text-align: left;">
            <h2><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="edition"><?php echo htmlspecialchars($product['edition'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="price">$<?php echo number_format($product['price']); ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($product['product_desc'], ENT_QUOTES, 'UTF-8')); ?></p>
            <button class="add-to-cart">Add to Cart</button>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>

<?php include("../inc/design/footer.php"); ?>
