<?php
include 'connection.php';

if (isset($_POST['continue_registration'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Insert into user_tb
    $query = "INSERT INTO user_tb (full_name, email, contact, dob, gender, role, status) 
              VALUES ('$full_name', '$email', '$contact', '$dob', '$gender', '$role', 'pending')";

    if (mysqli_query($conn, $query)) {
        $id = mysqli_insert_id($conn); // Get the auto-generated user ID
        if ($role == 'student') {
            header("Location: student_registration.php?id=$id");
        } else if ($role == 'lecturer') {
            header("Location: lecturer_registration.php?id=$id");
        }
        exit();
    } else {
        $error = "Something went wrong. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Campus</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="left">
        <img src="images/campus.jpeg" alt="Campus Image">
    </div>
    <div class="right">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact" placeholder="Contact Number" required><br>

            <label>Date of Birth:</label>
            <input type="date" name="dob" required>

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
            </select>

            <button type="submit" name="continue_registration">Continue Registration</button>
        </form><br>
        <p>Already have an account? <a href="index.php">Login Here</a></p>
    </div>
</body>
</html>
