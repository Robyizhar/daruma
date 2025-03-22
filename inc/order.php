<?php
session_start();
include("./sql/db.php");
include("./config/send_email.php");
$Model = new Model();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $order = $Model->getOrderById($_GET['id'], true);
    echo json_encode($order);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if ($type == 'update_status') {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        $order = $Model->updateStatusOrder($order_id, $status);
        if ($order) {
            echo json_encode(["success" => true, "message" => "Order updated", "data" => $order]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input", "error" => $data]);
        }
    } else if ($type == 'store_order') {
        $response = $Model->addOrders($_POST);
        if ($response) {
            echo json_encode(["success" => true, "message" => "Payment successful!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input", "error" => $response]);
        }
    } else if ($type == 'send_email') {
        $email = $_POST['user_email'];
        $name = $_POST['user_name'];
        $subject = "Your Order Has Been Confirmed";
        $body = "<h3>Hello $name,</h3><p>Your order is being processed. Thank you for shopping at our store!</p>";
        // Send email
        echo sendEmail($email, $name, $subject, $body);
    }
}





