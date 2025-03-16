
**# Project Documentation**

## **1. Introduction**

This document provides an overview of the project, including its purpose, technology stack, and system workflow.

## **2. Tech Stack Requirements**

The project is built using the following technologies:

* **Backend:** PHP 8.1 or later
* **Database:** MySQL 9 or later
* **Dependency Management:** Composer

## **3. System Workflow**

### **3.1 User Flow**

1. **User Registration & Login** : Users can create an account and log in to access the platform.
2. **Order Placement** : Users select products and place an order.
3. **Payment Processing (Dummy)** : Users enter credit card details, but no real transaction occurs.
4. **Order Management** : Admin can update the order status (Pending, Shipped, Delivered, Canceled).
5. **Email Notifications** : Users receive emails for order confirmations and status updates.

### **3.2 Admin Flow**

1. **Product Management** : Admins can add, edit, and remove products.
2. **Order Tracking** : Admins can view and update order statuses.

## **4. System Overview**

### **4.1 Home Page**

* Displays the latest products.

### **4.2 Product Page**

* Displays all available products.
* Includes a filter to sort products by price (low to high).

### **4.3 About Us & Account Page**

* If logged in:
  * Displays user's order history (if any orders exist).
* If not logged in:
  * Displays login and registration forms.

### **4.4 Cart Page (Shopping Cart)**

* If not logged in:
  * Redirects to the account page, prompting the user to log in.
* If logged in:
  * Displays products added to the cart.
  * Provides a "Proceed to Payment" button.

### **4.5 Payment Page**

* Users enter the following details:
  * Recipient name
  * Shipping address
  * Phone number
  * Credit card details (dummy input, no actual transaction)
* If successful:
  * Displays a success alert.
  * Sends an email confirmation to the user.

## **5. Deployment Instructions**

1. Clone the repository: `git clone <repo_url>`
2. Install dependencies: `composer install`
3. Configure database connection in `inc/sql/db.php`:

   ```php
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
   ```
4. Configure base URL in `inc/config/config.php`:

   ```php
   function base_url($path = '') {
       return 'http://your_project_domain.test/' . ltrim($path, '/'); // Change this to match your local or production domain
   }
   ```
5. Configure email settings for SMTP in inc/config/send_email.php

### **6. Table Relationships**

* **`users`** → can have many **`orders`**
* **`orders`** → can have many **`order_items`**
* **`products`** → can appear in many **`order_items`** and **`cart`**
* **`cart`** → contains temporary items before checkout
* **`payments`** → connects payments to orders

---
