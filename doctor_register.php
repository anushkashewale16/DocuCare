<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require 'db.php';

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check duplicate email
    $check = $conn->query("SELECT * FROM doctors WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = "Doctor with this email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO doctors (name, email, specialization, password) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $specialization, $password);
        if ($stmt->execute()) {
            $msg = "Doctor registered successfully!";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Doctor</title>
</head>
<body>
<h2>Register New Doctor</h2>
<a href="admin_dashboard.php">⬅ Back to Dashboard</a>
<br><br>

<?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Specialization:</label><br>
    <input type="text" name="specialization" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register Doctor</button>
</form>
</body>
</html>
