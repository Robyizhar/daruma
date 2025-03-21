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
            die("Connection failed: " . $conn->connect_error);
    
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

    public function deleteCartById($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id); 
        $result = $stmt->execute();
        $stmt->close();

        if ($result)
            return true;
        
        return false;

    }

    public function updateCartById($id, $user_id, $quantity) {
        // Query update quantity di tabel cart dengan filter user_id
        $stmt = $this->conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return true;
        } 

        return false;

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

            $order_stmt = $this->conn->prepare("INSERT INTO orders (user_id, received_name, shipping_address, phone_number, total_price, cc_number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            $order_stmt->bind_param("isssds", $user_id, $received_name, $shipping_address, $phone_number, $total_price, $cc_number);

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

            return true;
        } catch (\Throwable $th) {
            $this->conn->close();
            return false;
        }
    }

    public function getOrders($user_id = null) {

        if ($user_id != null) {
            $query = "SELECT id, received_name, shipping_address, phone_number, total_price, status, created_at 
                FROM orders 
                WHERE user_id = ? 
                ORDER BY created_at 
            DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
        } else {
            $query = "SELECT id, received_name, shipping_address, phone_number, total_price, status, created_at 
                FROM orders 
                ORDER BY created_at 
            DESC LIMIT 20";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
    
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    
        $stmt->close();
        return $orders;
    }

    public function getOrderById($id, $relation = false) {
        $query = "SELECT id, received_name, shipping_address, phone_number, total_price, status, created_at 
            FROM orders 
            WHERE id = ? 
            ORDER BY created_at 
        DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if ($relation) {
            
            $query2 = "SELECT id, order_id, product_id, quantity, price, name 
                FROM order_items
                WHERE order_id = ? 
                ORDER BY id 
            DESC";

            $stmt = $this->conn->prepare($query2);
            $stmt->bind_param("i", $id);

            $stmt->execute();
            $result = $stmt->get_result();
            $orders = [];
    
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            $order['detail'] = $orders;
        }

        $stmt->close();
        return $order;
    }

    public function updateStatusOrder($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
        $this->conn->close();
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

    /*
    |--------------------------------------------------------------------------
    |Query Product and Category
    |--------------------------------------------------------------------------
    */

    public function getCategory() {
        $query = "SELECT * FROM categories  ORDER BY id ASC LIMIT 20";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();
        $response = [];

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
    
        $stmt->close();
        return $response;
    }

    private function productStatement() {
        return  "SELECT 
            products.id, 
            products.name, 
            products.edition,
            products.price,
            products.description,
            products.image,
            categories.id AS category_id,
            categories.name AS category_name
        FROM products
        JOIN categories ON categories.id = products.category_id ";
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare( $this->productStatement() . "WHERE products.id = ?");
        if (!$stmt) {
            return null;
        }
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    public function getProductByCategoryId($id) {
        $stmt = $this->conn->prepare( $this->productStatement() . "WHERE categories.id = ?");
        if (!$stmt) {
            return null;
        }
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /* Get total number of products (with optional price range) */
    public function getTotalProducts($minPrice = null, $maxPrice = null, $category = null) {
        $query = "SELECT COUNT(products.id) AS total FROM products 
                  JOIN categories ON categories.id = products.category_id";
        $params = [];
        $types = "";
        $conditions = [];

        if ($minPrice !== null && $maxPrice !== null) {
            $conditions[] = "products.price BETWEEN ? AND ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
            $types .= "ii";
        }

        if ($category !== null) {
            $conditions[] = "products.category_id = ?";
            $params[] = $category;
            $types .= "i";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result['total'] ?? 0;
    }

    /* Get products with optional price filter and pagination */
    public function getProducts($limit, $offset, $minPrice = null, $maxPrice = null, $category = null) {
        $query = $this->productStatement();
        $params = [];
        $types = "";
        $conditions = [];
    
        if ($minPrice !== null && $maxPrice !== null) {
            $conditions[] = "products.price BETWEEN ? AND ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
            $types .= "ii";
        }
    
        if ($category !== null) {
            $conditions[] = "products.category_id = ?";
            $params[] = $category;
            $types .= "i";
        }
    
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $query .= " ORDER BY products.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
    
        error_log("Query: " . $query);
        error_log("Params: " . json_encode($params));
        error_log("Types: " . $types);
    
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
    
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $response = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
        return $response;
    }
    

    /* Add product */
    public function addProduct($name, $category, $description, $edition, $price, $image = null) {
        if ($image != null) {
            $query = "INSERT INTO products (name, category_id, description, edition, price, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sissds", $name, $category, $description, $edition, $price, $image);
        } else {
            $query = "INSERT INTO products (name, description, edition, price) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sissd", $name, $category, $description, $edition, $price);
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

    /*
    |--------------------------------------------------------------------------
    |Query Product
    |--------------------------------------------------------------------------
    */
}
?>
