<?php
include './db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Assuming you have authentication and authorization checks before accessing this endpoint

    // Get the wallet ID from the URL parameters
    $walletId = isset($_GET['wallet_id']) ? $_GET['wallet_id'] : null;

    // Check if wallet ID is provided
    if (!$walletId) {
        http_response_code(400); // Bad request
        echo json_encode(["message" => "Wallet ID is required"]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the wallet exists
    $walletExistQuery = "SELECT * FROM `tb_wallet` WHERE `id` = '$walletId'";
    $walletResult = $conn->query($walletExistQuery);

    if ($walletResult->num_rows > 0) {
        $response = updateWallet($conn, $data, $walletId);
    } else {
        // Wallet does not exist
        $response = ["message" => "Wallet not found"];
    }

    // Send response
    echo json_encode($response);
}

// Function to update wallet data
function updateWallet($conn, $data, $walletId)
{
    $requiredFields = ['amount']; // Add other required fields here if needed

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            return ["message" => "Missing required field: $field"];
        }
    }

    // Sanitize input data
    $amount = mysqli_real_escape_string($conn, $data['amount']);

    // Construct the update query
    $updateQuery = "UPDATE `tb_wallet` SET `amount` = '$amount' WHERE `id` = '$walletId'";

    // Execute the query
    if ($conn->query($updateQuery) === TRUE) {
        // Query executed successfully
        return ["message" => "Success"];
    } else {
        // Error executing query
        return ["message" => "Error updating wallet: " . $conn->error];
    }
}
?>
