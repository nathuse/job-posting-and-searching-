<?php
session_start();
header("Content-Type: application/json");

require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["error" => "Internal server error. Please try again later."]);
    exit();
}

$conn->set_charset("utf8mb4");

$type = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$response = [];

if ($type === 'location' && !empty($search)) {
    // Query to fetch distinct locations from the database
    $stmt = $conn->prepare("SELECT DISTINCT location FROM jobs WHERE location LIKE ? LIMIT 10");
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = htmlspecialchars($row['location']);
    }
} elseif ($type === 'keyword' && !empty($search)) {
    // Query to fetch distinct job titles (keywords) from the database
    $stmt = $conn->prepare("SELECT DISTINCT title FROM jobs WHERE title LIKE ? LIMIT 10");
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = htmlspecialchars($row['title']);
    }
} elseif (isset($_GET['location']) || isset($_GET['keyword'])) {
    // Search for jobs based on location and keyword
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $sql = "SELECT title, description, location, contact_email FROM jobs WHERE 1=1";
    $params = [];
    
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    if (!empty($keyword)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$keyword%";
    }
    if (empty($location) && empty($keyword)) {
        http_response_code(400);
        echo json_encode([
            "error" => "Please enter at least one search criteria"
        ]);
        exit();
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['jobs'][] = $row;
    }
}

echo json_encode($response);
$conn->close();
?>
    