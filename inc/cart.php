<?php
    session_start();
    include("./sql/db.php");
    $Model = new Model();

    /* Insert To Cart */
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_SESSION['user_id'];

        $insert = $Model->addToCart($user_id, $product_id, $quantity);
    
        if ($insert) {
            echo json_encode(["success" => true, "message" => "Product successfully added to cart!", "data" => $insert]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add to cart!"]);
        }
        exit;
    }
    /* Insert To Cart */

    if (!isset($_SESSION['user_id'])) {
        header("Location: account.php");
        exit;
    }

    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    include("../inc/design/footer.php");


?>