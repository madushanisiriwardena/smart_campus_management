<?php
session_start();
require "connection.php";
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php require "includes/topBar.php"; ?>

<div class="container">
    <?php require "includes/adminSideBar.php"; ?>
    <main id="content-area">


        <div style="display: flex;justify-content: end;">
            <button class="btn btn-primary">Add +</button>
        </div>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Code</th>
                <th scope="col">Name</th>
                <th scope="col">Max Count</th>
                <th scope="col">Status</th>
                <th scope="col" width="200">Action</th>
            </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>



    </main>
</div>

<script src="script.js"></script>
<script>
    window.onload = loadResourcesData;
</script>
</body>
</html>