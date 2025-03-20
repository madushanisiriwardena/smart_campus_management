<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../connection.php';

$user_id = $_SESSION['user_id']; // Ensure user is logged in

// Fetch Groups for Dropdown Selection
$group_sql = "SELECT group_id, group_name FROM collaboration_groups";
$group_result = $conn->query($group_sql);
$groups = [];
while ($row = $group_result->fetch_assoc()) {
    $groups[] = $row;
}

// Handle Task Creation (Without jQuery)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['task_name'])) { // Task Creation
        $group_id = $_POST['group_id'] ?? null;
        $assigned_by = $_POST['user_id'] ?? null;
        $task_name = $_POST['task_name'] ?? null;
        $task_description = $_POST['task_description'] ?? null;
        $due_date = $_POST['due_date'] ?? null;

        // Validate input
        if (!$group_id || !$assigned_by || !$task_name || !$task_description || !$due_date) {
            header("Location: admin_dashboard.php?page=task_management&error=missing_fields");
            exit();
        }

        // Validate Group ID Exists
        $check_group = $conn->query("SELECT * FROM collaboration_groups WHERE group_id = '$group_id'");
        if ($check_group->num_rows === 0) {
            header("Location: admin_dashboard.php?page=task_management&error=invalid_group");
            exit();
        }

        // Insert Task into Database
        $sql = "INSERT INTO task_management (group_id, assigned_by, task_name, task_description, due_date, status) 
                VALUES ('$group_id', '$assigned_by', '$task_name', '$task_description', '$due_date', 'To-Do')";

        if ($conn->query($sql) === TRUE) {
            header("Location: admin_dashboard.php?page=task_management&success=task_created");
        } else {
            header("Location: admin_dashboard.php?page=task_management&error=db_error");
        }
        exit();
    } elseif (isset($_POST['update_task'])) { // Task Status Update
        $task_id = $_POST['task_id'];
        $status = $_POST['status'];

        $sql = "UPDATE task_management SET status = '$status' WHERE task_id = '$task_id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: admin_dashboard.php?page=task_management&success=task_updated");
        } else {
            header("Location: admin_dashboard.php?page=task_management&error=update_failed");
        }
        exit();
    } elseif (isset($_POST['delete_task'])) { // Task Deletion
        $task_id = $_POST['delete_task'];

        $sql = "DELETE FROM task_management WHERE task_id = '$task_id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: admin_dashboard.php?page=task_management&success=task_deleted");
        } else {
            header("Location: admin_dashboard.php?page=task_management&error=delete_failed");
        }
        exit();
    }
}

// Fetch Tasks for Display
$tasks = [];
$sql = "SELECT t.*, g.group_name FROM task_management t 
        JOIN collaboration_groups g ON t.group_id = g.group_id 
        ORDER BY t.due_date ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
?>

<link rel="stylesheet" href="collaboration/task_management.css">

<div class="task-container">
    <h2>Task Management</h2>

    <?php if (isset($_GET['success'])): ?>
        <p id="success-message" class="success-message">
            <?php 
                if ($_GET['success'] == 'task_created') echo "✅ Task created successfully!";
                if ($_GET['success'] == 'task_updated') echo "✅ Task updated successfully!";
                if ($_GET['success'] == 'task_deleted') echo "✅ Task deleted successfully!";
            ?>
        </p>
        <script>
            setTimeout(function() {
                document.getElementById("success-message").style.display = "none";
            }, 3000); // ✅ Hide message after 3 seconds
        </script>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="error-message">❌ Error: <?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form class="task-form" method="POST">
        <select name="group_id" required>
            <option value="">-- Choose a Group --</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['group_id']; ?>"><?= htmlspecialchars($group['group_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="user_id" value="<?= $user_id; ?>">
        <input type="text" name="task_name" placeholder="Task Name" required>
        <textarea name="task_description" placeholder="Task Description" required></textarea>
        <input type="date" name="due_date" required>
        <button type="submit" class="btn">Create Task</button>
    </form>

    <table class="task-table">
        <thead>
            <tr>
                <th>Task</th>
                <th>Description</th>
                <th>Group</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr style="font-size: 14px;"> <!-- ✅ Reduce font size -->
                    <td><?= htmlspecialchars($task['task_name']); ?></td>
                    <td><?= htmlspecialchars($task['task_description']); ?></td>
                    <td><?= htmlspecialchars($task['group_name']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="task_id" value="<?= $task['task_id']; ?>">
                            <select name="status">
                                <option value="To-Do" <?= $task['status'] == 'To-Do' ? 'selected' : ''; ?>>To-Do</option>
                                <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <button type="submit" name="update_task" class="update-btn">Update</button> <!-- ✅ New Update Button -->
                        </form>
                    </td>
                    <td><?= $task['due_date']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="delete_task" value="<?= $task['task_id']; ?>">
                            <button type="submit" class="delete-btn">❌</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
