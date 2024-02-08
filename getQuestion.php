<?php 
include './db/db.php';

// Function to fetch questions by class_id and exam_id
function getQuestionsByClassAndExam($conn, $classId, $examId)
{
    // Construct the SQL query
    $sql = "SELECT * FROM `question` WHERE class_id = '$classId' AND exam_id = '$examId'";

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
    // Check if class_id and exam_id are provided in the request parameters
    if (isset($_GET['class_id']) && isset($_GET['exam_id'])) {
        // Get class_id and exam_id from request parameters
        $classId = $_GET['class_id'];
        $examId = $_GET['exam_id'];

        // Fetch questions by class_id and exam_id
        $questions = getQuestionsByClassAndExam($conn, $classId, $examId);

        // Send response
        echo json_encode($questions);
    } else {
        // Missing class_id or exam_id in request
        echo json_encode(["message" => "Missing class_id or exam_id in request"]);
    }
}

// Close the database connection
$conn->close();

?>
