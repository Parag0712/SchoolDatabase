<?php
include './db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

    $data = json_decode(file_get_contents("php://input"), true);
    // Assuming you have a user ID sent in the PATCH request
    $userId = $_GET['user_id'];
    // Check if the user exists
    $userExistOrNot = "SELECT * FROM `tbl_user` WHERE `user_id` = '$userId'";
    $result = $conn->query($userExistOrNot);
    if ($result->num_rows > 0) {
        $response = updateUser($conn, $data);
    } else {
        // User does not exist
        $response = ["message" => "User not found"];
    }

    // Send response
    echo json_encode($response);
}

// Function to update user data
function updateUser($conn, $data)
{
    $required_fields = ['username', 'contact', 'email', 'class', 'imgUrl', 'biodata', 'password'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            return ["message" => "Missing required field: $field"];
        }
    }

    // Check for existing email
    $userId = $_GET['user_id'];
    $newEmail = $data['email'];
    $newContact = $data['contact'];

    $checkEmailQuery = "SELECT * FROM `tbl_user` WHERE `user_email` = '$newEmail' AND `user_id` != '$userId'";
    $result = $conn->query($checkEmailQuery);

    // Query to check if contact exists for another user
    $checkContactQuery = "SELECT * FROM `tbl_user` WHERE `user_contact` = '$newContact' AND `user_id` != '$userId'";
    $contactResult = $conn->query($checkContactQuery);

    if ($result->num_rows > 0) {
        // Email already exists for another user
        return ["message" => "Email already exists"];
    }

    if ($contactResult->num_rows > 0) {
        // Contact already exists for another user
        return ["message" => "Contact already exists"];
    }

    $hashPass = password_hash($data['password'],PASSWORD_DEFAULT);
    // Construct the update query based on the fields provided in $data
    $updateQuery = "UPDATE `tbl_user` SET 
                `profile_image` = '{$data['imgUrl']}', 
                `user_name` = '{$data['username']}', 
                `user_contact` = '{$data['contact']}', 
                `user_email` = '{$data['email']}', 
                `class` = '{$data['class']}', 
                `user_password` = '{$hashPass}', 
                `bio_data` = '{$data['biodata']}' 
                WHERE `user_id` = '$userId'";

    if ($conn->query($updateQuery) === TRUE) {
        // Fetch updated user data
        $select_sql = "SELECT * FROM `tbl_user` WHERE `user_id` = '$userId'";
        $result = $conn->query($select_sql);
        $user_data = $result->fetch_assoc();
        unset($user_data['user_password']);

        return ["message" => "User updated successfully", "user" => $user_data];
    } else {
        return ["message" => "Error updating user: " . $conn->error];
    }
}
