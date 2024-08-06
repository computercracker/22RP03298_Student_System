<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';
require 'navbar.php'; // Include the navbar

$errors = [];
$student_id = null;
$first_name = $last_name = $dob = $email = $phone = '';
$student = [];

// Check if a student ID is provided and fetch the existing student data
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);

    $sql = "SELECT * FROM student WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $first_name = $student['first_name'];
        $last_name = $student['last_name'];
        $dob = $student['dob'];
        $email = $student['email'];
        $phone = $student['phone'];
    } else {
        die('Error fetching student data');
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['dob'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate input
    if (empty($first_name) || empty($last_name)) {
        $errors[] = "First name and last name are required.";
    } elseif (!preg_match("/^[a-zA-Z-' ]+$/", $first_name) || !preg_match("/^[a-zA-Z-' ]+$/", $last_name)) {
        $errors[] = "Names can only contain letters and whitespace.";
    }

    if (empty($dob)) {
        $errors[] = "Date of birth is required.";
    } else {
        $dob_timestamp = strtotime($dob);
        if (!$dob_timestamp) {
            $errors[] = "Invalid date format.";
        } else {
            $current_timestamp = time();
            $max_age_timestamp = strtotime('-120 years');
            if ($dob_timestamp >= $current_timestamp) {
                $errors[] = "Date of birth cannot be today or in the future.";
            } elseif ($dob_timestamp < $max_age_timestamp) {
                $errors[] = "Date of birth cannot be more than 120 years ago.";
            }
        }
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($phone) && !preg_match('/^07[0-9]{8,10}$/', $phone)) {
        $errors[] = "Phone number must start with '07' and be between 10 to 12 digits long.";
    }

    // Check email uniqueness
    if (empty($errors)) {
        $email_check_sql = "SELECT id FROM student WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($email_check_sql);
        $stmt->bind_param("si", $email, $student_id);
        $stmt->execute();
        $email_check_result = $stmt->get_result();

        if ($email_check_result->num_rows > 0) {
            $errors[] = "Email is already in use.";
        }
        $stmt->close();
    }

    // Perform update if no errors
    if (empty($errors)) {
        $sql_update = "UPDATE student SET first_name = ?, last_name = ?, dob = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssssi", $first_name, $last_name, $dob, $email, $phone, $student_id);

        if ($stmt->execute()) {
            header('Location: view_students.php');
            exit();
        } else {
            $errors[] = 'Failed to update student: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Edit Student</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>">
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($dob); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
