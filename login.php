<?php
include 'config.php';
session_start();

if (isset($_SESSION['loggedin'])) {
    header("location: home.php");
    exit;
}

$error = '';
$student_id = $password = '';
$login_attempts = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['login_attempts'])) {
        $login_attempts = $_SESSION['login_attempts'];
    }

    if (empty(trim($_POST["student_id"]))) {
        $error = "Please enter Student ID.";
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    if (empty(trim($_POST["password"]))) {
        $error = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($error)) {
        $sql = "SELECT user_id, student_id, password, role FROM users WHERE student_id = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_student_id);
            $param_student_id = $student_id;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $student_id, $hashed_password, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $id;
                            $_SESSION["student_id"] = $student_id;
                            $_SESSION["role"] = $role;

                            // if role is admin, redirect to dashboard
                            if ($_SESSION["role"] == 'admin') {
                                header("location: admin/");
                            } else {
                                header("location: home.php");
                            }
                        } else {
                            $error = "Invalid Student ID or Password.";
                            $login_attempts++;
                            $_SESSION['login_attempts'] = $login_attempts;
                        }
                    }
                } else {
                    $error = "Invalid Student ID or Password.";
                    $login_attempts++;
                    $_SESSION['login_attempts'] = $login_attempts;
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);

            // Check if login attempts exceed the limit
            if ($login_attempts >= 3) {
                // Redirect to register
                header("location: register.php");
                exit;
            }

            // Update login attempts in session
            $_SESSION['login_attempts'] = $login_attempts;
        }

        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Login - Informatics Library Management System</title>

    <link rel="stylesheet" href="index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 g-4">
                <h1>Welcome to the Informatics Library Management System</h1>
                <p>Please Login first to the system</p>
            </div>
            <div class="col-md-6">
            <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="POST" autocomplete="off">
                    <?php
                    if (!empty($error)) {
                    ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="mb-3">
                        <label for="student_id" class="form-label fw-bold">Username</label>
                        <small id="student_id" class="form-text text-muted">Use your Student ID as Username</small>
                        <input type="text" class="form-control" id="student_id" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <a href="register.php" class="float-end">Register Account</a>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
</body>

</html>