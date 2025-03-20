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

// Handle Sending Message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $message_text = mysqli_real_escape_string($conn, $_POST['message_text']);
    $receiver_id = $_POST['receiver_id'] ?? null;
    $group_id = $_POST['group_id'] ?? null;
    $file_attachment = '';

    // Handle File Upload
    if (!empty($_FILES['file_attachment']['name'])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES["file_attachment"]["name"]);
        $target_file = $upload_dir . $file_name;
        move_uploaded_file($_FILES["file_attachment"]["tmp_name"], $target_file);
        $file_attachment = $target_file;
    }

    // Insert message into chats table
    $query = "INSERT INTO chats (sender_id, receiver_id, group_id, message_text, file_attachment) 
              VALUES ('$user_id', " . ($receiver_id ? "'$receiver_id'" : "NULL") . ", " . ($group_id ? "'$group_id'" : "NULL") . ", '$message_text', '$file_attachment')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit();
}

// Handle Fetching Messages
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['fetch_messages'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = $_SESSION['user_id'] ?? null;
    $chat_type = $_GET['chat_type'] ?? null;
    $receiver_id = $_GET['receiver_id'] ?? null;
    $group_id = $_GET['group_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["error" => "User not authenticated"]);
        exit();
    }

    if ($chat_type === "individual") {
        // Fetch individual chats between logged-in user and receiver
        $query = "SELECT * FROM chats 
                  WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') 
                     OR (sender_id = '$receiver_id' AND receiver_id = '$user_id') 
                  ORDER BY sent_at ASC";
    } elseif ($chat_type === "group") {
        // Fetch group messages for a specific group
        $query = "SELECT * FROM chats 
                  WHERE group_id = '$group_id' 
                  ORDER BY sent_at ASC";
    } else {
        echo json_encode(["error" => "Invalid chat type"]);
        exit();
    }

    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo json_encode(["error" => mysqli_error($conn)]); // Debugging SQL Errors
        exit();
    }

    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($messages);
    exit();
}
?>

