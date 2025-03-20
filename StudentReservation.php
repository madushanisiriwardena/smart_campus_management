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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>
<body>
<?php require "includes/topBar.php"; ?>

<div class="container">
    <?php require "includes/adminSideBar.php"; ?>
    <main id="content-area">


        <div style="display: flex;padding: 15px">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                    Select Type
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                    <li><a class="dropdown-item" href="#" onclick="selectItem(this)">Class Room</a></li>
                    <li><a class="dropdown-item" href="#" onclick="selectItem(this)">Equipment</a></li>
                    <li><a class="dropdown-item" href="#" onclick="selectItem(this)">Resources</a></li>
                </ul>
            </div>
        </div>

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col" width="300">Start Date - End Date</th>
                <th scope="col">Type</th>
                <th scope="col">Name</th>
                <th scope="col">User By</th>
                <th scope="col">User type</th>
                <th scope="col">Status</th>
                <th scope="col" width="200">Action</th>
            </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>

    </main>
</div>





<!-- Modal -->
<div class="modal fade" id="p3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">More Details about</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="script.js">
</script>

<script>
    window.onload = loadSReservationData;
</script>
<script type="text/javascript" src="js/bootstrap/bootstrap-dropdown.js"></script>
<script>
    function selectItem(element) {
        document.getElementById("dropdownMenuButton2").textContent = element.textContent;
    }
</script>
</body>
</html>