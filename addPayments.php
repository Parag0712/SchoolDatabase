<?php
include './db/db2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have authentication and authorization checks before accessing this endpoint

    $data = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    $requiredFields = ['user_id', 'exam_id', 'payment_method', 'amount_paid'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400); // Bad request
            echo json_encode(["message" => "Missing or empty required field: $field"]);
            exit();
        }
    }

    // Sanitize input data
    $userId = mysqli_real_escape_string($conn, $data['user_id']);
    $examId = mysqli_real_escape_string($conn, $data['exam_id']);
    $paymentMethod = mysqli_real_escape_string($conn, $data['payment_method']);
    $amountPaid = mysqli_real_escape_string($conn, $data['amount_paid']);

    // Construct the insert query
    $insertQuery = "INSERT INTO `exam_payments` (`user_id`, `exam_id`, `payment_method`, `amount_paid`) 
                    VALUES ('$userId', '$examId', '$paymentMethod', '$amountPaid')";

    // Execute the query
    if ($conn->query($insertQuery) === TRUE) {
        // Query executed successfully
        $insertedId = $conn->insert_id;
        $response = ["message" => "success"];
        http_response_code(201); // Created
    } else {
        // Error executing query
        $response = ["message" => "Error inserting exam payment: " . $conn->error];
        http_response_code(500); // Internal Server Error
    }

    // Send response
    echo json_encode($response);
} else {
    // Method not allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Method not allowed"]);
}
?>