<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../connection.php';

?>

<link rel="stylesheet" href="collaboration/collaboration.css">

<div class="dashboard-container">
    <h2 class="dashboard-title">Groups & Discussions</h2>
    
    <div class="dashboard-grid">
        <a href="admin_dashboard.php?page=create_group" class="dashboard-card">
            <i class="fas fa-plus-circle icon"></i>
            <h3>Create a Group</h3>
            <p>Start a new study or project group.</p>
        </a>
        <a href="admin_dashboard.php?page=join_group" class="dashboard-card">
            <i class="fas fa-user-plus icon"></i>
            <h3>Join a Group</h3>
            <p>Find and join existing groups.</p>
        </a>
        <a href="admin_dashboard.php?page=manage_groups" class="dashboard-card">
            <i class="fas fa-users-cog icon"></i>
            <h3>Manage Groups</h3>
            <p>View and manage group members.</p>
        </a>
    </div>
</div>
