<?php
include './db/db.php';
// Function to fetch class data by ID
function getUserData($conn, $userToken)
{
    // Sanitize class ID
    // if (!is_numeric($userToken) || $userToken <= 0) {
    //     return ["message" => "Invalid user token"];
    // }

    $userToken = $userToken;
    // Query to fetch class data
    $sql = "SELECT * FROM `tbl_user` WHERE `user_token` = '$userToken'";
    $result = $conn->query($sql);

    // If class exists
    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        return ["message" => "success", "class" => $userData];
    } else {
        // Class not found
        return ["message" => "notfound"];
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if class_id is provided in the request parameters
    if (isset($_GET['user_token'])) {
        // Get class ID from request parameters
        $userToken = $_GET['user_token'];

        // Fetch class data by ID
        $response = getUserData($conn, $userToken);

        // Send response
        echo json_encode($response);
    } else {
        // Missing class_id in request
        echo json_encode(["message" => "Missing user_token in request"]);
    }
}

$conn->close();
