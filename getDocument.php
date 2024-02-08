<?php
include './db/db.php';
// Function to fetch class data by ID
function getClassById($conn, $classId)
{
    // Sanitize class ID
    $classId = (int)$classId;

    // Query to fetch class data
    $sql = "SELECT * FROM `documents` WHERE `class_id` = $classId";
    $result = $conn->query($sql);

    // If class exists
    if ($result && $result->num_rows > 0) {
        $classData = $result->fetch_assoc();
        return ["message" => "Class found", "class" => $classData];
    } else {
        // Class not found
        return ["message" => "Class not found"];
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if class_id is provided in the request parameters
    if (isset($_GET['class_id'])) {
        // Get class ID from request parameters
        $classId = $_GET['class_id'];

        // Fetch class data by ID
        $response = getClassById($conn, $classId);

        // Send response
        echo json_encode($response);
    } else {
        // Missing class_id in request
        echo json_encode(["message" => "Missing class_id in request"]);
    }
}

$conn->close();