<link rel="stylesheet" href="collaboration/chat.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="chat-container">
    <h2 class="chat-title">Messaging</h2>

    <!-- User Selection -->
    <div class="chat-header">
        <select id="chat-type" onchange="toggleChatType()">
            <option value="individual">Chat with a User</option>
            <option value="group">Chat in a Group</option>
        </select>

        <select id="user-select">
            <option value="">Select User...</option>
            <?php
            $users = mysqli_query($conn, "SELECT id, full_name FROM user_tb WHERE id != '$user_id'");
            while ($row = mysqli_fetch_assoc($users)) {
                echo "<option value='{$row['id']}'>{$row['full_name']}</option>";
            }
            ?>
        </select>

        <select id="group-select" style="display: none;">
            <option value="">Select Group...</option>
            <?php
            $groups = mysqli_query($conn, "SELECT group_id, group_name FROM collaboration_groups");
            while ($row = mysqli_fetch_assoc($groups)) {
                echo "<option value='{$row['group_id']}'>{$row['group_name']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- Chat Box -->
    <div id="chat-box">
        <ul id="chat-messages"></ul>
    </div>

    <!-- Message Input -->
    <form id="chat-form">
        <input type="hidden" id="chat-type-hidden">
        <input type="hidden" id="receiver-id">
        <input type="hidden" id="group-id">

        <div class="chat-input">
            <textarea id="message-input" placeholder="Type a message..." required></textarea>
            <div class="file-upload-container">
                <input type="file" id="file-attachment" onchange="displayFileName()" hidden>
                <label for="file-attachment" class="file-upload-label"><i class="fas fa-upload"></i> Choose File</label>
                <span class="file-name">No file chosen</span>
            </div>
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        loadMessages();  // Load messages on page load

        document.getElementById("chat-type").addEventListener("change", function () {
            toggleChatSelection();
            loadMessages();
        });

        document.getElementById("user-select").addEventListener("change", function () {
            loadMessages();  // Load messages when a user is selected
        });

        document.getElementById("group-select").addEventListener("change", function () {
            loadMessages();  // Load messages when a group is selected
        });

        document.getElementById("chat-form").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent form from refreshing the page
        });
    });

    function toggleChatType() {
        let chatType = document.getElementById("chat-type").value;
        document.getElementById("user-select").style.display = chatType === "individual" ? "block" : "none";
        document.getElementById("group-select").style.display = chatType === "group" ? "block" : "none";
    }

    document.getElementById("chat-form").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent page reload

    let messageText = document.querySelector("#message-input").value.trim(); // Ensure clean input

    if (messageText === "") {
        alert("Please enter a message before sending.");
        return;
    }

    let chatType = document.getElementById("chat-type").value;
    let receiverId = document.getElementById("user-select").value;
    let groupId = document.getElementById("group-select").value;
    let fileInput = document.getElementById("file-attachment").files[0];

    let formData = new FormData();
    formData.append("send_message", "1");
    formData.append("chat_type", chatType);
    formData.append("receiver_id", receiverId);
    formData.append("group_id", groupId);
    formData.append("message_text", messageText);
    if (fileInput) {
        formData.append("file_attachment", fileInput);
    }

    fetch("collaboration/messaging.php", {  // Adjust path as needed
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("message-input").value = ""; // Clear text area after sending
            document.getElementById("file-attachment").value = ""; // Clear file input
            loadMessages(); // Refresh messages
        } else {
            alert("Error sending message. Try again.");
        }
    })
    .catch(error => console.error("Error:", error));
    });


    function loadMessages() {
    let chatType = document.getElementById("chat-type").value;
    let receiverId = document.getElementById("user-select").value;
    let groupId = document.getElementById("group-select").value;

    if (!receiverId && !groupId) return;  // 🔥 Don't fetch if no user/group is selected

    fetch(`collaboration/messaging.php?fetch_messages=1&chat_type=${chatType}&receiver_id=${receiverId}&group_id=${groupId}`)
        .then(response => response.json())
        .then(messages => {
            console.log("Messages from server:", messages); // Debugging output
            let chatBox = document.getElementById("chat-messages");
            chatBox.innerHTML = "";  // Clear old messages
            
            if (messages.error) {
                console.error("Error from server:", messages.error);
                return;
            }

            let lastDate = ""; // Track last displayed date

            messages.forEach(msg => {
                let messageDate = new Date(msg.sent_at);
                let formattedDate = messageDate.toLocaleDateString();
                let formattedTime = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // Display Date Header if it's a new day
                if (formattedDate !== lastDate) {
                    let dateElement = document.createElement("div");
                    dateElement.classList.add("date-header");
                    dateElement.textContent = formattedDate;
                    chatBox.appendChild(dateElement);
                    lastDate = formattedDate;
                }

                let msgClass = (msg.sender_id == <?php echo $user_id; ?>) ? 'sent' : 'received';
                let messageElement = document.createElement("div");
                messageElement.classList.add("message-box", msgClass);

                // Check if there's a file attached
                let fileLink = "";
                if (msg.file_attachment) {
                    fileLink = `<br><a href="collaboration/${msg.file_attachment}" target="_blank" class="chat-file">📎 Download File</a>`;
                }

                messageElement.innerHTML = `<span class="message-text">${msg.message_text}</span> ${fileLink} <span class="timestamp">${formattedTime}</span>`;

                chatBox.appendChild(messageElement);
            });

            // Scroll to the bottom after loading messages
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => console.error("Error fetching messages:", error));
}




    function displayFileName() {
        let fileInput = document.getElementById("file-attachment");
        let fileNameDisplay = document.querySelector(".file-name");
        
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = "No file chosen";
        }
    }
 
    function sendMessage() {
        let messageText = document.getElementById("message-input").value.trim();
        let chatType = document.getElementById("chat-type").value;
        let receiverId = document.getElementById("user-select").value;
        let groupId = document.getElementById("group-select").value;
        let fileInput = document.getElementById("file-attachment").files[0];

        if (!messageText) return alert("Please enter a message");

        let formData = new FormData();
        formData.append("send_message", "1");
        formData.append("chat_type", chatType);
        formData.append("receiver_id", receiverId);
        formData.append("group_id", groupId);
        formData.append("message_text", messageText);
        if (fileInput) formData.append("file_attachment", fileInput);

        fetch("messaging.php", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("message-input").value = "";
                    document.getElementById("file-attachment").value = "";
                    loadMessages(); // Reload messages after sending
                } else {
                    alert("Error sending message");
                }
            })
            .catch(error => console.error("Error:", error));
    }

</script>