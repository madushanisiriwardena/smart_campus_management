<?php
session_start();
include 'connection.php';

// Include PHPMailer
require 'vendor/autoload.php'; // Load Composer dependencies

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch pending student registrations
$student_query = "SELECT u.id, u.full_name, u.email, u.contact, u.dob, u.gender, s.level, s.faculty, s.course 
                  FROM user_tb u 
                  JOIN student_tb s ON u.id = s.id 
                  WHERE u.status = 'pending' AND u.role = 'student'";
$student_result = mysqli_query($conn, $student_query);

// Fetch pending lecturer registrations
$lecturer_query = "SELECT u.id, u.full_name, u.email, u.contact, u.dob, u.gender, l.faculty, l.designation, l.specialization, l.experience, l.qualification 
                   FROM user_tb u 
                   JOIN lecturer_tb l ON u.id = l.id 
                   WHERE u.status = 'pending' AND u.role = 'lecturer'";
$lecturer_result = mysqli_query($conn, $lecturer_query);

// Handle accept/reject actions
if (isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // 'accept' or 'reject'
    $email = $_POST['email'];

    if ($action == 'accept') {
        // Auto-generate a password
        $auto_password = bin2hex(random_bytes(8)); // Generates a random 16-character password
        $hashed_password = password_hash($auto_password, PASSWORD_BCRYPT);

        // Update user_tb with the new password, username, and status
        $update_user_query = "UPDATE user_tb 
                             SET password = '$hashed_password', username = '$email', status = 'approved' 
                             WHERE id = $user_id";
        mysqli_query($conn, $update_user_query);

        // Update student_tb or lecturer_tb with the status
        $role_query = "SELECT role FROM user_tb WHERE id = $user_id";
        $role_result = mysqli_query($conn, $role_query);
        $role_row = mysqli_fetch_assoc($role_result);
        $role = $role_row['role'];

        if ($role == 'student') {
            $update_role_query = "UPDATE student_tb SET status = 'approved' WHERE id = $user_id";
        } elseif ($role == 'lecturer') {
            $update_role_query = "UPDATE lecturer_tb SET status = 'approved' WHERE id = $user_id";
        }
        mysqli_query($conn, $update_role_query);

        // Send email notification with username and auto-generated password
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; //SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'fwafiya22@gmail.com'; //  email
            $mail->Password = 'akfi tyrw tbix thup'; //  email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('fwafiya22@gmail.com', 'Smart Campus');
            $mail->addAddress($email); // recipient

            // Content
            $mail->isHTML(false); // email format to plain text
            $mail->Subject = 'Registration Request Approved';
            $mail->Body = "Dear $role,\n\nYour registration request has been approved. We're excited to have you join our community.
                            \n\nHere are your login details:\n\nUsername: $email\nPassword: $auto_password\n\nTo ensure your account 
                            security, we recommend changing your password after your first login.\n\nThank you.
                            \n\nBest regards,\nSmart Campus.";

            $mail->send();
        } catch (Exception $e) {
            echo "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        // Reject the request
        $update_user_query = "UPDATE user_tb SET status = 'rejected' WHERE id = $user_id";
        mysqli_query($conn, $update_user_query);

        // Update student_tb or lecturer_tb with the status
        $role_query = "SELECT role FROM user_tb WHERE id = $user_id";
        $role_result = mysqli_query($conn, $role_query);
        $role_row = mysqli_fetch_assoc($role_result);
        $role = $role_row['role'];

        if ($role == 'student') {
            $update_role_query = "UPDATE student_tb SET status = 'rejected' WHERE id = $user_id";
        } elseif ($role == 'lecturer') {
            $update_role_query = "UPDATE lecturer_tb SET status = 'rejected' WHERE id = $user_id";
        }
        mysqli_query($conn, $update_role_query);

        // Send email notification for rejection
        $mail = new PHPMailer(true);
        try {
             // Server settings
             $mail->isSMTP();
             $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
             $mail->SMTPAuth = true;
             $mail->Username = 'fwafiya22@gmail.com'; // email
             $mail->Password = 'akfi tyrw tbix thup'; // password
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port = 587;
 
             // Recipients
             $mail->setFrom('fwafiya22@gmail.com', 'Smart Campus');
             $mail->addAddress($email); // Add a recipient
 
            // Content
            $mail->isHTML(false); // Set email format to plain text
            $mail->Subject = 'Registration Request Rejected';
            $mail->Body = "Dear $role,\n\nWe regret to inform you that your registration request has been rejected.
                            \n\nThank you for your understanding.\n\nBest regards,\n\n Smart Campus.";

            $mail->send();
        } catch (Exception $e) {
            echo "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    }

    // Refresh the page to reflect changes
    header("Location: user_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <h1>Admin Dashboard - User Management</h1>
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
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-calendar"></i>Academic Scheduling</a></li>
        <li><a href="#"><i class="fas fa-cubes"></i> Resource Management</a></li>
        <li><a href="#"><i class="fas fa-calendar-alt"></i> Event Management</a></li>
        <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
    </ul>  
</nav>

        <main id="content-area">
            <h1>Registration Requests</h1><br>
            <h3>Student Registration Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Level</th>
                        <th>Faculty</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($student_result)): ?>
                        <tr>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['contact']; ?></td>
                            <td><?php echo $row['dob']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['level']; ?></td>
                            <td><?php echo $row['faculty']; ?></td>
                            <td><?php echo $row['course']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                                    <button type="submit" name="action" value="accept">Accept</button>
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Lecturer Registration Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Faculty</th>
                        <th>Designation</th>
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($lecturer_result)): ?>
                        <tr>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['contact']; ?></td>
                            <td><?php echo $row['dob']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['faculty']; ?></td>
                            <td><?php echo $row['designation']; ?></td>
                            <td><?php echo $row['specialization']; ?></td>
                            <td><?php echo $row['qualification']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                                    <button type="submit" name="action" value="accept">Accept</button>
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        function toggleProfileMenu() {
            document.getElementById('profile-menu').classList.toggle('show');
        }
    </script>
</body>
</html>