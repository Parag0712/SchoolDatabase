<?php
include './db/db.php';
// Function to fetch class data by ID
function getDocuments($conn)
{
    // Query to fetch class data
    $sql = "SELECT * FROM `documents` ";
    $result = $conn->query($sql);


    $documents = [];

    // If there are results, fetch each row and add it to the array
    if ($result && $result->num_rows > 0) {
        while ($documentsData = $result->fetch_assoc()) {
            $documents[] = $documentsData;
        }
    }
    return $documents;
}


// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = getDocuments($conn);
    echo json_encode($response);
}

$conn->close();
