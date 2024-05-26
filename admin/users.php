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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Users - Informatics College Northgate Library System</title>

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
            <h1 class="text-white text-center py-5">User Management</h1>
        </div>
    </div>
    <div class="container">
        <div class="row py-3">
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <h5 class="card-header text-center">Add User</h5>
                    <div class="card-body text-center">
                        <p class="card-text">Add user to the system.</p>
                        <a href="add_user.php" class="text-white stretched-link">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table showing the users with actions such as delete user -->
        <div class="row py-3">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Student ID</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Grade Level</th>
                            <th scope="col">Role</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?php echo $row['student_id']; ?></td>
                                    <td><?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?></td>
                                    <td><?php echo $row['grade_level']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td>
                                        <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-danger" onclick="return confirmAction();">Delete</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>