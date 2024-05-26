<?php
include 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// get user information from database, get it from get url otherwise the session
$student_id = $_GET['id'] ?? $_SESSION['student_id'];
$sql = "SELECT * FROM users WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// if no profile picture, show only placeholder
if ($user['image_user'] == '') {
    $user['image_user'] = 'cat.png';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Profile - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <!-- Profile information on left column, profile picture on right column use cards -->
            <div class="col">
                <div class="card">
                <h3 class="card-header"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></h3>
                    <div class="card-body">
                        <p class="card-text">Student ID: <?php echo $user['student_id']; ?></p>
                        <p class="card-text">Grade Level: <?php echo $user['grade_level']; ?></p>
                        <p class="card-text">Role: <?php echo $user['role']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <img src="uploads/<?php echo $user['image_user']; ?>" class="img-fluid" alt="Profile Picture" width="300px">
            </div>
        </div>
</body>

</html>