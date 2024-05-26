<nav class="navbar navbar-expand-lg navbar-dark text-bold" style="background-color: #007bff;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="info-logo.png" alt="Logo" width="30" class="d-inline-block align-text-top">
        Library System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <?php
          if (isset($_SESSION['loggedin'])) {
            echo '<a class="nav-link" href="profile.php">Profile</a>';
          }
          ?>
        </li>
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['role'] == 'admin') {
        ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Admin Tools
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="admin/index.php">Dashboard</a></li>
              <li><a class="dropdown-item" href="admin/borrow_return.php">Borrow/Return</a></li>
              <li><a class="dropdown-item" href="admin/book_catalog.php">Book Catalog</a></li>
              <li><a class="dropdown-item" href="admin/reports.php">Reports</a></li>
              <li><a class="dropdown-item" href="admin/users.php">Users</a></li>
            </ul>
          </li>
        <?php
        }
        ?>
        <li class="nav-item">
          <?php
          if (!isset($_SESSION['loggedin'])) {
            echo '<a class="nav-link" href="login.php">Login/Register</a>';
          } else {
            echo '<a class="nav-link" href="logout.php">Logout</a>';
          }
          ?>
        </li>
      </ul>
    </div>

  </div>
</nav>