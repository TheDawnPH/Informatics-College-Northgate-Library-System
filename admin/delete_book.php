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

$book_id = $_GET['id'];
// delete book from database
$sql = "DELETE FROM books WHERE id = $book_id";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    if (!mysqli_stmt_execute($stmt)) {
        // echo "Something went wrong when deleting job applications. Please try again later.";
        exit;
    }
}
?>
<html>

<head>
<script>
            alert("Book has been deleted successfully.");
            window.location.href = "book_catalog.php";
        </script>
</head>

</html>