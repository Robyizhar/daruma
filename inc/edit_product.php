<?php
    include("./config/config.php");
    include("./sql/db.php");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        try {
            $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
            $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
            $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
            $edition = isset($_POST["edition"]) ? trim($_POST["edition"]) : "";
            $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
            $imagePath = null;
        
            $response["success"] = false;
            $response["message"] = '';
            $response["data"] = null;
        
            if ($id > 0 && !empty($name) && !empty($description) && !empty($edition) && $price > 0) {
                /* Handle file upload */
                if (!empty($_FILES["image"]["name"])) {
                    $targetDir = "../images/products/";
                    $fileName = time() . "_" . basename($_FILES["image"]["name"]); /* timestamp for unique name */
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
                $query = $Model->updateProduct($id, $name, $description, $edition, $price, $imagePath); /* Update produk di database */
        
                if ($query) {
                    $response["success"] = true;
                    $response["message"] = "Product updated successfully!";
                    echo json_encode($response);
                } else {
                    $response["message"] = "Error updating product.";
                    echo json_encode($response);
                }
            } else {
                $response["message"] = "Invalid input.";
                echo json_encode($response);
            }
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
            echo json_encode($response);
        }
    }
    
?>
