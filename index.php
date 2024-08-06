<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';
require 'navbar.php'; // Include the navbar

// Retrieve counts from the database
$studentQuery = "SELECT COUNT(*) AS count FROM student";
$studentResult = mysqli_query($conn, $studentQuery);
$studentCount = mysqli_fetch_assoc($studentResult)['count'];

$userQuery = "SELECT COUNT(*) AS count FROM user";
$userResult = mysqli_query($conn, $userQuery);
$userCount = mysqli_fetch_assoc($userResult)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Load Font Awesome -->
</head>
<body>
<div class="container mt-4">
    <h1>Dashboard</h1>
    <div class="row">
        <!-- Student Count Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mr-3"></i> <!-- Font Awesome Icon -->
                    <div>
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text"><?php echo htmlspecialchars($studentCount); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- User Count Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-users fa-3x text-success mr-3"></i> <!-- Font Awesome Icon -->
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"><?php echo htmlspecialchars($userCount); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Additional metric cards can be added here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
