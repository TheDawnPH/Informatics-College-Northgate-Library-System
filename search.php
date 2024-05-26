<?php
include 'config.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
} 

$search = '%' . $_GET['search'] . '%';

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
            <h3>Search Results for: <?php echo htmlspecialchars($_GET['search']); ?></h3>
            <hr>
            <?php
            $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ? OR subject LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Author</th>
                        <th scope="col">Category</th>
                        <th scope="col">Subject</th>
                        <th scope="col">ISBN</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                    <th scope="row">' . htmlspecialchars($row["book_id"]) . '</th>
                    <td>' . htmlspecialchars($row["title"]) . '</td>
                    <td>' . htmlspecialchars($row["author"]) . '</td>
                    <td>' . htmlspecialchars($row["category"]) . '</td>
                    <td>' . htmlspecialchars($row["subject"]) . '</td>
                    <td>' . htmlspecialchars($row["isbn"]) . '</td>
                    <td><a href="reserve.php?id=' . htmlspecialchars($row["book_id"]) . '" class="btn btn-primary">Reserve</a></td>
                </tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="alert alert-warning" role="alert">
                No books found!
            </div>';
            }
            ?>
        </div>
    </div>
</body>

</html>
