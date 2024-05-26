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
    <title>Reports - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        @media print {
            .print-only {
                display: block;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid no-print" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-5">Reports</h1>
        </div>
    </div>
    <div class="container">
        <div class="row py-3 no-print">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Print Borrow and Return Activity</h5>
                        <p class="card-text">Print the borrow and return activity of books</p>
                        <a href="#" class="btn btn-primary" onclick="window.print()">Print</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table showing the activity of books from borrow  table -->
        <h2 class="text-center print-only">Borrow and Return Activity</h2>
        <?php
        $sql = "SELECT * FROM borrow";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered print-only">';
            echo '<thead>';
            echo '<tr>';
            echo '<th scope="col">ID</th>';
            echo '<th scope="col">Book ISBN</th>';
            echo '<th scope="col">Student ID</th>';
            echo '<th scope="col">Borrow Date</th>';
            echo '<th scope="col">Return Date</th>';
            echo '<th scope="col">Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                // get student id by user_id
                $student_id = $row['user_id'];
                $sql_student = "SELECT * FROM users WHERE user_id = '$student_id'";
                $result_student = $conn->query($sql_student);
                $student = $result_student->fetch_assoc();

                // get isbn from book id
                $book_id = $row['book_id'];
                $sql_book = "SELECT * FROM books WHERE book_id = '$book_id'";
                $result_book = $conn->query($sql_book);
                $book = $result_book->fetch_assoc();

                echo '<tr>';
                echo '<td>' . $row['borrow_id'] . '</td>';
                echo '<td>' . $book['isbn'] . '</td>';
                echo '<td>' . $student['student_id'] . '</td>';
                echo '<td>' . $row['borrow_date'] . '</td>';
                echo '<td>' . $row['return_date'] . '</td>';
                echo '<td>' . $row['status'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p class="text-center">No activity found</p>';
        }
        ?>
    </div>
</body>

</html>
