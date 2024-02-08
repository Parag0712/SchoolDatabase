<?php
include './db/db.php';
// Function to fetch class data by ID
function getClassById($conn, $userId)
{
    // Sanitize class ID
    $userId = (int)$userId;

    // Query to fetch class data
    $sql = "SELECT * FROM `tb_wallet` WHERE `user_id` = $userId";
    $result = $conn->query($sql);

    // If class exists
    if ($result && $result->num_rows > 0) {
        $wallet = $result->fetch_assoc();
        return ["message" => "success", "Wallet" => $wallet];
    } else {
        // Class not found
        return ["message" => "User Not Found"];
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if class_id is provided in the request parameters
    if (isset($_GET['user_id'])) {
        // Get class ID from request parameters
        $userId = $_GET['user_id'];

        // Fetch class data by ID
        $response = getClassById($conn, $userId);

        // Send response
        echo json_encode($response);
    } else {
        // Missing class_id in request
        echo json_encode(["message" => "Missing user_id in request"]);
    }
}

$conn->close();
