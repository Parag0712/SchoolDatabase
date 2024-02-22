<?php
include './db/db.php';

// Function to fetch questions by class_id and exam_id
function getQuestionsByClassAndExam($conn, $userId)
{


    $sql = "SELECT
        exam_data.*,
        COALESCE(exam_payments.amount_paid, 'NotPaid') AS amount_paid,
        CASE WHEN exam_payments.amount_paid = 'paid' THEN 'Paid' ELSE 'NotPaid' END AS payment_status
    FROM
        exam_data
    LEFT JOIN
        exam_payments ON exam_data.exam_id = exam_payments.exam_id AND exam_payments.user_id = '$userId';
    ";

    // Execute the query
    $result = $conn->query($sql);

    // Initialize an empty array to store question data
    $examData = [];

    // If there are results, fetch each row and add it to the array
    if ($result && $result->num_rows > 0) {
        while ($questionData = $result->fetch_assoc()) {
            $examData[] = $questionData;
        }
    }

    // Return the array of question data
    return $examData;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if class_id and exam_id are provided in the request parameters
    if (isset($_GET['user_id'])) {
        // Get class_id and exam_id from request parameters
        $userId = $_GET['user_id'];

        // Fetch questions by class_id and exam_id
        $examData = getQuestionsByClassAndExam($conn, $userId);

        // Send response
        echo json_encode($examData);
    } else {
        // Missing class_id or exam_id in request
        echo json_encode(["message" => "Missing class_id"]);
    }
}
// Close the database connection
$conn->close();
