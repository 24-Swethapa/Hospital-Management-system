<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("
    SELECT PatientID, Name, Disease, AdmittedDate
    FROM patients
    ORDER BY PatientID DESC
");

if (!$result) {
    die("Error loading patients: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Patients</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #eef5fb;
}

.navbar {
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    padding: 18px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 22px;
    font-weight: bold;
}

.navbar a {
    color: white;
    text-decoration: none;
    background: rgba(255,255,255,0.15);
    padding: 9px 15px;
    border-radius: 7px;
    font-weight: bold;
}

.navbar a:hover {
    background: rgba(255,255,255,0.25);
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 35px auto;
}

.page-title {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
    border-left: 6px solid #1976d2;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.page-title h1 {
    margin: 0 0 8px;
    color: #12355b;
}

.page-title p {
    margin: 0;
    color: #777;
}

.table-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #1976d2;
    color: white;
    padding: 14px;
    text-align: left;
}

td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    color: #444;
}

tr:hover {
    background: #f5f9fd;
}

.edit-btn {
    background: #198754;
    color: white;
    text-decoration: none;
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 13px;
}

.edit-btn:hover {
    background: #146c43;
}

.no-records {
    text-align: center;
    padding: 30px;
    color: #777;
    font-size: 16px;
}

.footer {
    margin-top: 50px;
    background: #12355b;
    color: white;
    text-align: center;
    padding: 20px;
}

</style>

</head>

<body>

<div class="navbar">

    <div class="logo">
        🏥 Hospital Management System
    </div>

    <a href="index.php">
        Dashboard
    </a>

</div>


<div class="container">

    <div class="page-title">

        <h1>👤 Patient Records</h1>

        <p>
            View all registered patients and their details.
        </p>

    </div>


    <div class="table-card">

        <?php if ($result->num_rows > 0) { ?>

        <table>

            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Disease</th>
                <th>Admitted Date</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?= htmlspecialchars($row['PatientID']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['Name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['Disease']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['AdmittedDate']) ?>
                </td>

                <td>
                    <a
                        href="edit_patient.php?id=<?= $row['PatientID'] ?>"
                        class="edit-btn"
                    >
                        Edit
                    </a>
                </td>

            </tr>

            <?php } ?>

        </table>

        <?php } else { ?>

        <div class="no-records">
            No patient records found.
        </div>

        <?php } ?>

    </div>

</div>


<div class="footer">
    Hospital Management System 2026
</div>

</body>
</html>