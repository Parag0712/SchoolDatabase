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
    $required_fields = ['username', 'contact', 'class', 'biodata'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            return ["message" => "Missing required field: $field"];
        }
    }

    // Check for existing contact
    $userId = $_GET['user_id'];
    $newContact = $data['contact'];

    // Query to check if contact exists for another user
    $checkContactQuery = "SELECT * FROM `tbl_user` WHERE `user_contact` = '$newContact' AND `user_id` != '$userId'";
    $contactResult = $conn->query($checkContactQuery);

    if ($contactResult->num_rows > 0) {
        // Contact already exists for another user
        return ["message" => "Contact already exists"];
    }

    // Construct the update query based on the fields provided in $data
    $updateQuery = "UPDATE `tbl_user` SET 
                `user_name` = '{$data['username']}', 
                `user_contact` = '{$data['contact']}', 
                `class` = '{$data['class']}', 
                `bio_data` = '{$data['biodata']}' 
                WHERE `user_id` = '$userId'";

    if ($conn->query($updateQuery) === TRUE) {
        // Fetch updated user data
        $select_sql = "SELECT * FROM `tbl_user` WHERE `user_id` = '$userId'";
        $result = $conn->query($select_sql);
        $user_data = $result->fetch_assoc();

        return ["message" => "User updated successfully", "user" => $user_data];
    } else {
        return ["message" => "Error updating user: " . $conn->error];
    }
}
?>