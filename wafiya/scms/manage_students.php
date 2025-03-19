<?php
session_start();
include 'connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all approved students for School of Computing
$computing_query = "SELECT u.full_name, u.email, u.contact, u.dob, u.gender, s.level, s.faculty, s.course 
                    FROM user_tb u 
                    JOIN student_tb s ON u.id = s.id 
                    WHERE u.status = 'approved' AND u.role = 'student' AND s.faculty = 'School of Computing'";
$computing_result = mysqli_query($conn, $computing_query);

// Fetch all approved students for School of Business Management
$business_query = "SELECT u.full_name, u.email, u.contact, u.dob, u.gender, s.level, s.faculty, s.course 
                   FROM user_tb u 
                   JOIN student_tb s ON u.id = s.id 
                   WHERE u.status = 'approved' AND u.role = 'student' AND s.faculty = 'School of Business Management'";
$business_result = mysqli_query($conn, $business_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function filterStudents(faculty) {
            const level = document.querySelector(`input[name="level_${faculty}"]:checked`)?.value || '';
            const course = document.querySelector(`input[name="course_${faculty}"]:checked`)?.value || '';

            fetch(`filter_students.php?level=${level}&course=${course}&faculty=${faculty}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById(`student-table-${faculty}`).innerHTML = data;
                });
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
            <h1>View Students Details</h1><br>

            <!-- School of Computing Section -->
            <div class="filter-section">
                <h3>School of Computing</h3>
                <div class="filter-options">
                    <!-- Level Filter Group -->
                    <div class="filter-group">
                        <h4>Level</h4>
                        <label><input type="checkbox" name="level_School of Computing" value="Diploma Level" onchange="filterStudents('School of Computing')"> Diploma Level</label>
                        <label><input type="checkbox" name="level_School of Computing" value="Higher National Diploma Level" onchange="filterStudents('School of Computing')"> Higher National Diploma Level</label>
                        <label><input type="checkbox" name="level_School of Computing" value="Undergraduate Level" onchange="filterStudents('School of Computing')"> Undergraduate Level</label>
                    </div>

                    <!-- Course Filter Group -->
                    <div class="filter-group">
                        <h4>Course</h4>
                        <label><input type="checkbox" name="course_School of Computing" value="Information Technology - General" onchange="filterStudents('School of Computing')"> Information Technology - General</label>
                        <label><input type="checkbox" name="course_School of Computing" value="Software Engineering" onchange="filterStudents('School of Computing')"> Software Engineering</label>
                        <label><input type="checkbox" name="course_School of Computing" value="Network Engineering" onchange="filterStudents('School of Computing')"> Network Engineering</label>
                        <label><input type="checkbox" name="course_School of Computing" value="Data Analytics" onchange="filterStudents('School of Computing')"> Data Analytics</label>
                    </div>
                </div>
                <div id="student-table-School of Computing">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Level</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($computing_result)): ?>
                            <tr>
                                <td><?php echo $row['full_name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td><?php echo $row['dob']; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td><?php echo $row['level']; ?></td>
                                <td><?php echo $row['course']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

            

            <!-- School of Business Management Section -->
            <div class="filter-section">
                <h3>School of Business Management</h3>
                <div class="filter-options">
                    <div class="filter-group">
                        <h4>Level</h4> 
                        <label><input type="checkbox" name="level_School of Business Management" value="Diploma Level" onchange="filterStudents('School of Business Management')"> Diploma Level</label>
                        <label><input type="checkbox" name="level_School of Business Management" value="Higher National Diploma Level" onchange="filterStudents('School of Business Management')"> Higher National Diploma Level</label>
                        <label><input type="checkbox" name="level_School of Business Management" value="Undergraduate Level" onchange="filterStudents('School of Business Management')"> Undergraduate Level</label>
                    </div>
                    <div class="filter-group">
                        <h4>Course</h4>
                        <label><input type="checkbox" name="course_School of Business Management" value="Business Management - General" onchange="filterStudents('School of Business Management')"> Business Management - General</label>
                        <label><input type="checkbox" name="course_School of Business Management" value="Accounting and Finance" onchange="filterStudents('School of Business Management')"> Accounting and Finance</label>
                        <label><input type="checkbox" name="course_School of Business Management" value="Human Resource Management" onchange="filterStudents('School of Business Management')"> Human Resource Management</label>
                    </div>
                </div>

                <div id="student-table-School of Business Management">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Level</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($business_result)): ?>
                            <tr>
                                <td><?php echo $row['full_name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td><?php echo $row['dob']; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td><?php echo $row['level']; ?></td>
                                <td><?php echo $row['course']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            </div> 
        </main>
    </div>

    <script>
        function toggleProfileMenu() {
            document.getElementById('profile-menu').classList.toggle('show');
        }
    </script>
</body>
</html>