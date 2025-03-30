<?php
session_start(); // Start the session

// Include database configuration
require_once 'config.php';

// Get the search term from the AJAX request
$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (!empty($term)) {
    // Add wildcards to the search term
    $searchTerm = '%' . $term . '%';

    // Fetch matching locations
    $sql_locations = "SELECT DISTINCT location FROM jobs WHERE location LIKE ? LIMIT 5";
    $stmt_locations = $conn->prepare($sql_locations);
    if ($stmt_locations) {
        $stmt_locations->bind_param('s', $searchTerm); // Use the variable here
        $stmt_locations->execute();
        $result_locations = $stmt_locations->get_result();

        // Fetch matching keywords (job titles)
        $sql_keywords = "SELECT DISTINCT title FROM jobs WHERE title LIKE ? LIMIT 5";
        $stmt_keywords = $conn->prepare($sql_keywords);
        if ($stmt_keywords) {
            $stmt_keywords->bind_param('s', $searchTerm); // Use the variable here
            $stmt_keywords->execute();
            $result_keywords = $stmt_keywords->get_result();

            // Combine results
            $suggestions = [];
            while ($row = $result_locations->fetch_assoc()) {
                $suggestions[] = $row['location'];
            }
            while ($row = $result_keywords->fetch_assoc()) {
                $suggestions[] = $row['title'];
            }

            // Return suggestions as JSON
            header('Content-Type: application/json');
            echo json_encode($suggestions);
            exit(); // Stop further execution
        } else {
            // Handle errors gracefully
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error preparing keywords statement']);
            exit();
        }
    } else {
        // Handle errors gracefully
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error preparing locations statement']);
        exit();
    }
} else {
    // Handle empty term
    header('Content-Type: application/json');
    echo json_encode([]); // Return an empty array
    exit();
}?>