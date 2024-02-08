<?php
include './db/db.php';
// Function to fetch class data by ID
function getClassById($conn, $userToken)
{
    // Sanitize class ID
    $userToken = (int)$userToken;

    // Query to fetch class data
    $sql = "SELECT * FROM `tbl_user` WHERE `user_token` = $userToken";
    $result = $conn->query($sql);

    // If class exists
    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        return ["message" => "success", "class" => $userData];
    } else {
        // Class not found
        return ["message" => "user not found"];
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if class_id is provided in the request parameters
    if (isset($_GET['user_token'])) {
        // Get class ID from request parameters
        $userToken = $_GET['user_token'];

        // Fetch class data by ID
        $response = getClassById($conn, $userToken);

        // Send response
        echo json_encode($response);
    } else {
        // Missing class_id in request
        echo json_encode(["message" => "Missing user_token in request"]);
    }
}

$conn->close();
