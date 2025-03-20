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
$user_role = $_SESSION['role'];

?>

<!-- Include CSS -->
<link rel="stylesheet" href="collaboration/collaboration.css">

<div class="dashboard-container">
    <h2 class="dashboard-title">Manage Groups</h2>

    <!-- Dropdown to Select Group -->
    <form method="POST" class="form-group">
        <label>Select Group</label>
        <select name="group_id" required>
            <option value="">-- Choose a Group --</option>
            <?php
            $query = "SELECT * FROM collaboration_groups 
                      WHERE created_by='$user_id' 
                      OR EXISTS (SELECT 1 FROM group_members WHERE group_id=collaboration_groups.group_id AND user_id='$user_id' AND role='admin')";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['group_id'] . "'>" . htmlspecialchars($row['group_name']) . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="view_members" class="btn-create view-members-btn">View Members</button>
    </form>

    <?php
    if (isset($_POST['view_members']) && isset($_POST['group_id']) && !empty($_POST['group_id'])) {
        $group_id = $_POST['group_id'];

        // Check if user is group admin or system admin
        $is_admin_query = "SELECT * FROM group_members WHERE group_id = '$group_id' AND user_id = '$user_id' AND role = 'admin'";
        $is_admin_result = mysqli_query($conn, $is_admin_query);
        $is_system_admin = ($user_role == 'admin');

        echo "<h3>Group Members</h3>";

        if (mysqli_num_rows($is_admin_result) > 0 || $is_system_admin) {
            echo "<form method='POST' class='add-member-form'>
                    <input type='hidden' name='group_id' value='$group_id'>
                    <input type='email' name='member_email' placeholder='Enter user email' required>
                    <button type='submit' name='add_member' class='btn-create'>Add</button>
                  </form>";
        }

        echo "<table class='table'>
                <tr>
                    <th>Member Name</th>
                    <th>Role</th>";
        if (mysqli_num_rows($is_admin_result) > 0 || $is_system_admin) {
            echo "<th>Actions</th>";
        }
        echo "</tr>";

        $member_query = "SELECT u.id, u.full_name, gm.role FROM user_tb u 
                         JOIN group_members gm ON u.id = gm.user_id 
                         WHERE gm.group_id = '$group_id'";
        $member_result = mysqli_query($conn, $member_query);

        while ($member = mysqli_fetch_assoc($member_result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($member['full_name']) . "</td>
                    <td>" . ucfirst($member['role']) . "</td>";

            if (mysqli_num_rows($is_admin_result) > 0 || $is_system_admin) {
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='group_id' value='$group_id'>
                            <input type='hidden' name='member_id' value='" . $member['id'] . "'>
                            <button type='submit' name='remove_member' class='btn-delete'>Remove</button>
                        </form>
                      </td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>

</div>

<?php
// Handle Member Removal
if (isset($_POST['remove_member'])) {
    $group_id = $_POST['group_id'];
    $member_id = $_POST['member_id'];

    $delete_query = "DELETE FROM group_members WHERE group_id = '$group_id' AND user_id = '$member_id'";
    mysqli_query($conn, $delete_query);
    echo "<script>alert('Member removed successfully!'); window.location.href='/scms/admin_dashboard.php?page=manage_groups';</script>";
}

// Handle Adding Members
if (isset($_POST['add_member'])) {
    $group_id = $_POST['group_id'];
    $member_email = mysqli_real_escape_string($conn, $_POST['member_email']);

    $user_query = "SELECT id FROM user_tb WHERE email = '$member_email'";
    $user_result = mysqli_query($conn, $user_query);

    if (mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $new_member_id = $user_data['id'];

        $insert_query = "INSERT INTO group_members (group_id, user_id, role) VALUES ('$group_id', '$new_member_id', 'member')";
        mysqli_query($conn, $insert_query);
        echo "<script>alert('Member added successfully!'); window.location.href='/scms/admin_dashboard.php?page=manage_groups';</script>";
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}
?>
