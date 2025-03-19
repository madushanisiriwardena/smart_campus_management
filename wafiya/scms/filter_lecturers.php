<?php
include 'connection.php';

$designation = $_GET['designation'] ?? '';
$specialization = $_GET['specialization'] ?? '';
$faculty = $_GET['faculty'] ?? '';

$query = "SELECT u.full_name, u.email, u.contact, u.dob, u.gender, l.faculty, l.designation, l.specialization, l.qualification 
          FROM user_tb u 
          JOIN lecturer_tb l ON u.id = l.id 
          WHERE u.status = 'approved' AND u.role = 'lecturer' AND l.faculty = '$faculty'";

if ($designation) {
    $query .= " AND l.designation = '$designation'";
}
if ($specialization) {
    $query .= " AND l.specialization = '$specialization'";
}

$result = mysqli_query($conn, $query);

echo '<table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Designation</th>
                <th>Specialization</th>
                <th>Qualification</th>
            </tr>
        </thead>
        <tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['full_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['contact']}</td>
            <td>{$row['dob']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['designation']}</td>
            <td>{$row['specialization']}</td>
            <td>{$row['qualification']}</td>
          </tr>";
}

echo '</tbody></table>';
?>