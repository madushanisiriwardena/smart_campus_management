<?php
include 'connection.php';

$id = $_GET['id'];

if (isset($_POST['complete'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification']);

    // Insert into lecturer_tb
    $query = "INSERT INTO lecturer_tb (id, faculty, designation, specialization, experience, qualification, status) 
              VALUES ('$id', '$faculty', '$designation', '$specialization', '$experience', '$qualification', 'pending')";

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
    <title>Lecturer Registration</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function updateSpecialization() {
            var faculty = document.getElementById("faculty").value;
            var specializationSelect = document.getElementById("specialization");
            specializationSelect.innerHTML = "";

            var specializations = [];
            if (faculty === "School of Computing") {
                specializations = ["General - Information Technology", "Software Engineering", "Network Engineering", "Data Analytics"];
            } else if (faculty === "School of Business Management") {
                specializations = ["General - Business Management", "Accounting and Finance", "Human Resource Management"];
            }

            specializations.forEach(function(specialization) {
                var option = document.createElement("option");
                option.value = specialization;
                option.textContent = specialization;
                specializationSelect.appendChild(option);
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
        <h2>Lecturer Registration</h2>

        <!-- Message Box -->
        <?php if (isset($success) || isset($error)): ?>
            <div id="message-box" class="<?php echo isset($success) ? 'success-box' : 'error-box'; ?>">
                <span><?php echo isset($success) ? $success : $error; ?></span>
                <button onclick="closeMessage()" class="close-btn">&times;</button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <select name="faculty" id="faculty" onchange="updateSpecialization()" required>
                <option value="">Select Faculty</option>
                <option value="School of Computing">School of Computing</option>
                <option value="School of Business Management">School of Business Management</option>
            </select>

            <select name="designation" required>
                <option value="">Select Designation</option>
                <option value="Assistant Lecturer">Assistant Lecturer</option>
                <option value="Lecturer">Lecturer</option>
                <option value="Senior Lecturer">Senior Lecturer</option>
                <option value="Professor">Professor</option>
            </select>

            <select name="specialization" id="specialization" required>
                <option value="">Select Specialization</option>
            </select>

            <input type="number" name="experience" placeholder="Years of Experience" min="0" required>

            <select name="qualification" required>
                <option value="">Select Highest Qualification</option>
                <option value="Bachelor's Degree">Bachelor's Degree</option>
                <option value="Master's Degree">Master's Degree</option>
                <option value="PhD">PhD</option>
            </select>

            <button type="submit" name="complete">Complete Registration</button>
        </form>
    </div>
</body>
</html>
