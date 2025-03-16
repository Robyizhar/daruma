<?php

class Model {
    private $conn;

    public function __construct() {
        $config = [
            'db_host' => 'localhost',
            'db_user' => 'root',
            'db_pass' => '',
            'db_name' => 'daruma_db',
        ];
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
        if ($conn->connect_error) 
            die("Koneksi gagal: " . $conn->connect_error);
    
        $this->conn = $conn;
    }

    public function addToCart($user_id, $product_id, $quantity) {
        /* Check if the product is already in the user's cart */
        $stmt = $this->conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            /* If it already exists, update quantity and added_at */
            $stmt->close();
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity + ?, added_at = NOW() WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        } else {
            /* If not, enter new data */
            $stmt->close();
            $stmt = $this->conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return $this->getCartByUser($user_id);
        } else {
            return false;
        }
    }

    public function getCartByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT c.id, c.product_id, p.name, p.price, c.quantity, c.added_at 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $cartItems = [];
    
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    
        $stmt->close();
        return $cartItems;
    }

    public function addOrders($request) {
        try {
            $carts = $this->getCartByUser($request['user_id']);

            $user_id = $request['user_id'];
            $received_name = $request['name'];
            $shipping_address = $request['shipping_address'];
            $cc_number = $request['cc_number'];
            $phone_number = $request['phone_number'];

            $total_price = 0;
            foreach ($carts as $item) {
                $total_price += $item['price'] * $item['quantity'];
            }

            $order_stmt = $this->conn->prepare("INSERT INTO orders (user_id, received_name, shipping_address, phone_number, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
            $order_stmt->bind_param("isssd", $user_id, $name, $shipping_address, $phone, $total_price );

            $order_stmt->execute();
            $order_id = $order_stmt->insert_id;
            $order_stmt->close();

            $order_item_stmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)");

            foreach ($carts as $item) {
                $order_item_stmt->bind_param("iisdi", $order_id, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
                $order_item_stmt->execute();
            }
            $order_item_stmt->close();

            // Clear user's cart
            $delete_stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            $this->conn->close();

            return ["success" => true, "message" => "Payment successful!"];
        } catch (\Throwable $th) {
            $this->conn->close();
            return ["success" => false, "message" => $th->getMessage(), "error" => $th];
        }
    }

    public function getOrders() {
        $query = "SELECT id, received_name, shipping_address, phone_number, total_price, status, created_at 
            FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at 
        DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        if ($stmt === false)
            die("MySQL prepare error: " . $this->conn->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $fname, $hashed_password, $role);
        $stmt->fetch();

        $user = ($user_id) ? ["id" => $user_id, "name" => $fname, "password" => $hashed_password, "role" => $role] : null;
        $stmt->close();
        return $user;
    }

    public function registerUser($fname, $lname, $email, $password) {
        $full_name = trim($fname . ' ' . $lname);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            return ["success" => false, "errors" => ["Database error: " . $this->conn->error]];
        }
    
        $stmt->bind_param("sss", $full_name, $email, $hashed_password);
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "errors" => []];
        } else {
            $stmt->close();
            return ["success" => false, "errors" => ($stmt->errno == 1062) ? ["Email already exists."] : ["Error: " . $stmt->error]];
        }
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, edition, price, description, image FROM products WHERE id = ?");
        if (!$stmt) {
            return null;
        }
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /* Get the total number of products based on price range */
    public function getTotalProductsByPrice($minPrice, $maxPrice) {
        $stmt = $this->conn->prepare("SELECT COUNT(id) AS total FROM products WHERE price BETWEEN ? AND ?");
        $stmt->bind_param("ii", $minPrice, $maxPrice);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    /* Get products based on price filters and pagination */
    public function getProductsByPrice($minPrice, $maxPrice, $limit, $offset) {
        $stmt = $this->conn->prepare("SELECT id, name, edition, price, image FROM products WHERE price BETWEEN ? AND ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("iiii", $minPrice, $maxPrice, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    /* Get total products for pagination */
    public function getTotalProducts() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM products");
        return $result->fetch_assoc()['total'];
    }

    /* Get product list with pagination */
    public function getProducts($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    /* Add product */
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

    /* Edit produk */
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

    /* Delete product */
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
