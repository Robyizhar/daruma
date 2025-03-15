<?php
include("./config/config.php");
include("./sql/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
    $edition = isset($_POST["edition"]) ? trim($_POST["edition"]) : "";
    $price = isset($_POST["price"]) ? floatval($_POST["price"]) : 0;
    $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
    $imagePath = null;

    $response["success"] = false;
    $response["message"] = '';
    $response["data"] = null;

    if (!empty($name) && !empty($description) && !empty($edition) && $price > 0) {
        // Handle file upload jika ada file
        if (!empty($_FILES["image"]["name"])) {
            $targetDir = "../images/products/";
            $fileName = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Hanya izinkan format gambar tertentu
            $allowedTypes = ["jpg", "jpeg", "png", "gif", "webp"];
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imagePath = "images/products/" . $fileName;
                } else {
                    $response["message"] = "Error uploading file.";
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response["message"] = "Invalid file format. Only JPG, JPEG, PNG, webp and GIF are allowed.";
                echo json_encode($response);
                exit;
            }
        }

        $productModel = new ProductModel($conn);
        $query = $productModel->addProduct($name, $description, $edition, $price, $imagePath); /* Insert produk di database */

        if ($query) {
            $response["success"] = true;
            $response["message"] = "Product added successfully!";
            echo json_encode($response);
        } else {
            $response["message"] = "Error adding product.";
            echo json_encode($response);
        }

    } else {
        $response["message"] = $th->getMessage();
        echo json_encode($response);
    }
}
?>
