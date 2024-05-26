<?php
include 'config.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Ensure this is the correct session variable for the user ID

// Query to get the borrowed books
$borrowed_sql = "SELECT borrow.*, books.title AS book_title FROM borrow
        JOIN books ON borrow.book_id = books.book_id
        WHERE borrow.user_id = ? AND borrow.status = 'borrowed'";
$borrowed_stmt = $conn->prepare($borrowed_sql);
$borrowed_stmt->bind_param("i", $user_id);
$borrowed_stmt->execute();
$borrowed_result = $borrowed_stmt->get_result();

// Query to get the reserved books
$reservation_sql = "SELECT * FROM reservation WHERE user_id = ?";
$reservation_stmt = $conn->prepare($reservation_sql);
$reservation_stmt->bind_param("i", $user_id);
$reservation_stmt->execute();
$reservation_result = $reservation_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col g-4">
                <!-- centered logo -->
                <div class="text-center">
                    <img src="info-logo.png" alt="Informatics Logo" width="200" height="200">
                </div>
                <h1>Welcome to the Informatics College Northgate Library System</h1>
                <p>This system is designed to help you manage your library resources. You can search for books, view your profile, and more.</p>
            </div>
            <div class="col g-4">
                <!-- Search bar for books -->
                <div class="card">
                    <div class="card-header">
                        <h3>Search Books to borrow</h3>
                    </div>
                    <div class="card-body">
                        <form action="search.php" method="get">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search for books" name="search" aria-label="Search for books" aria-describedby="button-search">
                                <button class="btn btn-outline-secondary" type="submit" id="button-search">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <!-- show reserved books from reservation table, and list them -->
        <div class="row">
            <div class="col text-center">
                <div class="card text-dark bg-warning mb-3" style="max-width: 50rem;">
                    <div class="card-header">
                        <h1>
                            <?php echo $reservation_result->num_rows; ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Number of Books Reserved</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Books you have reserved</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($reservation_result->num_rows > 0) {
                            echo "<table class='table table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th scope='col'>Reference Number</th>";
                            echo "<th scope='col'>Book Title</th>";
                            echo "<th scope='col'>Date Reserved</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = $reservation_result->fetch_assoc()) {
                                // get book title from book_id
                                $sql = "SELECT title FROM books WHERE book_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $row['book_id']);
                                $stmt->execute();
                                $book = $stmt->get_result()->fetch_assoc();

                                echo "<tr>";
                                echo "<th scope='row'>" . $row['reservation_id'] . "</th>";
                                echo "<td>" . $book['title'] . "</td>";
                                echo "<td>" . $row['reserve_date'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                        } else {
                            echo "<p>You have not reserved any books yet.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <!-- show status of borrowed books, and list them -->
        <div class="row">
            <div class="col text-center">
                <div class="card text-white bg-primary mb-3" style="max-width: 50rem;">
                    <div class="card-header">
                        <h1>
                            <?php echo $borrowed_result->num_rows; ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Number of Books Borrowed</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Books you have borrowed</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($borrowed_result->num_rows > 0) {
                            echo "<table class='table table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th scope='col'>Reference Number</th>";
                            echo "<th scope='col'>Book Title</th>";
                            echo "<th scope='col'>Date Borrowed</th>";
                            echo "<th scope='col'>Status</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = $borrowed_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<th scope='row'>" . $row['borrow_id'] . "</th>";
                                echo "<td>" . $row['book_title'] . "</td>";
                                echo "<td>" . $row['borrow_date'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                        } else {
                            echo "<p>You have not borrowed any books yet.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>
