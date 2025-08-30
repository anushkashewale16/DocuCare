<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid login!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuCare - Admin Login</title>
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
            background-image: linear-gradient(to right, #005f99, #004b7c);
            transition: all 0.3s ease-in-out;
        }
        .gradient-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 95, 153, 0.2);
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
     <!-- Back Button -->
        <button onclick="window.history.back()" class="absolute top-6 left-6 text-gray-500 hover:text-gray-900 transition duration-300 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>   
        <div class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-[#004b7c] leading-tight mb-2 tracking-wide">Admin Login</h1>
            <p class="text-lg text-gray-600">Please enter your credentials.</p>
        </div>

        <form id="loginForm" method="POST" action="">
    <div>
        <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
        <input type="text" id="username" name="username" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#004b7c]" required>
    </div>
    <div>
        <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#004b7c]" required>
    </div>
     <button type="submit" class="gradient-button w-full text-center py-4 px-6 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-transform duration-300">
        Login
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

        // Prevent the form from actually submitting
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // In a real application, you would send this data to a server for authentication.
            // For this demonstration, we'll just show a success message.
            if (username && password) {
                showMessage("Login successful! Redirecting...");
                // Here you would add code to redirect the user to the admin dashboard.
                // e.g., window.location.href = 'admin_dashboard.html';
            } else {
                showMessage("Please fill out both fields.", 5000);
            }
        });
    </script>
</body>
</html>
