<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit();
}
require 'db.php';

$doctor_id = $_SESSION['doctor_id'];

// Handle accept/decline/cancel
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE appointments SET status='Accepted', accepted_at=NOW() WHERE appointment_id=? AND doctor_id=?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE appointments SET status='Declined', updated_at=NOW() WHERE appointment_id=? AND doctor_id=?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id=? AND doctor_id=?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
    }
    header("Location: doctor_dashboard.php");
    exit();
}

// Fetch appointments
$pending = $conn->query("SELECT a.*, p.name AS patient_name FROM appointments a 
                         JOIN patients p ON a.patient_id=p.patient_id
                         WHERE a.doctor_id=$doctor_id AND a.status='Pending'");

$accepted = $conn->query("SELECT a.*, p.name AS patient_name FROM appointments a 
                          JOIN patients p ON a.patient_id=p.patient_id
                          WHERE a.doctor_id=$doctor_id AND a.status='Accepted'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <!-- Google Fonts for clean, modern typography -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">

    <!-- Material Icons for intuitive action buttons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #f6f9fb;
            margin: 0;
            padding: 0;
            color: #222;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(40, 50, 70, 0.08);
            padding: 32px 40px;
        }
        h2 {
            font-weight: 700;
            font-size: 2.1em;
            margin-bottom: 10px;
            color: #2f5464;
        }
        h3 {
            color: #2388ad;
            margin-top: 40px;
            font-size: 1.4em;
        }
        .logout {
            float: right;
            background: #e04a2b;
            color: #fff;
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.16s;
        }
        .logout:hover {
            background: #c43317;
        }

        table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
            background: #fafcff;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e0e7ef;
            text-align: left;
        }
        th {
            background: #f3f7fb;
            color: #41729f;
            font-size: 1.06em;
        }
        tr:hover {
            background: #e7f2fa;
            transition: background 0.15s;
        }

        .action-btn {
            text-decoration: none;
            background: #2388ad;
            color: #fff;
            border-radius: 5px;
            padding: 7px 16px;
            margin: 0 3px;
            font-size: 1em;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: background 0.15s;
        }
        .action-btn.accept { background: #29bc8a; }
        .action-btn.accept:hover { background: #21916a; }
        .action-btn.decline { background: #e78e1c; }
        .action-btn.decline:hover { background: #ba730e; }
        .action-btn.cancel { background: #e04a2b; }
        .action-btn.cancel:hover { background: #c43317; }

        .action-btn .material-icons {
            font-size: 16px;
            margin-right: 4px;
        }

        @media (max-width: 800px) {
            .container {
                padding: 18px 8px;
            }
            table, th, td {
                font-size: 0.98em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a class="logout" href="logout.php">Logout</a>
        <h2>Welcome, Dr. <?= $_SESSION['doctor_name'] ?></h2>
        
        <h3>Pending Appointments</h3>
        <table>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Symptoms</th>
                <th>Action</th>
            </tr>
            <?php while($row = $pending->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['patient_name'] ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                    <td><?= $row['appointment_time'] ?></td>
                    <td><?= $row['symptoms'] ?></td>
                    <td>
                        <a class="action-btn accept" href="?action=accept&id=<?= $row['appointment_id'] ?>">
                            <span class="material-icons">check_circle</span> Accept
                        </a>
                        <a class="action-btn decline" href="?action=decline&id=<?= $row['appointment_id'] ?>">
                            <span class="material-icons">cancel</span> Decline
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <h3>Accepted Appointments (Reports Available)</h3>
        <table>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Symptoms</th>
                <th>Report</th>
                <th>AI Summary</th>
                <th>Action</th>
            </tr>
            <?php while($row = $accepted->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['patient_name'] ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                    <td><?= $row['appointment_time'] ?></td>
                    <td><?= $row['symptoms'] ?></td>
                    <td>
                        <?php if($row['report_path']): ?>
                            <a class="action-btn" href="<?= $row['report_path'] ?>" target="_blank">
                                <span class="material-icons">download</span> Download
                            </a>
                        <?php else: ?>
                            <span style="color:#e04a2b">No report uploaded</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!empty($row['report_text'])): ?>
                            <a class="action-btn" href="ai_summary.php?id=<?= $row['appointment_id'] ?>" target="_blank">
                                <span class="material-icons">summarize</span> View Summary
                            </a>
                        <?php else: ?>
                            <span style="color:#999;">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="action-btn cancel" href="?action=cancel&id=<?= $row['appointment_id'] ?>" onclick="return confirm('Cancel this appointment?');">
                            <span class="material-icons">delete</span> Cancel
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
