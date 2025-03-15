<?php

class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get the total number of products based on price range
    public function getTotalProductsByPrice($minPrice, $maxPrice) {
        $stmt = $this->conn->prepare("SELECT COUNT(id) AS total FROM products WHERE price BETWEEN ? AND ?");
        $stmt->bind_param("ii", $minPrice, $maxPrice);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    // Get products based on price filters and pagination
    public function getProductsByPrice($minPrice, $maxPrice, $limit, $offset) {
        $stmt = $this->conn->prepare("SELECT id, name, edition, price, image FROM products WHERE price BETWEEN ? AND ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("iiii", $minPrice, $maxPrice, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }
    

    // Get total products for pagination
    public function getTotalProducts() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM products");
        return $result->fetch_assoc()['total'];
    }

    // Get product list with pagination
    public function getProducts($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Add product
    public function addProduct($name, $description, $edition, $price, $image = null) {
        if ($image != null) {
            $query = "INSERT INTO products (name, description, edition, price, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssds", $name, $description, $edition, $price, $image);
        } else {
            $query = "INSERT INTO products (name, description, edition, price) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssd", $name, $description, $edition, $price);
        }

        if ($stmt->execute())
            return true;
        
        return false;
    }

    // Edit produk
    public function updateProduct($id, $name, $description, $edition, $price, $image = null) {
        if ($image != null) {
            $query = "UPDATE products SET name=?, description=?, edition=?, price=?, image=? WHERE id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssdsi", $name, $description, $edition, $price, $image, $id);
        } else {
            $query = "UPDATE products SET name=?, description=?, edition=?, price=? WHERE id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssdi", $name, $description, $edition, $price, $id);
        }

        if ($stmt->execute())
            return true;
        
        return false;
    }

    // Delete product
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
