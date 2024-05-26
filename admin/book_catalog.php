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
    <title>Book Catalog - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmAction() {
            return confirm("Are you sure you want to delete this book?");
        }
    </script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-5">Book Catalog</h1>
        </div>
    </div>
    <div class="container">
        <div class="row py-3">
            <div class="col-md-4">
                <div class="card text-bg-primary">
                    <h5 class="card-header text-center">Add Book</h5>
                    <div class="card-body text-center">
                        <p class="card-text">Add a new book to the catalog.</p>
                        <a href="insert_book.php" class="btn btn-light">Add Book</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table showing the books with actions such as delete book -->
        <div class="row py-3">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header text-center">Book Catalog</h5>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Book ID</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">ISBN</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM books";
                                if ($result = $conn->query($sql)) {
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row["book_id"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["author"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["isbn"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["category"]) . "</td>";
                                            echo "<td><a onclick='return confirmAction();' href='delete_book.php?book_id=" . htmlspecialchars($row["book_id"]) . "' class='btn btn-danger'>Delete</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No books found</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>Error retrieving data: " . htmlspecialchars($conn->error) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
