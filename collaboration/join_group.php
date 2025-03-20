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

if (isset($_POST['join_group'])) {
    $group_id = $_POST['group_id'];

    $check_query = "SELECT * FROM group_members WHERE group_id = '$group_id' AND user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        mysqli_query($conn, "INSERT INTO group_members (group_id, user_id, role) VALUES ('$group_id', '$user_id', 'member')");
        echo "<script>alert('You have successfully joined the group!'); window.location.href='/scms/admin_dashboard.php?page=groups';</script>";
    } else {
        echo "<script>alert('You are already a member of this group.');</script>";
    }
}
?>

<link rel="stylesheet" href="collaboration/collaboration.css">

<div class="dashboard-container">
    <h2 class="dashboard-title">Join a Group</h2>

    <form method="POST" class="modal-form">
        <div class="form-group">
            <label>Select Group</label>
            <select name="group_id">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM collaboration_groups WHERE group_type='public'");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['group_id'] . "'>" . htmlspecialchars($row['group_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" name="join_group" class="btn-create">Join Group</button>
    </form>
</div>
