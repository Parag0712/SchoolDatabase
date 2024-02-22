<?php 
include './db/db.php';

// Function to fetch questions by exam_id
function getQuestionsByExam($conn, $examId)
{
    // Construct the SQL query
    $sql = "SELECT * FROM `question` WHERE exam_id = '$examId'";

    // Execute the query
    $result = $conn->query($sql);

    // Initialize an empty array to store question data
    $questions = [];

    // If there are results, fetch each row and add it to the array
    if ($result && $result->num_rows > 0) {
        while ($questionData = $result->fetch_assoc()) {
            $questions[] = $questionData;
        }
    }

    // Return the array of question data
    return $questions;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if exam_id is provided in the request parameters
    if (isset($_GET['exam_id'])) {
        // Get exam_id from request parameters
        $examId = $_GET['exam_id'];

        // Fetch questions by exam_id
        $questions = getQuestionsByExam($conn, $examId);

        // Send response
        echo json_encode($questions);
    } else {
        // Missing exam_id in request
        echo json_encode(["message" => "Missing exam_id in request"]);
    }
}

// Close the database connection
$conn->close();
?>
