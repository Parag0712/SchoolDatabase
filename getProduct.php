<?php
// Include the database connection file
include './db/db.php';

// Function to fetch all class data
function getProduct($conn)
{
    $sql = "SELECT * FROM `product`";
    $result = $conn->query($sql);

    // Initialize an empty array to store class data
    $product = [];

    // If there are results, fetch each row and add it to the array
    if ($result && $result->num_rows > 0) {
        while ($productData = $result->fetch_assoc()) {
            $product[] = $productData;
        }
    }

    // Return the array of class data
    return $product;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all class data
    $product = getProduct($conn);

    // Send response
    echo json_encode($product);
}

// Close the database connection
$conn->close();
