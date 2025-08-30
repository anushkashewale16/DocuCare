<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Check if phone or email already exists
    $check = $conn->prepare("SELECT patient_id FROM patients WHERE phone=? OR email=? LIMIT 1");
    $check->bind_param("ss", $phone, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = "Phone number or email already registered. Please login.";
    } else {
        // hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO patients (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $password_hash);

        if ($stmt->execute()) {
            $msg = "Registration successful! <a href='patient_login.php'>Login here</a>";
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuCare - Patient Registration</title>
    <!-- Use the Inter font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* General body styling for the font family and background */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
        }

        /* Custom button styling for a gradient and shadow effect */
        .gradient-button {
            background-image: linear-gradient(to right, #28a745, #14a79c);
            transition: all 0.3s ease-in-out;
        }
        .gradient-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
        }

        /* Message box for notifications */
        .message-box {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            display: none;
            background-color: #fff;
            color: #333;
        }
    </style>
</head>
<body class="p-4">
    <!-- Message box for notifications -->
    <div id="messageBox" class="message-box"></div>

    <div class="w-full max-w-sm bg-white p-8 md:p-12 rounded-3xl shadow-2xl backdrop-filter backdrop-blur-lg bg-opacity-70">
        <div class="text-center mb-10">
    <!-- Back Button -->
        <button onclick="window.history.back()" class="absolute top-6 left-6 text-gray-500 hover:text-gray-900 transition duration-300 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button> 
            <h1 class="text-5xl font-extrabold text-[#28a745] leading-tight mb-2 tracking-wide">Patient Registration</h1>
            <p class="text-lg text-gray-600">Create your account to get started.</p>
        </div>

        <form id="registrationForm" method="post" action="" class="flex flex-col space-y-6">
            <div>
                <label for="name" class="block text-gray-700 font-semibold mb-2">Full Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#28a745]" placeholder="Full Name" required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#28a745]" placeholder="Email" required>
            </div>
            <div>
                <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone</label>
                <input type="text" id="phone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#28a745]" placeholder="Phone" required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#28a745]" placeholder="Password" required>
            </div>
            <button type="submit" class="gradient-button w-full text-center py-4 px-6 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-transform duration-300">
                Register
            </button>
            <button type="button" onclick="history.back()" class="w-full text-center py-4 px-6 text-gray-600 font-semibold rounded-2xl transition-colors duration-300 hover:text-[#28a745]">
                Back
            </button>
        </form>
    </div>

    <script>
        /**
         * Shows a message to the user in a styled box.
         * @param {string} message The text to display.
         * @param {number} duration The duration in milliseconds to show the message.
         */
        function showMessage(message, duration = 3000) {
            const messageBox = document.getElementById('messageBox');
            messageBox.textContent = message;
            messageBox.style.display = 'block';
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, duration);
        }

        
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            
            // In a real application, you would send this data to a server for registration.
            // For this demonstration, we'll just show a success message.
            if (name && email && phone && password) {
                showMessage("Registration successful! Redirecting...");
                // Here you would add code to redirect the user to a success page or login page.
                // e.g., window.location.href = 'patient_login.html';
            } else {
                showMessage("Please fill out all fields.", 5000);
            }
        });
    </script>
</body>
</html>
