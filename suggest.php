<?php
// suggest.php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$symptoms = isset($_POST['symptoms']) ? trim($_POST['symptoms']) : '';
if ($symptoms === '') {
    echo json_encode(['success'=>false,'message'=>'No symptoms provided']);
    exit;
}

$sym_lower = mb_strtolower($symptoms, 'UTF-8');

// Fetch all symptom rules
$res = $conn->query("SELECT symptom_keyword, suggested_specialization FROM symptoms_master");
$suggestions = [];

while ($r = $res->fetch_assoc()) {
    $kw = mb_strtolower($r['symptom_keyword'], 'UTF-8');
    if (mb_strpos($sym_lower, $kw) !== false) {
        $suggestions[] = $r['suggested_specialization'];
    }
}

$suggestions = array_values(array_unique($suggestions));

if (count($suggestions) > 0) {
    echo json_encode(['success'=>true,'suggestions'=>$suggestions]);
} else {
    echo json_encode(['success'=>false,'message'=>'No suggestion']);
}
