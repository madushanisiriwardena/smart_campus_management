<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../connection.php';

$user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] == 'admin');
?>

<link rel="stylesheet" href="collaboration/file_sharing.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload_file'])) {
        // Debugging: Check if file data is received
        error_log(print_r($_FILES, true));
        error_log(print_r($_POST, true));

        if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
            echo json_encode(["error" => "File upload failed."]);
            exit();
        }

        $file = $_FILES['file'];
        $filePath = "uploads/" . basename($file['name']); // Ensure uploads folder is correct

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $visibility_scope = $_POST['visibility_scope'];

            // Insert into shared_files table
            $insertQuery = "INSERT INTO shared_files (uploaded_by, file_name, file_path, visibility_scope)
                            VALUES ('$user_id', '{$file['name']}', '$filePath', '$visibility_scope')";
            if (mysqli_query($conn, $insertQuery)) {
                $file_id = mysqli_insert_id($conn);

                // If public, grant view access to all users
                if ($visibility_scope == "public") {
                    $all_users = mysqli_query($conn, "SELECT id FROM user_tb WHERE id != '$user_id'");
                    while ($user = mysqli_fetch_assoc($all_users)) {
                        mysqli_query($conn, "INSERT INTO file_access_control (file_id, user_id, permission, granted_by)
                                             VALUES ('$file_id', '{$user['id']}', 'view', '$user_id')");
                    }
                }
                echo json_encode(["success" => "File uploaded successfully."]);
                exit();
            } else {
                echo json_encode(["error" => "Database insertion failed."]);
                exit();
            }
        } else {
            echo json_encode(["error" => "File move failed."]);
            exit();
        }
    }

    // Handle file deletion
    /*if (isset($_POST['delete_file'])) {
        $file_id = $_POST['delete_file'];
        mysqli_query($conn, "DELETE FROM shared_files WHERE file_id='$file_id'");
        exit();
    }*/

    if (isset($_POST['delete_file'])) {
        $file_id = intval($_GET['file_id']); // Sanitize input
        $sql = "DELETE FROM files WHERE id = ?"; // Adjust table name if different
    
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $file_id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "Failed to prepare statement"]);
        }
        exit();
    }

}
?>

<div class="file-sharing-container">
    <h2><i class="fas fa-folder"></i> File Sharing</h2>

    <!-- Upload Section -->
    <div class="upload-section">
        <form id="uploadForm" enctype="multipart/form-data">
            <label for="fileInput">Upload a File:</label>
            <input type="file" id="fileInput" name="file" required>
            <select id="visibilityScope">
                <option value="private">Private</option>
                <option value="public">Public</option>
            </select>
            <button type="submit">Upload</button>
        </form>
        <div id="upload-status"></div>
    </div>

    <!-- Manage Files -->
    <div class="uploaded-files">
        <h3>Manage Files</h3>
        <table class="file-table">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Visibility</th>
                <th>Actions</th>
            </tr>
        </thead>
            <?php
            $query = "SELECT * FROM shared_files WHERE uploaded_by = '$user_id'";
            $result = mysqli_query($conn, $query);
            while ($file = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><a href='{$file['file_path']}' target='_blank'>{$file['file_name']}</a></td>";
                echo "<td>{$file['visibility_scope']}</td>";
                echo "<td>
                    <button class='delete-btn' onclick='deleteFile({$file['file_id']})'>Delete</button>
                    <a href='admin_dashboard.php?page=manage_access&file_id={$file['file_id']}' class='manage-access-btn'>Manage Access</a>
                </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>

<script>
// File Upload AJAX
document.getElementById("uploadForm").onsubmit = function(event) {
    event.preventDefault();

    let fileInput = document.getElementById("fileInput").files[0];
    let visibilityScope = document.getElementById("visibilityScope").value;

    // Debugging: Check if elements exist and have values
    console.log("Uploading File: ", fileInput);
    console.log("Visibility Scope: ", visibilityScope);

    if (!fileInput) {
        alert("Please select a file to upload.");
        return;
    }

    let formData = new FormData();
    formData.append("file", fileInput);
    formData.append("visibility_scope", visibilityScope);
    formData.append("upload_file", "1");

    fetch("collaboration/file_sharing.php", { // Ensure correct path
        method: "POST",
        body: formData
    }).then(response => response.text()).then(data => {
        console.log("Server Response:", data); 
        document.getElementById("upload-status").innerHTML = "<p style='color:green;'>File uploaded successfully!</p>";
        location.reload();
    }).catch(error => {
        console.error("Upload Failed:", error);
    });
};

// Delete File AJAX
function deleteFile(fileId) {
    if (confirm("Are you sure you want to delete this file?")) {
        fetch("file_sharing.php", {
            method: "POST",
            body: new URLSearchParams({ "delete_file": fileId })
        }).then(() => location.reload());
    }
}
</script>
