<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log the raw POST data for debugging
error_log('POST data: ' . print_r($data, true));

// Validate input
if (!isset($data['jobId']) || !isset($data['applicantName']) || !isset($data['applicantEmail']) || !isset($data['applicationMessage'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Connect to database (update with your credentials)
$conn = new mysqli('localhost', 'username', 'password', 'job_board');

// Check connection
if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Sanitize inputs
$jobId = intval($data['jobId']);
$applicantName = $conn->real_escape_string($data['applicantName']);
$applicantEmail = $conn->real_escape_string($data['applicantEmail']);
$applicationMessage = $conn->real_escape_string($data['applicationMessage']);

// Insert application into database
$sql = "INSERT INTO job_applications (job_id, applicant_name, applicant_email, message, application_date, status) 
        VALUES ($jobId, '$applicantName', '$applicantEmail', '$applicationMessage', NOW(), 'pending')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
} else {
    error_log('Database error: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>