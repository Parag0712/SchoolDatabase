<?php
include './db/db.php';

// Function to register a user
function registerUser($conn, $data)
{
    // Check if email or user token already exist
    $email = mysqli_real_escape_string($conn, $data['email']);
    $userToken = mysqli_real_escape_string($conn, $data['userToken']);
    $checkSql = "SELECT * FROM `tbl_user` WHERE `user_email` = '$email' OR `user_token` = '$userToken'";
    $result = $conn->query($checkSql);
    if ($result->num_rows > 0) {
        return ["message" => "User with this email or token already exists"];
    }

    // Check for required fields
    $requiredFields = ['username', 'contact', 'email', 'class', 'password', 'userToken'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            return ["message" => "Missing required field: $field"];
        }
    }

    // Set default values if not provided
    $userStatus = isset($data['userStatus']) ? $data['userStatus'] : 1;
    $profileImage = isset($data['imgUrl']) ? $data['imgUrl'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';

    // Extract sanitized data
    $userName = mysqli_real_escape_string($conn, $data['username']);
    $userContact = mysqli_real_escape_string($conn, $data['contact']);
    $userEmail = mysqli_real_escape_string($conn, $data['email']);
    $class = mysqli_real_escape_string($conn, $data['class']);
    $userToken = mysqli_real_escape_string($conn, $data['userToken']);
    $userPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Generate random user view password
    $userViewPassword = generateRandomPassword();

    // Initialize bio_data to empty string
    $bioData = "";

    // Insert data into database
    $sql = "INSERT INTO `tbl_user` (`profile_image`, `user_name`, `user_contact`, `user_email`, `class`, `user_status`, `user_password`, `user_view_password`, `bio_data`, `user_token`) 
            VALUES ('$profileImage', '$userName', '$userContact', '$userEmail', '$class', $userStatus, '$userPassword', '$userViewPassword', '$bioData', '$userToken')";

    if ($conn->query($sql) === TRUE) {
        // Fetch inserted user data
        $userId = $conn->insert_id;
        $selectSql = "SELECT `user_id`, `profile_image`, `user_name`, `user_contact`, `user_email`, `class`, `user_status`, `user_view_password`, `bio_data`, `user_token` FROM `tbl_user` WHERE `user_id` = '$userId'";
        $result = $conn->query($selectSql);
        $userData = $result->fetch_assoc();

        $sql2 = "INSERT INTO `tb_wallet` (`user_id`, `amount`) VALUES ($userId, '0')";
        $conn->query($sql2);
        return ["message" => "Success", "user" => $userData];
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
?>
