<?php
// env function
function loadEnv($path = __DIR__)
{
    $envFile = $path . '/.env';

    if (!file_exists($envFile)) {
        return false;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        } else {
            $_ENV[$line] = null;
        }
    }

    return true;
}

// Load environment variables from .env file
// if loadEnv is already loaded, don't load it again
if (!isset($_ENV['DB_HOST'])) {
    loadEnv();
}

// if anyone visit this page redirect to 404 page
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("location: 404.php");
    exit;
}

// mkdir and change permission upload folder in windows
$cmd = "mkdir uploads && icacls uploads /grant Everyone:F /t";
exec($cmd);

// mkdir and change permission upload folder in linux
$cmd = "mkdir uploads && chmod 777 uploads";
exec($cmd);

date_default_timezone_set('Asia/Manila');

// Database connection
define('DB_SERVER', $_ENV['DB_HOST']);
define('DB_USERNAME', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);

// Get connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// create table for users
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    user_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    student_id TEXT NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    grade_level TEXT NOT NULL,
    password TEXT NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    image_user TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_users_table);

// create table for books
$create_books_table = "CREATE TABLE IF NOT EXISTS books (
    book_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    category TEXT NOT NULL,
    subject TEXT NOT NULL,
    isbn TEXT NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_books_table);

// create table for borrow/return
$create_borrow_table = "CREATE TABLE IF NOT EXISTS borrow (
    borrow_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(255) NOT NULL,
    book_id INT(255) NOT NULL,
    borrow_date DATE DEFAULT CURRENT_TIMESTAMP,
    return_date DATE,
    status ENUM('borrowed', 'returned') NOT NULL DEFAULT 'borrowed',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
)";

mysqli_query($conn, $create_borrow_table);

// create table for reservation of book
$create_reservation_table = "CREATE TABLE IF NOT EXISTS reservation (
    reservation_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(255) NOT NULL,
    book_id INT(255) NOT NULL,
    reserve_date DATE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
)";

mysqli_query($conn, $create_reservation_table);

// create user data for admin if not exists
$check_admin = "SELECT * FROM users WHERE role = 'admin'";
$admin_result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($admin_result) == 0) {
    $admin_password = password_hash('infoLibrary!', PASSWORD_DEFAULT);
    $create_admin = "INSERT INTO users (student_id, first_name, last_name, grade_level, password, role) VALUES ('admin', 'Informatics', 'Administrator', 'Corporate', '$admin_password', 'admin')";
    mysqli_query($conn, $create_admin);
}