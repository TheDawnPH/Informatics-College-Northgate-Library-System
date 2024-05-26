<?php
$home_dir = '..';
include $home_dir . '/config.php';
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if the user is an admin, if not redirect to home page
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 'admin') {
    header("location: ../home.php");
    exit;
}

// Check if reservation_id and user_id are set in the URL
if (isset($_GET['reserve_id']) && isset($_GET['user_id'])) {
    $reservation_id = $_GET['reserve_id'];
    $user_id = $_GET['user_id'];

    // Use prepared statement to delete reservation from the table
    $sql = "DELETE FROM reservation WHERE reservation_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $reservation_id, $user_id);
        if ($stmt->execute()) {
            //header("location: /admin/index.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Error: Missing reservation_id or user_id";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script>
        alert("Reservation has been deleted.");
        window.location.href = "index.php";
    </script>
</head>
</html>
