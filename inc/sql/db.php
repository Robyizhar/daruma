<?php

/**
 * db.php
 * Contains the Model class and database connection logic.
 */

class Model {
    private $conn;

    public function __construct() {
        // Database configuration
        // $config = [
        //     'db_host' => 'localhost',
        //     'db_user' => 'inf1005-sqldev',
        //     'db_pass' => 'r2Qr3YjS',
        //     'db_name' => 'daruma_db',
        // ];
        $config = [
            'db_host' => 'localhost',
            'db_user' => 'root',
            'db_pass' => '',
            'db_name' => 'daruma_db',
        ];
        
        // Connect to database
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
        if ($conn->connect_error) 
            die("Connection failed: " . $conn->connect_error);
    
        $this->conn = $conn;
    }

    // -----------------------------------------------------------------------
    // CART-RELATED METHODS
    // -----------------------------------------------------------------------

    public function addToCart($user_id, $product_id, $quantity) {
        // Check if product is already in cart
        $stmt = $this->conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update quantity
            $stmt->close();
            $stmt = $this->conn->prepare(
                "UPDATE cart SET quantity = quantity + ?, added_at = NOW()
                 WHERE user_id = ? AND product_id = ?"
            );
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        } else {
            // Insert a new row
            $stmt->close();
            $stmt = $this->conn->prepare(
                "INSERT INTO cart (user_id, product_id, quantity, added_at)
                 VALUES (?, ?, ?, NOW())"
            );
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? $this->getCartByUser($user_id) : false;
    }

