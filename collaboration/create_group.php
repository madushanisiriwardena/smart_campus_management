<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /scms/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['create_group'])) {
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $group_type = mysqli_real_escape_string($conn, $_POST['group_type']);
    $group_description = mysqli_real_escape_string($conn, $_POST['group_description']);

    $query = "INSERT INTO collaboration_groups (group_name, group_type, group_description, created_by) 
              VALUES ('$group_name', '$group_type', '$group_description', '$user_id')";

    if (mysqli_query($conn, $query)) {
        $group_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO group_members (group_id, user_id, role) VALUES ('$group_id', '$user_id', 'admin')");
        echo "<script>alert('Group created successfully!'); window.location.href='/scms/admin_dashboard.php?page=groups';</script>";
    } else {
        echo "<script>alert('Error creating group: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<link rel="stylesheet" href="collaboration/collaboration.css">

<div class="dashboard-container">
    <h2 class="dashboard-title">Create a Group</h2>
    
    <form method="POST" class="modal-form">
        <div class="form-group">
            <label>Group Name</label>
            <input type="text" name="group_name" required>
        </div>

        <div class="form-group">
            <label>Group Type</label>
            <select name="group_type">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div>

        <div class="form-group">
            <label for="group_description">Group Description</label>
            <textarea id="group_description" name="group_description" required></textarea>
        </div>


        <button type="submit" name="create_group" class="btn-create">Create Group</button>
    </form>
</div>
