<?php

include './db/db.php';

// Assuming you have received JSON data in the POST request
$jsonData = file_get_contents('php://input');

// Decode the JSON data into a PHP associative array
$orderData = json_decode($jsonData, true);

// Define the required fields
$requiredFields = ['user_id', 'product_name', 'product_price', 'order_status'];

// Check if all required fields are present
foreach ($requiredFields as $field) {
    if (!isset($orderData[$field])) {
        // Send response with an error message indicating the missing field
        $response = array(
            "error" => "Missing required field: $field"
        );
        echo json_encode($response);
        exit; // Stop execution if a required field is missing
    }
}

// Assuming $conn is your MySQL connection object

// Extracting data from the JSON
$user_id = $orderData['user_id'];
$product_name = $orderData['product_name'];
$product_price = $orderData['product_price'];
$order_status = $orderData['order_status'];

// Insert data into the order_list table
$sql = "INSERT INTO `order_list` (`user_id`, `product_name`, `product_price`, `order_status`, `create_at`) 
        VALUES ('$user_id', '$product_name', '$product_price', '$order_status', CURRENT_TIMESTAMP())";

if ($conn->query($sql) === TRUE) {
    // Retrieve the last inserted order_id
    $order_id = $conn->insert_id;
    
    // Send response with order_id and success message
    $response = array(
        "order_id" => $order_id,
        "message" => "OrderSuccessfully"
    );
    echo json_encode($response);
} else {
    // If there's an error in the SQL execution
    $response = array(
        "error" => "Error: " . $sql . "<br>" . $conn->error
    );
    echo json_encode($response);
}

// Close the MySQL connection
$conn->close();
?>
