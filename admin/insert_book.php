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

$error = $success = '';
$title = $author = $category = $subject = $isbn = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["title"]))) {
        $error = "Please enter Book Title.";
    } else {
        $title = trim($_POST["title"]);
    }

    if (empty(trim($_POST["author"]))) {
        $error = "Please enter Author.";
    } else {
        $author = trim($_POST["author"]);
    }

    if (empty(trim($_POST["category"]))) {
        $error = "Please enter Category.";
    } else {
        $category = trim($_POST["category"]);
    }

    if (empty(trim($_POST["subject"]))) {
        $error = "Please enter Subject.";
    } else {
        $subject = trim($_POST["subject"]);
    }

    if (empty(trim($_POST["isbn"]))) {
        $error = "Please enter ISBN.";
    } else {
        $isbn = trim($_POST["isbn"]);
    }

    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Check if file already exists
    if (file_exists($target_file)) {
        // Rename to add Unix time after name
        $image = pathinfo($image, PATHINFO_FILENAME) . time() . '.' . pathinfo($image, PATHINFO_EXTENSION);
    }

    $newtarget_file = $target_dir . $image;

    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = "Sorry, only JPG, JPEG, PNG files are allowed.";
    }

    if (empty($error)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $newtarget_file)) {
            $sql = "INSERT INTO books (title, author, category, subject, isbn, image) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssss", $title, $author, $category, $subject, $isbn, $image);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Book added successfully.";
                } else {
                    $error = "Something went wrong. Please try again later.";
                }
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
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
    <title>Book Catalog - Informatics College Northgate Library System</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/index.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmAction() {
            return confirm("Are you sure to insert this book?");
        }
    </script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-5">Add Book Catalog</h1>
        </div>
    </div>
    <div class="container mt-5">
        <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="post" autocomplete="off" enctype="multipart/form-data">
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Front Book Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick='return confirmAction();'>Add Book</button>
        </form>
    </div>
</body>

</html>
