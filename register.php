<?php
include './db/db.php';

// Function to sanitize data
function sanitizeData($conn, $data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = sanitizeData($conn, $value);
            } else {
                $data[$key] = mysqli_real_escape_string($conn, $value);
            }
        }
    } else {
        $data = mysqli_real_escape_string($conn, $data);
    }
    return $data;
}

// Function to register a user
function registerUser($conn, $data)
{
    // Sanitize data
    $sanitized_data = sanitizeData($conn, $data);

    // Check for required fields
    $required_fields = ['username', 'contact', 'email', 'class', 'password','userToken'];
    foreach ($required_fields as $field) {
        if (!isset($sanitized_data[$field])) {
            return ["message" => "Missing required field: $field"];
        }
    }

    // Set default values if not provided
    $userStatus = isset($sanitized_data['userStatus']) ? $sanitized_data['userStatus'] : 1;
    $profileImage = isset($sanitized_data['imgUrl']) ? $sanitized_data['imgUrl'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';

    // Extract sanitized data
    $userName = $sanitized_data['username'];
    $userContact = $sanitized_data['contact'];
    $userEmail = $sanitized_data['email'];
    $class = $sanitized_data['class'];
    $user_token = $sanitized_data['userToken'];
    $userPassword = password_hash($sanitized_data['password'], PASSWORD_DEFAULT);

    // Generate random user view password
    $userViewPassword = generateRandomPassword();

    // Initialize bio_data to empty string
    $bioData = "";

    // Insert data into database
    $sql = "INSERT INTO `tbl_user` (`profile_image`, `user_name`, `user_contact`, `user_email`, `class`, `user_status`, `user_password`, `user_view_password`, `bio_data`,`user_token`) 
            VALUES ('$profileImage', '$userName', '$userContact', '$userEmail', '$class', $userStatus, '$userPassword', '$userViewPassword','$bioData' ,'$user_token')";

    if ($conn->query($sql) === TRUE) {
        // Fetch inserted user data
        $user_id = $conn->insert_id;
        $select_sql = "SELECT `user_id`, `profile_image`, `user_name`, `user_contact`, `user_email`, `class`, `user_status`, `user_view_password`, `bio_data`,`user_token` FROM `tbl_user` WHERE `user_id` = '$user_id'";
        $result = $conn->query($select_sql);
        $user_data = $result->fetch_assoc();

        return ["message" => "Success", "user" => $user_data];
    } else {
        return ["message" => "Error: " . $sql . "<br>" . $conn->error];
    }
}

// Function to generate a random password
function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    // Register the user
    $response = registerUser($conn, $data);

    // Send response
    echo json_encode($response);
}

$conn->close();
