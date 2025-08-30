<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require 'db.php';

// ✅ Fetch data
$doctors = $conn->query("SELECT * FROM doctors");
$patients = $conn->query("SELECT * FROM patients");
$appointments = $conn->query("SELECT a.*, p.name AS patient_name, d.name AS doctor_name 
                              FROM appointments a
                              JOIN patients p ON a.patient_id=p.patient_id
                              JOIN doctors d ON a.doctor_id=d.doctor_id
                              ORDER BY a.appointment_date DESC, a.appointment_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DocuCare</title>
    <!-- Use the Inter font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQqG9g6vGqU2P0zP0E9O9uP2zI7aN6j1iW6d+13s4+8A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="p-8 md:p-12 bg-gray-100">

    <!-- Admin Header Section -->
    <header class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-xl mb-12">
        <div class="flex items-center gap-4">
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#004b7c]">Welcome, Admin</h1>
            <!-- PHP variable placeholder for username -->
            <p class="text-2xl font-semibold text-gray-700"><?= $_SESSION['admin_username'] ?></p>
        </div>
        <a href="logout.php" class="mt-4 md:mt-0 px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 hover:bg-red-700">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </header>

    <!-- Dashboard Content Sections -->
    <main class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Doctors Section -->
        <section class="bg-white p-6 rounded-2xl shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">All Doctors</h2>
                <a href="doctor_register.php" class="px-4 py-2 bg-[#28a745] text-white font-medium rounded-lg shadow-md transition-transform duration-200 hover:scale-105 hover:bg-[#14a79c]">
                    <i class="fas fa-plus mr-2"></i>Add New Doctor
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">ID</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Name</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Email</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Specialization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop placeholder for doctor data -->
                        <?php while($row = $doctors->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['doctor_id'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['name'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['email'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['specialization'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Patients Section -->
        <section class="bg-white p-6 rounded-2xl shadow-xl">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">All Patients</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">ID</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Name</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop placeholder for patient data -->
                        <?php while($row = $patients->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['patient_id'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['name'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['email'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Appointments Section -->
        <section class="bg-white p-6 rounded-2xl shadow-xl lg:col-span-3">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">All Appointments</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">ID</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Patient</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Doctor</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Date</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Time</th>
                            <th class="py-3 px-4 text-sm font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop placeholder for appointment data -->
                        <?php while($row = $appointments->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['appointment_id'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['patient_name'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['doctor_name'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['appointment_date'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['appointment_time'] ?></td>
                            <td class="py-3 px-4 text-sm text-gray-800"><?= $row['status'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>
</html>

