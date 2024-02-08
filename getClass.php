<?php
// Include the database connection file
include './db/db.php';

// Function to fetch all class data
function getAllClasses($conn)
{
    // Query to fetch all class data
    $sql = "SELECT * FROM `class`";
    $result = $conn->query($sql);

    // Initialize an empty array to store class data
    $classes = [];

    // If there are results, fetch each row and add it to the array
    if ($result && $result->num_rows > 0) {
        while ($classData = $result->fetch_assoc()) {
            $classes[] = $classData;
        }
    }

    // Return the array of class data
    return $classes;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all class data
    $classes = getAllClasses($conn);

    // Send response
    echo json_encode($classes);
}

// Close the database connection
$conn->close();
