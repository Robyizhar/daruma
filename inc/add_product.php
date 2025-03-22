<?php
include("./config/config.php");
include("./sql/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = ["success" => false, "message" => '', "data" => null];

    try {
        $name        = isset($_POST["name"]) ? trim($_POST["name"]) : "";
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
        $edition     = isset($_POST["edition"]) ? trim($_POST["edition"]) : "";
        $price       = isset($_POST['price']) 
                        ? filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) 
                        : 0;
        $stock       = isset($_POST["stock"]) ? (int)$_POST["stock"] : 0;
        $category    = isset($_POST["category"]) ? trim($_POST["category"]) : "";
        $imagePath   = null;

        if (!empty($name) && !empty($description) && !empty($edition) 
            && $price >= 0 && !empty($category)) {

            /* Handle file upload */
            if (!empty($_FILES["image"]["name"])) {
                $targetDir = "../images/products/";
                $fileName = time() . "_" . basename($_FILES["image"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

                /* Allowed format */
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
                    $response["message"] = "Invalid file format. Only JPG, JPEG, PNG, webp, and GIF are allowed.";
                    echo json_encode($response);
                    exit;
                }
            }

            $Model = new Model();

            // Add product with the new stock field
            $query = $Model->addProduct($name, $category, $description, $edition, $price, $stock, $imagePath);

            if ($query) {
                $response["success"] = true;
                $response["message"] = "Product added successfully!";
                echo json_encode($response);
            } else {
                $response["message"] = "Error adding product to database.";
                echo json_encode($response);
            }

        } else {
            $response["message"] = "Invalid input. Make sure all fields are filled.";
            echo json_encode($response);
        }
    } catch (\Throwable $th) {
        $response["message"] = $th->getMessage();
        echo json_encode($response);
    }
}
?>