    public function deleteCartById($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateCartById($id, $user_id, $quantity) {
        $stmt = $this->conn->prepare(
            "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("iii", $quantity, $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getCartByUser($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT cart.id, cart.product_id, products.name, products.price,
                    products.stock, cart.quantity, cart.added_at
             FROM cart
             JOIN products ON cart.product_id = products.id
             WHERE cart.user_id = ?"
        );
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

    // -----------------------------------------------------------------------
    // ORDER-RELATED METHODS
    // -----------------------------------------------------------------------

    public function addOrders($request) {
        try {
            $carts = $this->getCartByUser($request['user_id']);

            $user_id          = $request['user_id'];
            $received_name    = $request['name'];
            $shipping_address = $request['shipping_address'];
            $cc_number        = $request['cc_number'];
            $phone_number     = $request['phone_number'];

            // Sum total price from cart
            $total_price = 0;
            foreach ($carts as $item) {
                $total_price += $item['price'] * $item['quantity'];
            }

            // Insert to orders
            // If you need SG time, do date_default_timezone_set("Asia/Singapore") above
            $order_stmt = $this->conn->prepare(
                "INSERT INTO orders (
                    user_id, received_name, shipping_address,
                    phone_number, total_price, cc_number, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())"
            );
            $order_stmt->bind_param("isssds",
                $user_id,
                $received_name,
                $shipping_address,
                $phone_number,
                $total_price,
                $cc_number
            );
            $order_stmt->execute();
            $order_id = $order_stmt->insert_id;
            $order_stmt->close();

            // Insert order_items & reduce stock
            $order_item_stmt   = $this->conn->prepare(
                "INSERT INTO order_items (
                    order_id, product_id, name, price, quantity
                ) VALUES (?, ?, ?, ?, ?)"
            );
            $update_stock_stmt = $this->conn->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ?"
            );

            foreach ($carts as $item) {
                $order_item_stmt->bind_param(
                    "iisdi",
                    $order_id,
                    $item['product_id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity']
                );
                $order_item_stmt->execute();

                // Decrement stock
                $update_stock_stmt->bind_param("ii",
                    $item['quantity'],
                    $item['product_id']
                );
                $update_stock_stmt->execute();
            }
            $order_item_stmt->close();
            $update_stock_stmt->close();

            // Clear cart
            $delete_stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Close connection
            $this->conn->close();
            return true;
        } catch (\Throwable $th) {
            $this->conn->close();
            return false;
        }
    }

    public function getOrders($user_id = null) {
        if ($user_id !== null) {
            $query = "SELECT id, received_name, shipping_address, phone_number,
                             total_price, status, created_at
                      FROM orders
                      WHERE user_id = ?
                      ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
        } else {
            $query = "SELECT id, received_name, shipping_address, phone_number,
                             total_price, status, created_at
                      FROM orders
                      ORDER BY created_at DESC
                      LIMIT 20";
            $stmt = $this->conn->prepare($query);
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
        $query = "SELECT id, received_name, shipping_address, phone_number,
                         total_price, status, created_at
                  FROM orders
                  WHERE id = ?
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        // If the order exists and $relation = true, fetch order_items
        if ($order && $relation) {
            $stmt2 = $this->conn->prepare(
                "SELECT id, order_id, product_id, quantity, price, name
                 FROM order_items
                 WHERE order_id = ?
                 ORDER BY id DESC"
            );
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $orderDetails = [];
            while ($row2 = $result2->fetch_assoc()) {
                $orderDetails[] = $row2;
            }
            $stmt2->close();
            $order['detail'] = $orderDetails;
        }

        return $order;
    }

    public function updateStatusOrder($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);

        try {
            $stmt->execute();
            $stmt->close();
            $this->conn->close();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $stmt->close();
            $this->conn->close();
            return false;
        }
    }

    // -----------------------------------------------------------------------
    // USER-RELATED METHODS
    // -----------------------------------------------------------------------

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        if (!$stmt) {
            die("MySQL prepare error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $fname, $hashed_password, $role);
        $stmt->fetch();
        $user = $user_id ? [
            "id" => $user_id,
            "name" => $fname,
            "password" => $hashed_password,
            "role" => $role
        ] : null;
        $stmt->close();
        return $user;
    }

    public function registerUser($fname, $lname, $email, $password) {
        $full_name = trim($fname . ' ' . $lname);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            return [
                "success" => false,
                "errors" => ["Database error: " . $this->conn->error]
            ];
        }
        $stmt->bind_param("sss", $full_name, $email, $hashed_password);

        try {
            $stmt->execute();
            $stmt->close();
            return ["success" => true, "errors" => []];
        } catch (\mysqli_sql_exception $e) {
            $stmt->close();
            if ($e->getCode() == 1062) {
                return [
                    "success" => false,
                    "errors" => ["Email already exists."]
                ];
            }
            return [
                "success" => false,
                "errors" => ["Database error: " . $e->getMessage()]
            ];
        }
    }

    // -----------------------------------------------------------------------
    // CATEGORY & PRODUCT-RELATED METHODS
    // -----------------------------------------------------------------------

    public function getCategory() {
        $query = "SELECT * FROM categories ORDER BY id ASC LIMIT 20";
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
        return "SELECT
            products.id,
            products.name,
            products.edition,
            products.price,
            products.description,
            products.image,
            products.stock,
            categories.id AS category_id,
            categories.name AS category_name
        FROM products
        JOIN categories ON categories.id = products.category_id ";
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare($this->productStatement() . "WHERE products.id = ?");
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
        $stmt = $this->conn->prepare($this->productStatement() . "WHERE categories.id = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /**
     * Count total products, with optional price range, category, and search
     */
    public function getTotalProducts($minPrice = null, $maxPrice = null, $category = null, $search = null) {
        $query = "SELECT COUNT(products.id) AS total
                  FROM products
                  JOIN categories ON categories.id = products.category_id";
        $conditions = [];
        $params = [];
        $types = "";

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
        // NEW: If there's a search, search in name OR edition
        if (!empty($search)) {
            $conditions[] = "(products.name LIKE ? OR products.edition LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $types .= "ss";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['total'] ?? 0;
    }

    /**
     * Get products, with optional price range, category, search, limit & offset
     */
    public function getProducts($limit, $offset, $minPrice = null, $maxPrice = null, $category = null, $search = null) {
        $query = $this->productStatement();
        $conditions = [];
        $params = [];
        $types = "";

        // Price range
        if ($minPrice !== null && $maxPrice !== null) {
            $conditions[] = "products.price BETWEEN ? AND ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
            $types .= "ii";
        }
        // Category
        if ($category !== null) {
            $conditions[] = "products.category_id = ?";
            $params[] = $category;
            $types .= "i";
        }
        // Search
        if (!empty($search)) {
            $conditions[] = "(products.name LIKE ? OR products.edition LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $types .= "ss";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY products.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

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

    /**
     * Add product (includes $stock).
     */
    public function addProduct($name, $category, $description, $edition, $price, $stock, $image = null) {
        if ($image !== null) {
            $query = "INSERT INTO products (
                name, category_id, description, edition, price, stock, image, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sissdis",
                $name,
                $category,
                $description,
                $edition,
                $price,
                $stock,
                $image
            );
        } else {
            $query = "INSERT INTO products (
                name, category_id, description, edition, price, stock, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sissdi",
                $name,
                $category,
                $description,
                $edition,
                $price,
                $stock
            );
        }
        return $stmt->execute();
    }

    /**
     * Update product details (includes $stock).
     */
    public function updateProduct($id, $name, $category, $description, $edition, $price, $stock, $image = null) {
        if ($image !== null) {
            $query = "UPDATE products SET
                name = ?,
                category_id = ?,
                description = ?,
                edition = ?,
                price = ?,
                stock = ?,
                image = ?
                WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            // Notice the bind_param string: "sissdi si" -> that has a space in it, 
            // better to keep it as "sissdisi" or separate them carefully
            // We'll do "sissdisi" here for clarity
            $stmt->bind_param("sissdisi",
                $name,
                $category,
                $description,
                $edition,
                $price,
                $stock,
                $image,
                $id
            );
        } else {
            $query = "UPDATE products SET
                name = ?,
                category_id = ?,
                description = ?,
                edition = ?,
                price = ?,
                stock = ?
                WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sissdii",
                $name,
                $category,
                $description,
                $edition,
                $price,
                $stock,
                $id
            );
        }
        return $stmt->execute();
    }

    /**
     * Delete product by ID
     */
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
