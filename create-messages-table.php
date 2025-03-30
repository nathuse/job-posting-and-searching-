<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'job_board');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create messages table
$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    sender_type ENUM('employer', 'worker') NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (application_id) REFERENCES job_applications(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Messages table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
