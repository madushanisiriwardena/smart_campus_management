<?php
include 'connection.php';

$level = $_GET['level'] ?? '';
$course = $_GET['course'] ?? '';
$faculty = $_GET['faculty'] ?? '';

$query = "SELECT u.full_name, u.email, u.contact, u.dob, u.gender, s.level, s.faculty, s.course 
          FROM user_tb u 
          JOIN student_tb s ON u.id = s.id 
          WHERE u.status = 'approved' AND u.role = 'student' AND s.faculty = '$faculty'";

if ($level) {
    $query .= " AND s.level = '$level'";
}
if ($course) {
    $query .= " AND s.course = '$course'";
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
                <th>Level</th>
                <th>Course</th>
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
            <td>{$row['level']}</td>
            <td>{$row['course']}</td>
          </tr>";
}

echo '</tbody></table>';
?>