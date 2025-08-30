<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit();
}
require 'db.php';

$appointment_id = intval($_GET['id']);
$doctor_id = $_SESSION['doctor_id'];

// Fetch the report text
$res = $conn->query("SELECT report_text, patient_id FROM appointments 
                     WHERE appointment_id=$appointment_id AND doctor_id=$doctor_id AND status='Accepted'");
if ($res->num_rows == 0) {
    die("No report found or not authorized.");
}
$row = $res->fetch_assoc();
$report_text = trim($row['report_text']);

if (empty($report_text)) {
    $summary = "Report text is empty. Cannot generate AI summary.";
} else {
    // API configuration
    $apiKey = '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';
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $data = [
        "model" => "mistralai/mistral-7b-instruct:free",
        "messages" => [
            ["role"=>"system","content"=>"You are a helpful medical assistant."],
            ["role"=>"user","content"=>"Summarize this medical report in bullet points for the doctor:\n\n".$report_text]
        ],
        "temperature" => 0.5
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // avoid SSL issues on InfinityFree
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if ($response === false) {
        $summary = "Error calling AI API: " . curl_error($ch);
    } else {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $summary = $result['choices'][0]['message']['content'];
        } else {
            $summary = "AI API returned unexpected response.";
        }
    }

    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">AI-Generated Summary</h1>
        
        <!-- The summary content area -->
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Summary Details</h3>
            <p class="text-gray-600 leading-relaxed" id="summary-content">
                <?php
                    // This is the PHP code provided by the user, dynamically displaying the summary.
                    // The nl2br and htmlspecialchars functions are used to ensure proper formatting and security.
                    echo nl2br(htmlspecialchars($summary));
                ?>
            </p>
        </div>

        <!-- Back button -->
        <div class="mt-8 text-center">
            <a href="doctor_dashboard.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>

