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

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    // delete user data from borrow table
    $sql = "DELETE FROM borrow WHERE user_id = $id";
    if ($conn->query($sql) === TRUE) {
        // delete user from borrow table
        $sql = "DELETE FROM borrow WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;
            if ($stmt->execute()) {
                $stmt->close();
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }

        // delete user from user table
        $sql = "DELETE FROM users WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;
            if ($stmt->execute()) {
                header("location: users.php");
                exit;
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Error deleting record: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script>
            alert("User has been deleted.");
            window.location.href = "users.php";
        </script>
    </head>
</html>