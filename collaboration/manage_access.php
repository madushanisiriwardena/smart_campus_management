
<<?php
include __DIR__ . '/../connection.php'; // Database connection
?>

<link rel="stylesheet" href="collaboration/manage_access.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="manage-access-container">
    <!-- ✅ Section Header -->
    <h2><i class="fas fa-users-cog"></i> Manage File Access</h2>

    <!-- ✅ Grant Access to Users -->
    <div class="access-block">
        <h3>Grant Access to Users</h3>
        <form class="access-form" action="manage_access.php" method="POST">
            <select name="user_id" required>
                <option value="">Select a User</option>
                <?php
                // Fetch users from the database
                $user_query = "SELECT id, username FROM user_tb";
                $user_result = $conn->query($user_query);
                while ($row = $user_result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                ?>
            </select>

            <select name="permission" required>
                <option value="view">View</option>
                <option value="edit">Edit</option>
            </select>

            <button type="submit" name="grant_user_access" class="grant-btn">Grant Access</button>
        </form>
    </div>

    <!-- ✅ Grant Access to Groups -->
    <div class="access-block">
        <h3>Grant Access to Groups</h3>
        <form class="access-form" action="manage_access.php" method="POST">
            <select name="group_id" required>
                <option value="">Select a Group</option>
                <?php
                // Fetch groups from the database
                $group_query = "SELECT group_id, group_name FROM collaboration_groups";
                $group_result = $conn->query($group_query);
                while ($row = $group_result->fetch_assoc()) {
                    echo "<option value='{$row['group_id']}'>{$row['group_name']}</option>";
                }
                ?>
            </select>

            <select name="permission" required>
                <option value="view">View</option>
                <option value="edit">Edit</option>
            </select>

            <button type="submit" name="grant_group_access" class="grant-btn">Grant Access</button>
        </form>
    </div>

    <!-- ✅ Access Control List -->
    <div class="access-block">
        <h3>Access Control List</h3>
        <table class="access-table">
            <thead>
                <tr>
                    <th>User / Group</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch user access list
                $access_query = "SELECT a.access_id, u.email, a.permission FROM file_access_control a, 
                                user_tb u WHERE a.user_id = u.id";
                $access_result = $conn->query($access_query);
                while ($row = $access_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['email']}</td>
                        <td>{$row['permission']}</td>
                        <td><button class='remove-btn' data-access-id='{$row['access_id']}'>Remove</button></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // ✅ Handle Remove Access Button Click
    document.querySelectorAll(".remove-btn").forEach(button => {
        button.addEventListener("click", function() {
            let accessId = this.dataset.accessId;
            if (confirm("Are you sure you want to remove this access?")) {
                fetch("manage_access.php?action=delete&access_id=" + accessId, {
                    method: "GET"
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Access removed successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
</script>

<?php
// ✅ Handle Granting Access to Users
if (isset($_POST['grant_user_access'])) {
    $user_id = $_POST['user_id'];
    $permission = $_POST['permission'];

    $sql = "INSERT INTO access_control (user_id, permission) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $permission);
    if ($stmt->execute()) {
        echo "<script>alert('Access granted successfully!'); window.location.href='manage_access.php';</script>";
    } else {
        echo "<script>alert('Error granting access');</script>";
    }
    $stmt->close();
}

// ✅ Handle Granting Access to Groups
if (isset($_POST['grant_group_access'])) {
    $group_id = $_POST['group_id'];
    $permission = $_POST['permission'];

    $sql = "INSERT INTO access_control (group_id, permission) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $group_id, $permission);
    if ($stmt->execute()) {
        echo "<script>alert('Group access granted successfully!'); window.location.href='manage_access.php';</script>";
    } else {
        echo "<script>alert('Error granting access');</script>";
    }
    $stmt->close();
}

// ✅ Handle Removing Access
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['access_id'])) {
    $access_id = intval($_GET['access_id']);

    $sql = "DELETE FROM access_control WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $access_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Failed to prepare delete query"]);
    }
    exit();
}
?>
