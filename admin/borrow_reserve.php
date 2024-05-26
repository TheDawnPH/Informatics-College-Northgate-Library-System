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

// Check if reservation_id and user_id are set
if (!isset($_GET['reserve_id']) || !isset($_GET['user_id'])) {
    echo "Error: reservation_id or user_id not set.";
    exit;
}

$reservation_id = $_GET['reserve_id'];
$user_id = $_GET['user_id'];

// Sanitize input to prevent SQL injection
$reservation_id = $conn->real_escape_string($reservation_id);
$user_id = $conn->real_escape_string($user_id);

// Start transaction
$conn->begin_transaction();

try {
    // Fetch the book_id from reservation
    $sql = "SELECT book_id FROM reservation WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("No reservation found with the provided ID.");
    }

    $row = $result->fetch_assoc();
    $book_id = $row['book_id'];

    // Insert book to borrow table
    $sql = "INSERT INTO borrow (book_id, user_id, borrow_date) VALUES (?, ?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();

    // Delete reservation from table
    $sql = "DELETE FROM reservation WHERE reservation_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Redirect to home page with success message
    // header("location: ../admin/home.php");
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script>
        alert("Reserved Book has been borrowed.");
        window.location.href = "index.php";
    </script>
</head>
</html>
