<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}
require 'db.php';

$patient_id = $_SESSION['patient_id'];

// Fetch doctors
$doctors_res = $conn->query("SELECT doctor_id, name, specialization FROM doctors ORDER BY name ASC");
$doctors = [];
while ($d = $doctors_res->fetch_assoc()) {
    $doctors[] = $d;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $symptoms = $_POST['symptoms'];
    $report_text = $_POST['report_text'] ?? null;

    // Handle PDF upload
    $report_path = null;
    if (!empty($_FILES['report']['name'])) {
        $target_dir = "uploads/reports/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES["report"]["name"]));
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["report"]["tmp_name"], $target_file)) {
            $report_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO appointments 
        (patient_id, doctor_id, appointment_date, appointment_time, symptoms, report_path, report_text) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $symptoms, $report_path, $report_text);

    if ($stmt->execute()) $msg = "Appointment booked successfully!";
    else $msg = "Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Book Appointment</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-3xl mx-auto p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-800">📅 Book Appointment</h2>
      <a href="patient_dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>

    <!-- Status Message -->
    <?php if($msg): ?>
      <div class="mb-4 p-3 rounded-lg text-white <?= strpos($msg,'successfully')!==false ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
      <form id="bookingForm" method="POST" enctype="multipart/form-data" class="space-y-5">
        
        <!-- Doctor Select -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Choose Doctor</label>
          <select name="doctor_id" id="doctorSelect" required 
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
            <?php foreach($doctors as $doc): ?>
              <option value="<?= $doc['doctor_id'] ?>">
                Dr. <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['specialization']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Date & Time -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date" name="appointment_date" required 
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
            <input type="time" name="appointment_time" required 
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
          </div>
        </div>

        <!-- Symptoms -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Symptoms</label>
          <textarea id="symptomsInput" name="symptoms" rows="3" required
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2"></textarea>
          <small id="suggestionBox" class="text-blue-600 mt-1 block"></small>
        </div>

        <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Report (PDF only)</label>
                    <label for="reportFile" class="flex items-center justify-center w-full px-4 py-3 bg-gray-50 rounded-xl border border-dashed border-gray-300 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span id="fileUploadText" class="text-gray-600 text-sm font-medium">Click to upload a PDF file</span>
                        <input type="file" id="reportFile" name="report" accept="application/pdf" class="hidden">
                    </label>
                </div>

                <textarea name="report_text" id="reportText" hidden></textarea>

        <!-- Submit -->
        <div class="pt-2">
          <button type="submit" 
            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Book Appointment
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
  <script>
  const doctors = <?= json_encode($doctors, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
  const specMap = {};
  doctors.forEach(d => {
      const spec = d.specialization || 'General';
      if (!specMap[spec]) specMap[spec] = [];
      specMap[spec].push(d);
  });

  function debounce(fn, delay){ let t; return function(...args){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args),delay); }; }
  const suggestionBox = document.getElementById('suggestionBox');
  const symptomsInput = document.getElementById('symptomsInput');
  const doctorSelect = document.getElementById('doctorSelect');

  async function fetchSuggestion(text) {
      const form = new FormData();
      form.append('symptoms', text);
      try {
          const res = await fetch('suggest.php', { method: 'POST', body: form });
          return await res.json();
      } catch (e) { return { success:false }; }
  }

  const handleSuggest = debounce(async function() {
      const txt = symptomsInput.value.trim();
      if (!txt) { suggestionBox.textContent=''; return; }
      suggestionBox.textContent = '🔎 Checking suggestions...';
      const data = await fetchSuggestion(txt);
      if (data.success && data.suggestions && data.suggestions.length>0) {
          const spec = data.suggestions[0];
          suggestionBox.textContent = '💡 Suggested specialization: ' + spec;
          if (specMap[spec] && specMap[spec].length>0) {
              const docId = specMap[spec][0].doctor_id;
              doctorSelect.value = docId;
              suggestionBox.textContent += ' → Suggested doctor: Dr. ' + specMap[spec][0].name;
          }
      } else suggestionBox.textContent = 'No specific suggestion. Choose preferred doctor.';
  }, 600);

  symptomsInput.addEventListener('input', handleSuggest);
  symptomsInput.addEventListener('blur', handleSuggest);

  // PDF.js extract text
  document.querySelector('input[name="report"]').addEventListener('change', function(e){
      const file = e.target.files[0];
      if (!file || file.type !== 'application/pdf') return;
      const reader = new FileReader();
      reader.onload = function(){
          const typedarray = new Uint8Array(this.result);
          pdfjsLib.getDocument(typedarray).promise.then(pdf=>{
              let textContent = "";
              let promises = [];
              for(let i=1;i<=pdf.numPages;i++){
                  promises.push(pdf.getPage(i).then(page=>page.getTextContent().then(tc=>tc.items.forEach(item=>textContent+=item.str+" "))));
              }
              Promise.all(promises).then(()=>{ document.getElementById("reportText").value = textContent; });
          });
      };
      reader.readAsArrayBuffer(file);
  });
  </script>
</body>
</html>
