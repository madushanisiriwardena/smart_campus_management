
<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = "";

// Fetch admin's full name from the database
$query = "SELECT full_name FROM user_tb WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function toggleProfileMenu() {
            document.getElementById('profile-menu').classList.toggle('show');
        }
    </script>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="logo">
                <img src="images/campus_logo.png" alt="Campus Logo">
            </div>
        </div>

        <div class="header-right">
            <div class="welcome">
                <h1>Admin Dashboard</h1>
                <h2>Welcome Back <?php echo htmlspecialchars($full_name); ?>!</h2>
            </div>
            <div class="header-icons">
                <i class="fas fa-bell"></i>
                <div class="profile">
                    <i class="fas fa-user-circle" onclick="toggleProfileMenu()"></i>
                    <div class="profile-menu" id="profile-menu">
                        <a href="admin_profile.php">Profile</a>
                        <a href="index.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
    <nav class="sidebar">
    <ul>
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li class="active">
            <a href="#"><i class="fas fa-users"></i> User Management</a>
            <ul class="sub-menu">
                <li><a href="user_management.php">Registration Requests</a></li>
                <li>
                    <a href="#">View User Details</a>
                        <ul class="sub-sub-menu">
                            <li><a href="manage_students.php">Students</a></li>
                            <li><a href="manage_lecturers.php">Lecturers</a></li>
                        </ul>
                </li>
        <li><a href="#"><i class="fas fa-calendar"></i>Academic Scheduling</a></li>
        <li><a href="#"><i class="fas fa-cubes"></i> Resource Management</a></li>
        <li><a href="#"><i class="fas fa-calendar-alt"></i> Event Management</a></li>
        <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
    </ul>  
</nav>

        <main id="content-area">
          
        </main>

    </div>
</body>
</html>
