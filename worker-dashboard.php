<?php
session_start(); // Start the session

// Redirect to login page if the user is not logged in or is not a worker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: index.php'); // Redirect to the login page
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard - Job Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 1rem;
        }
        .nav-links a {
            display: block;
            text-decoration: none;
            padding: 12px;
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Worker'); ?>!</h1>
        <p>Your job search starts here.</p>
        <div class="nav-links">
            <a href="search-jobs.html"><i class="fas fa-search"></i> Search Jobs</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="footer">&copy; 2025 Job Board. All rights reserved.</div>
    </div>
</body>
</html>
