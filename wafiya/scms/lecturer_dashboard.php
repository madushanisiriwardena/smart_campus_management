<?php
session_start();
include 'connection.php';

// Check if the user is logged in and is a lecturer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = "";

// Fetch lecturer's full name from the database
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
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="lecturer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <h2>Welcome Back <?php echo htmlspecialchars($full_name); ?>!</h2>
            </div>
            <div class="header-icons">
                <i class="fas fa-bell"></i>
                <div class="profile">
                    <i class="fas fa-user-circle" onclick="toggleProfileMenu()"></i>
                    <div class="profile-menu" id="profile-menu">
                        <a href="lecturer_profile.php">Profile</a>
                        <a href="index.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <nav class="sidebar">
            <ul>
                <li class="active"><a href="lecturer_dashboard"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-calendar"></i> My Schedule</a></li>
                <li><a href="#"><i class="fas fa-cubes"></i> Resources</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Communication</a></li>
            </ul>  
        </nav>

        <main id="content-area">
            
        </main>
    </div>
</body>
</html>