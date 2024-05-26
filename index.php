<?php
// if user is not logged in, redirect to login page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
} else {
    header("location: login.php");
    exit;
}
?>