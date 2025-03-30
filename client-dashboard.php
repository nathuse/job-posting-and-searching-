<?php
session_start(); // Start the session

// Redirect to login page if the user is not logged in or is not a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php');
    exit();
}

// Set a default username if not found
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - Job Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        .nav-links a {
            text-decoration: none;
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            display: block;
            transition: 0.3s;
        }
        .nav-links a:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .employer-btn {
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin: 10px 0;
    transition: background-color 0.3s ease;
    font-weight: 500;
}

.employer-btn:hover {
    background-color: #218838;
    text-decoration: none;
    color: white;
}

.employer-btn i {
    margin-right: 8px;
}
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>(Client Dashboard)</p>
        <div class="nav-links">
            <a href="post-job.html">Post a Job</a>
            <a href="employer-dashboard.html" class="dashboard-btn employer-btn">
    <i class="fas fa-building"></i> Employer Dashboard
</a>

            <a href="logout.php">Logout</a>
        </div>
        <div class="footer">&copy; 2025 Job Board. All rights reserved.</div>
    </div>
</body>
</html>
