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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $isbn = $_POST['isbn'];
        $student_id = $_POST['user_id'];

        // Ensure a valid database connection
        if ($conn) {
            if ($action == 'borrow') {
                // Find the book by ISBN
                $sql = "SELECT book_id FROM books WHERE isbn = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $isbn);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $book_id = $row['book_id'];

                    // Find the user by student ID
                    $sql = "SELECT user_id FROM users WHERE student_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $student_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $user_id = $row['user_id'];

                        // Check if the book is already borrowed by the user
                        $sql = "SELECT borrow_id FROM borrow WHERE book_id = ? AND user_id = ? AND return_date IS NULL";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $book_id, $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows == 0) {
                            // Insert the borrow record
                            $sql = "INSERT INTO borrow (book_id, user_id, borrow_date) VALUES (?, ?, NOW())";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $book_id, $user_id);
                            if ($stmt->execute()) {
                                header("location: borrow_return.php?borrow=success");
                                exit;
                            } else {
                                header("location: borrow_return.php?borrow=failed");
                                exit;
                            }
                        } else {
                            header("location: borrow_return.php?borrow=failed");
                            exit;
                        }
                    } else {
                        header("location: borrow_return.php?borrow=failed");
                        exit;
                    }
                } else {
                    header("location: borrow_return.php?borrow=failed");
                    exit;
                }
            } else if ($action == 'return') {
                // Find the book by ISBN
                $sql = "SELECT book_id FROM books WHERE isbn = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $isbn);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $book_id = $row['book_id'];

                    // Find the user by student ID
                    $sql = "SELECT user_id FROM users WHERE student_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $student_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $user_id = $row['user_id'];

                        // Check if the book is currently borrowed by the user
                        $sql = "SELECT borrow_id FROM borrow WHERE book_id = ? AND user_id = ? AND return_date IS NULL";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $book_id, $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            // Update the borrow record with return date
                            $sql = "UPDATE borrow SET return_date = NOW(), status = 'returned' WHERE book_id = ? AND user_id = ? AND return_date IS NULL";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $book_id, $user_id);
                            if ($stmt->execute()) {
                                header("location: borrow_return.php?return=success");
                                exit;
                            } else {
                                header("location: borrow_return.php?return=failed");
                                exit;
                            }
                        } else {
                            header("location: borrow_return.php?return=failed");
                            exit;
                        }
                    } else {
                        header("location: borrow_return.php?return=failed");
                        exit;
                    }
                } else {
                    header("location: borrow_return.php?return=failed");
                    exit;
                }
            }
        } else {
            // Handle the case where the database connection is not available
            header("location: borrow_return.php?db_error=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Transaction - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-5">Borrow/Return Transaction</h1>
        </div>
    </div>
    <div class="container">
        <div class="row py-3">
            <div class="col">
                <?php if (isset($_GET['borrow'])) : ?>
                    <div class="alert alert-<?php echo $_GET['borrow'] == 'success' ? 'success' : 'danger'; ?>" role="alert">
                        <?php echo $_GET['borrow'] == 'success' ? 'Book borrowed successfully' : 'Failed to borrow book'; ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">Borrow</h1>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES); ?>" method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <small id="isbn" class="form-text text-muted">Use Barcode Scanner to get ISBN Barcode</small>
                                <input type="text" class="form-control" id="isbn" name="isbn" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Student ID</label>
                                <small id="user_id" class="form-text text-muted">Do not include dash</small>
                                <input type="text" class="form-control" id="user_id" name="user_id" required>
                            </div>
                            <input type="hidden" name="action" value="borrow">
                            <button type="submit" class="btn btn-primary">Borrow</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <?php if (isset($_GET['return'])) : ?>
                    <div class="alert alert-<?php echo $_GET['return'] == 'success' ? 'success' : 'danger'; ?>" role="alert">
                        <?php echo $_GET['return'] == 'success' ? 'Book returned successfully' : 'Failed to return book'; ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">Return</h1>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES); ?>" method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <small id="isbn" class="form-text text-muted">Use Barcode Scanner to get ISBN Barcode</small>
                                <input type="text" class="form-control" id="isbn" name="isbn" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Student ID</label>
                                <small id="user_id" class="form-text text-muted">Do not include dash</small>
                                <input type="text" class="form-control" id="user_id" name="user_id" required>
                            </div>
                            <input type="hidden" name="action" value="return">
                            <button type="submit" class="btn btn-warning">Return</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
