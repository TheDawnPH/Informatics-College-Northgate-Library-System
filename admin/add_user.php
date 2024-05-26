<?php
$home_dir = '..';
include $home_dir . '/config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// if user is not admin redirect to home page
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != 'admin') {
    header("location: ../home.php");
    exit;
}

$error = $success = '';
$student_id = $first_name = $last_name = $grade_level = $password = $confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    if (empty(trim($_POST["student_id"]))) {
        $errors[] = "Please enter Student ID.";
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    if (empty(trim($_POST["first_name"]))) {
        $errors[] = "Please enter First Name.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }

    if (empty(trim($_POST["last_name"]))) {
        $errors[] = "Please enter Last Name.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }

    if (empty(trim($_POST["grade_level"]))) {
        $errors[] = "Please select Grade Level.";
    } else {
        $grade_level = trim($_POST["grade_level"]);
    }

    if (empty(trim($_POST["password"]))) {
        $errors[] = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $errors[] = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
    }

    if ($password !== $confirm_password) {
        $errors[] = "Password did not match.";
    }

    $image = $_FILES['image_user']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image_user"]["name"]);

    // Check if file already exists
    if (file_exists($target_file)) {
        // Rename to add Unix time after name
        $image = pathinfo($image, PATHINFO_FILENAME) . time() . '.' . pathinfo($image, PATHINFO_EXTENSION);
    }

    $newtarget_file = $target_dir . $image;

    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $errors[] = "Sorry, only JPG, JPEG, PNG files are allowed.";
    }

    if (empty($errors)) {
        if (move_uploaded_file($_FILES["image_user"]["tmp_name"], $newtarget_file)) {
            $sql = "INSERT INTO users (student_id, first_name, last_name, grade_level, password, image_user) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "ssssss", $student_id, $first_name, $last_name, $grade_level, $hashed_password, $image);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "User added successfully! Please login with your credentials.";
                } else {
                    $errors[] = "Error: Could not execute the query. " . mysqli_error($conn);
                }
            } else {
                $errors[] = "Error: Could not prepare the query. " . mysqli_error($conn);
            }
        } else {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    }

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Add Users - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmAction() {
            return confirm("Are you sure to delete this user?");
        }
    </script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-5">Add User</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="post" autocomplete="off" enctype="multipart/form-data">
            <?php
            if (!empty($error)) {
            ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php
            }
            ?>
            <?php
            if (!empty($success)) {
            ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php
            }
            ?>
            <div class="mb-3">
                <label for="student_id" class="form-label fw-bold">Student ID (without dash)*</label>
                <input type="text" class="form-control" id="student_id" name="student_id" required>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label fw-bold">First Name*</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label fw-bold">Last Name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="grade_level" class="form-label fw-bold">Grade Level*</label>
                <select class="form-select" id="grade_level" name="grade_level" required>
                    <option value="11">Grade 11</option>
                    <option value="12">Grade 12</option>
                    <option value="HE">Higher Education</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password*</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label fw-bold">Confirm Password*</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="mb-3">
                <label for="image_user" class="form-label fw-bold">Profile Picture*</label>
                <input type="file" class="form-control" id="image_user" name="image_user" required>
            </div>
            <p class="text-muted">* Required</p>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>

</html>
