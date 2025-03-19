<?php
include 'connection.php';

$id = $_GET['id'];

if (isset($_POST['complete'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    // Insert into student_tb
    $query = "INSERT INTO student_tb (id, level, faculty, course, status) 
              VALUES ('$id', '$level', '$faculty', '$course', 'pending')";

    if (mysqli_query($conn, $query)) {
        $success = "Registration request sent successfully! Admin will review your details and approve soon.";
    } else {
        $error = "Something went wrong. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function updateCourses() {
            var faculty = document.getElementById("faculty").value;
            var courseSelect = document.getElementById("course");
            courseSelect.innerHTML = "";

            if (faculty === "School of Business Management") {
                var courses = ["Business Management - General", "Accounting and Finance", "Human Resource Management"];
            } else if (faculty === "School of Computing") {
                var courses = ["Information Technology - General", "Software Engineering", "Network Engineering", "Data Analytics"];
            } else {
                var courses = [];
            }

            courses.forEach(function(course) {
                var option = document.createElement("option");
                option.value = course;
                option.textContent = course;
                courseSelect.appendChild(option);
            });
        }

        function closeMessage() {
            var messageBox = document.getElementById("message-box");
            messageBox.style.display = "none";
        }
    </script>
</head>
<body>
    <div class="left">
        <img src="images/campus.jpeg" alt="Campus Image">
    </div>
    <div class="right">
        <h2>Student Registration</h2>

        <!-- Message Box -->
        <?php if (isset($success) || isset($error)): ?>
            <div id="message-box" class="<?php echo isset($success) ? 'success-box' : 'error-box'; ?>">
                <span><?php echo isset($success) ? $success : $error; ?></span>
                <button onclick="closeMessage()" class="close-btn">&times;</button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <select name="level" required>
                <option value="">Select Level</option>
                <option value="Diploma Level">Diploma Level</option>
                <option value="Higher National Diploma Level">Higher National Diploma Level</option>
                <option value="Undergraduate Level">Undergraduate Level</option>
            </select>

            <select name="faculty" id="faculty" onchange="updateCourses()" required>
                <option value="">Select Faculty</option>
                <option value="School of Computing">School of Computing</option>
                <option value="School of Business Management">School of Business Management</option>
            </select>

            <select name="course" id="course" required>
                <option value="">Select Course</option>
            </select><br>

            <button type="submit" name="complete">Complete Registration</button>
        </form>
    </div>
</body>
</html>