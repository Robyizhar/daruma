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
?>

<div class="container">
    <h1>Our Products</h1>
    <p>Browse our collection of exclusive items.</p>

    <div class="products-grid">
        <?php
        // Select product details including the new product_image column
        $query = "SELECT product_id, product_name, edition, price, product_image FROM products";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="product">
                    <a href="product.php?id=<?php echo $row['product_id']; ?>">
                        <!-- Use product_image to build the correct file path in /images/ -->
                        <img src="/images/<?php echo $row['product_image']; ?>"
                             alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </a>
                    <h3>
                        <a href="product.php?id=<?php echo $row['product_id']; ?>">
                            <?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h3>
                    <p class="product-edition"><?php echo htmlspecialchars($row['edition'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>$<?php echo number_format($row['price']); ?></p>
                    <a href="product.php?id=<?php echo $row['product_id']; ?>" class="buy-now">View Product</a>
                </div>
                <?php
            }
        } else {
            echo '<p>No products available.</p>';
        }
        ?>
    </div>
</div>

<?php include("../inc/design/footer.php"); ?>
