<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user_tb WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    $update_query = "UPDATE user_tb SET full_name='$full_name', username='$username', email='$email', contact='$contact', dob='$dob', gender='$gender' WHERE id=$user_id";
    if (mysqli_query($conn, $update_query)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $password_query = "SELECT password FROM user_tb WHERE id = $user_id";
    $password_result = mysqli_query($conn, $password_query);
    $row = mysqli_fetch_assoc($password_result);

    if (password_verify($old_password, $row['password'])) {
        $update_password = "UPDATE user_tb SET password='$new_password' WHERE id=$user_id";
        if (mysqli_query($conn, $update_password)) {
            $success = "Password changed successfully!";
        } else {
            $error = "Error changing password.";
        }
    } else {
        $error = "Old password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="profile-body">
    <div class="profile-container">

    <a href="admin_dashboard.php" class="close-btn">
    <i class="fas fa-times"></i>
</a>

        <h2>Admin Profile</h2>

        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" class="profile-form">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name:</label>
                <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user"></i>  Username:</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-phone"></i> Contact:</label>
                <input type="text" name="contact" value="<?php echo $user['contact']; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-calendar"></i> Date of Birth:</label>
                <input type="date" name="dob" value="<?php echo $user['dob']; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Gender:</label>
                <select name="gender">
                    <option value="male" <?php if ($user['gender'] == 'male') echo "selected"; ?>>Male</option>
                    <option value="female" <?php if ($user['gender'] == 'female') echo "selected"; ?>>Female</option>
                </select>
            </div>

            <button type="submit" name="update_profile" class="btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form><br><br>

        <h3>Change Password</h3>
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Old Password:</label>
                <input type="password" name="old_password" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-key"></i> New Password:</label>
                <input type="password" name="new_password" required>
            </div>

            <button type="submit" name="change_password" class="btn-primary">
                <i class="fas fa-sync-alt"></i> Change Password
            </button>
        </form>
    </div>
    </div>
</body>
</html>