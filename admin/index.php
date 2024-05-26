<?php
$home_dir = '..';
include $home_dir . '/config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// if user is not admin redirect to home page
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 'admin') {
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
    <title>Admin - Informatics College Northgate Library System</title>

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
            <h1 class="text-white text-center py-5">Admin Panel</h1>
        </div>
    </div>
    <div class="container">
        <div class="row py-3">
            <div class="col-md-4">
                <div class="card text-bg-primary">
                    <h5 class="card-header text-center">Manage Users</h5>
                    <div class="card-body text-center">
                        <p class="card-text">Add, edit, delete users.</p>
                        <a href="users.php" class="text-white stretched-link">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning">
                    <h5 class="card-header text-center">Book Catalog</h5>
                    <div class="card-body text-center">
                        <p class="card-text">Insert and Delete Books.</p>
                        <a href="book_catalog.php" class="text-white stretched-link">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-dark">
                    <h5 class="card-header text-center">Borrow/Return Transaction</h5>
                    <div class="card-body text-center">
                        <p class="card-text">Transact for borrow/return book.</p>
                        <a href="borrow_return.php" class="text-white stretched-link">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card text-bg-success">
                    <h5 class="card-header text-center">View Reports</h5>
                    <div class="card-body text-center">
                        <p class="card-text">View reports of transactions.</p>
                        <a href="reports.php" class="text-white stretched-link">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Show all reserve books, and add button to borrow -->
        <div class="row py-4">
            <hr>
            <h3>Reserved Books</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Book ISBN</th>
                        <th scope="col">Book Title</th>
                        <th scope="col">Reserver</th>
                        <th scope="col">Reserved Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM reservation ORDER BY reserve_date ASC LIMIT 5";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $reservation_id = $row['reservation_id'];
                            $book_id = $row['book_id'];
                            $reserver_id = $row['user_id'];
                            $reserve_date = $row['reserve_date'];

                            // Initialize $reserver variable
                            $reserver = '';

                            $sql_book = "SELECT title, isbn FROM books WHERE book_id ='$book_id'";
                            $result_book = $conn->query($sql_book);
                            $row_book = $result_book->fetch_assoc();
                            $book_title = $row_book['title'];
                            $book_isbn = $row_book['isbn'];

                            $sql_reserver = "SELECT * FROM users WHERE user_id = '$reserver_id'";
                            $result_reserver = $conn->query($sql_reserver);
                            $row_reserver = $result_reserver->fetch_assoc();

                            // Check if reserver exists
                            if ($row_reserver) {
                                $reserver = $row_reserver['first_name'] . " " . $row_reserver['last_name'];
                            }
                            echo "<tr>";
                            echo "<td>$book_isbn</td>";
                            echo "<td>$book_title</td>";

                            // Display reserver if defined
                            echo "<td>";
                            if (!empty($reserver)) {
                                echo $reserver;
                            } else {
                                echo "Unknown";
                            }
                            echo "</td>";

                            echo "<td>$reserve_date</td>";
                            // actions to borrow book, or remove reservation
                            echo "<td>";
                            echo "<a href='borrow_reserve.php?reserve_id=$reservation_id&user_id=$reserver_id' class='btn btn-primary'>Borrow</a> <a href='remove_reservation.php?reserve_id=$reservation_id&user_id=$reserver_id' class='btn btn-danger' onclick='return confirmAction();'>Remove</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <div class="row py-4">
            <hr>
            <h3>Latest Borrowed Book</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Book ISBN</th>
                        <th scope="col">Book Title</th>
                        <th scope="col">Borrower</th>
                        <th scope="col">Borrowed Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM borrow ORDER BY borrow_date ASC LIMIT 5";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $book_id = $row['book_id'];
                            $borrower_id = $row['user_id'];
                            $borrow_date = $row['borrow_date'];

                            // Initialize $borrower variable
                            $borrower = '';

                            $sql_book = "SELECT title, isbn FROM books WHERE book_id ='$book_id'";
                            $result_book = $conn->query($sql_book);
                            $row_book = $result_book->fetch_assoc();
                            $book_title = $row_book['title'];
                            $book_isbn = $row_book['isbn'];

                            $sql_borrower = "SELECT * FROM users WHERE user_id = '$borrower_id'";
                            $result_borrower = $conn->query($sql_borrower);
                            $row_borrower = $result_borrower->fetch_assoc();

                            // Check if borrower exists
                            if ($row_borrower) {
                                $borrower = $row_borrower['first_name'] . " " . $row_borrower['last_name'];
                            }
                            echo "<tr>";
                            echo "<td>$book_isbn</td>";
                            echo "<td>$book_title</td>";

                            // Display borrower if defined
                            echo "<td>";
                            if (!empty($borrower)) {
                                echo $borrower;
                            } else {
                                echo "Unknown";
                            }
                            echo "</td>";

                            echo "<td>$borrow_date</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>