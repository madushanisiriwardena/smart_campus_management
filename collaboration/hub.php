<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fix connection path issue
include __DIR__ . '/../connection.php'; // Absolute path to connection.php
?>

<div class="dashboard-container">
    <h2 class="dashboard-title">Collaboration Hub</h2>
    <div class="dashboard-grid">
        <a href="admin_dashboard.php?page=groups" class="dashboard-card">
            <i class="fas fa-users icon"></i>
            <h3>Groups & Discussions</h3>
            <p>Join or create study groups & discussions.</p>
        </a>

        <a href="admin_dashboard.php?page=messaging" class="dashboard-card">
            <i class="fas fa-comments icon"></i>
            <h3>Messaging</h3>
            <p>Chat privately or in group discussions.</p>
        </a>

        <a href="admin_dashboard.php?page=file_sharing" class="dashboard-card">
            <i class="fas fa-folder icon"></i>
            <h3>File Sharing</h3>
            <p>Upload and manage shared resources.</p>
        </a>

        <a href="admin_dashboard.php?page=task_management" class="dashboard-card">
            <i class="fas fa-tasks icon"></i>
            <h3>Task Management</h3>
            <p>Assign, track, and complete group tasks.</p>
        </a>
    </div>
</div>

<style>
    .dashboard-container {
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .dashboard-title {
        font-size: 32px;
        margin-bottom: 20px;
        color: #333;
        font-weight: bold;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .dashboard-card {
        background: linear-gradient(135deg, #4b0082, #8a2be2); 
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        text-align: center;
        text-decoration: none;
        transition: 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .dashboard-card:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }
    .dashboard-card .icon {
        font-size: 50px;
        margin-bottom: 10px;
    }
    .dashboard-card h3 {
        margin: 10px 0;
        font-size: 20px;
    }
    .dashboard-card p {
        font-size: 14px;
        opacity: 0.9;
    }
</style>
