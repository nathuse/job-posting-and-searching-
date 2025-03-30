<?php
session_start(); // Start the session

// Redirect to login page if the user is not logged in or is not a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirect to the login page
    exit(); // Stop further execution
}

// Include database configuration
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = htmlspecialchars(trim($_POST['title'])); // Sanitize input
    $description = strip_tags(trim($_POST['description']), '<b><i><u>'); // Allow basic HTML tags
    $location = htmlspecialchars(trim($_POST['location']));
    $contact_email = filter_var(trim($_POST['contact_email']), FILTER_VALIDATE_EMAIL); // Validate email
    $posted_date = date('Y-m-d H:i:s');

    // Validate input lengths
    if (strlen($title) > 100 || strlen($description) > 1000 || strlen($location) > 100) {
        $error = "Input data is too long. Please check the length of your inputs.";
    }

    // Check if all required fields are filled
    if (empty($title) || empty($description) || empty($location) || !$contact_email) {
        $error = "All fields are required and email must be valid.";
    }
// Debug data before insertion
var_dump([
    'title' => $title,
    'description' => $description,
    'location' => $location,
    'contact_email' => $contact_email,
    'posted_date' => $posted_date
]);

// Debug SQL errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Insert job into the database
// Insert job into the database
if (!isset($error)) {
    $sql = $conn->prepare("INSERT INTO jobs (title, description, location, contact_email, posted_date) VALUES (?, ?, ?, ?, ?)");
    
    // Add these debug lines
    if (!$sql) {
        die("Prepare failed: " . $conn->error);
    }
    
    if ($sql) {
        $sql->bind_param("sssss", $title, $description, $location, $contact_email, $posted_date);
        if ($sql->execute()) {
            $success = "Job posted successfully!";
        } else {
            die("Execute failed: " . $sql->error);  // This will show the actual SQL error
        }
        $sql->close();
    } else {
        $error = "Database error. Please try again later.";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Job Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Form Styles */
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        input, textarea {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(255, 77, 77, 0.5);
        }

        button:hover {
            background-color: #ff1a1a;
            transform: scale(1.05);
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Post a Job</h1>

        <!-- Display error or success messages -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- Job Posting Form -->
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Job Title" required>
            <textarea name="description" placeholder="Job Description" required></textarea>
            <input type="text" name="location" placeholder="Location" required>
            <input type="email" name="contact_email" placeholder="Contact Email" required>
            <button type="submit">Post Job</button>
        </form>
    </div>
</body>
</html>