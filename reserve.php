<?php
include 'config.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

// insert reservation to table
$sql = "INSERT INTO reservation (book_id, user_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
?>
<html>

<head>
<script>
            alert("Book has been reserved successfully.");
            window.location.href = "home.php";
        </script>
</head>

</html>
