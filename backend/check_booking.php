
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read input data from POST
$data = json_decode(file_get_contents("php://input"), true);
$resourceId = $data['resource_id'];
$startTime = $data['start_time'];
$endTime = $data['end_time'];


// Check if there's an existing booking for the resource during the selected time slot
$sql = "SELECT * FROM reservations WHERE resource_id = ? AND ((start_time BETWEEN ? AND ?) OR (end_time BETWEEN ? AND ?)) AND status != 'Cancelled'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $resourceId, $startTime, $endTime, $startTime, $endTime);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
// If there's a booking, return error response
echo json_encode(['success' => false]);
} else {
// If no bookings, proceed with the booking
echo json_encode(['success' => true]);
}

$stmt->close();
$conn->close();
?>