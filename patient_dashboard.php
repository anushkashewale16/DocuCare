<?php
session_start();
require 'db.php'; // ✅ DB connection

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// ✅ Handle report upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_report'])) {
    $appointment_id = intval($_POST['appointment_id']);

    // Check if appointment belongs to this patient
    $check = $conn->query("SELECT * FROM appointments WHERE appointment_id=$appointment_id AND patient_id=$patient_id");
    if ($check->num_rows > 0) {
        if (isset($_FILES['report']) && $_FILES['report']['error'] == 0) {
            $allowed = ['pdf','jpg','jpeg','png'];
            $ext = strtolower(pathinfo($_FILES["report"]["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                header("Location: patient_dashboard.php?msg=invalid_file");
                exit();
            }

            $targetDir = "uploads/reports/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $fileName = time() . "_" . basename($_FILES["report"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["report"]["tmp_name"], $targetFilePath)) {
                $conn->query("UPDATE appointments SET report_path='$targetFilePath' WHERE appointment_id=$appointment_id");
                header("Location: patient_dashboard.php?msg=uploaded");
                exit();
            } else {
                header("Location: patient_dashboard.php?msg=upload_error");
                exit();
            }
        }
    }
}

// ✅ Fetch appointments with doctor name
$appointments = $conn->query("
    SELECT a.*, d.name AS doctor_name 
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id = $patient_id
    ORDER BY a.appointment_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - DocuCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="p-8 md:p-12 bg-gray-100">

    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-xl mb-12">
        <div class="flex items-center gap-4">
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#28a745]">Welcome,</h1>
            <p class="text-2xl font-semibold text-gray-700"><?= $_SESSION['patient_name'] ?></p>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mt-4 md:mt-0">
            <a href="book_appointment.php" class="px-6 py-3 bg-[#28a745] text-white font-semibold rounded-xl shadow-lg hover:scale-105 transition">
                <i class="fas fa-plus mr-2"></i>Book Appointment
            </a>
            <a href="logout.php" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:scale-105 transition">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </header>

    <!-- Dashboard -->
    <main class="bg-white p-6 rounded-2xl shadow-xl">
        <!-- ✅ Upload Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg']=="uploaded"): ?>
                <p class="text-green-600 text-center mb-4">Report uploaded successfully.</p>
            <?php elseif($_GET['msg']=="upload_error"): ?>
                <p class="text-red-500 text-center mb-4">Error uploading report. Try again.</p>
            <?php elseif($_GET['msg']=="invalid_file"): ?>
                <p class="text-red-500 text-center mb-4">Invalid file type. Only PDF, JPG, JPEG, PNG allowed.</p>
            <?php endif; ?>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Appointments</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">Doctor</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Time</th>
                        <th class="py-3 px-4">Symptoms</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Report</th>
                        <th class="py-3 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $appointments->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">Dr. <?= $row['doctor_name'] ?></td>
                        <td class="py-3 px-4"><?= $row['appointment_date'] ?></td>
                        <td class="py-3 px-4"><?= $row['appointment_time'] ?></td>
                        <td class="py-3 px-4"><?= $row['symptoms'] ?></td>
                        <td class="py-3 px-4"><?= $row['status'] ?></td>
                        <td class="py-3 px-4">
                            <?php if($row['report_path']): ?>
                                <a href="<?= $row['report_path'] ?>" target="_blank" class="text-[#28a745] font-semibold hover:underline">View Report</a>
                            <?php else: ?>
                                <form action="patient_dashboard.php" method="post" enctype="multipart/form-data" class="flex gap-2">
                                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                    <input type="file" name="report" accept=".pdf,.jpg,.jpeg,.png" class="text-sm border rounded px-2 py-1" required>
                                    <button type="submit" name="upload_report" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Upload</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <?php if($row['status'] === 'Pending' || $row['status'] === 'Declined'): ?>
                                <a href="patient_dashboard.php?delete_id=<?= $row['appointment_id'] ?>" onclick="return confirm('Delete appointment?');" class="text-red-600 hover:underline">Delete</a>
                            <?php else: ?>
                                <span class="text-gray-500">Not Allowed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
