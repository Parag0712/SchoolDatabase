<?php
include './db/db.php';

// Function to login a user using email
function loginUser($conn, $data)
{
    // Check for required fields
    if (!isset($data['email']) || !isset($data['password'])) {
        return ["message" => "Missing required field: email or password"];
    }

    // Extract data
    $email = $data['email'];
    $password = $data['password'];

    // Query to fetch user based on email
    $sql = "SELECT * FROM `tbl_user` WHERE `user_email` = '$email'";
    $result = $conn->query($sql);

    // If user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['user_password'])) {
            // Password is correct
            unset($user['user_password']); // Remove password from user data
            return ["message" => "success", "user" => $user];
        } else {
            // Incorrect password
            return ["message" => "Incorrect password"];
        }
    } else {
        // User not found
        return ["message" => "User not found"];
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data
    $data = json_decode(file_get_contents("php://input"), true);


    // Login the user
    $response = loginUser($conn, $data);

    // Send response
    echo json_encode($response);
}

$conn->close();
