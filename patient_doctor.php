<?php
include 'db.php';
session_start();

/* Check login */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

/* Assign doctor to patient */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_doctor'])) {

    $patient_id = intval($_POST['patient_id']);
    $doctor_id = intval($_POST['doctor_id']);
    $assigned_date = $_POST['assigned_date'];

    /* Check whether the same patient-doctor assignment already exists */
    $check_stmt = $conn->prepare(
        "SELECT AssignmentID
         FROM patient_doctor
         WHERE PatientID = ? AND DoctorID = ?"
    );

    $check_stmt->bind_param("ii", $patient_id, $doctor_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {

        $message = "This doctor is already assigned to this patient.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO patient_doctor
             (PatientID, DoctorID, AssignedDate)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "iis",
            $patient_id,
            $doctor_id,
            $assigned_date
        );

        if ($stmt->execute()) {
            $message = "Doctor assigned successfully!";
            $message_type = "success";
        } else {
            $message = "Unable to assign doctor.";
            $message_type = "error";
        }

        $stmt->close();
    }

    $check_stmt->close();
}

/* Delete assignment */
if (isset($_GET['delete'])) {

    $assignment_id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "DELETE FROM patient_doctor
         WHERE AssignmentID = ?"
    );

    $stmt->bind_param("i", $assignment_id);

    if ($stmt->execute()) {
        $message = "Assignment removed successfully!";
        $message_type = "success";
    } else {
        $message = "Unable to remove assignment.";
        $message_type = "error";
    }

    $stmt->close();
}

/* Get patients */
$patients = $conn->query(
    "SELECT PatientID, Name, Disease
     FROM patients
     ORDER BY Name ASC"
);

/* Get doctors */
$doctors = $conn->query(
    "SELECT DoctorID, Name, Specialization
     FROM doctors
     ORDER BY Name ASC"
);

/* Get all assignments */
$assignments = $conn->query(
    "SELECT
        patient_doctor.AssignmentID,
        patients.Name AS PatientName,
        patients.Disease,
        doctors.Name AS DoctorName,
        doctors.Specialization,
        patient_doctor.AssignedDate
     FROM patient_doctor
     INNER JOIN patients
        ON patient_doctor.PatientID = patients.PatientID
     INNER JOIN doctors
        ON patient_doctor.DoctorID = doctors.DoctorID
     ORDER BY patient_doctor.AssignmentID DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Patient - Doctor Assignment</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #eef5fb;
    color: #333;
}

/* Navbar */

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

.navbar-links {
    display: flex;
    gap: 10px;
}

.navbar-links a {
    color: white;
    text-decoration: none;
    padding: 9px 15px;
    border-radius: 7px;
    font-size: 14px;
    font-weight: bold;
}

.navbar-links a:hover {
    background: rgba(255,255,255,0.18);
}

.logout-btn {
    background: #e53935;
}

.logout-btn:hover {
    background: #c62828 !important;
}

/* Main container */

.container {
    width: 90%;
    max-width: 1200px;
    margin: 35px auto;
}

/* Page heading */

.page-heading {
    background: white;
    border-radius: 18px;
    padding: 28px;
    margin-bottom: 25px;
    border-left: 6px solid #f39c12;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.page-heading h1 {
    margin: 0 0 8px 0;
    color: #12355b;
    font-size: 28px;
}

.page-heading p {
    margin: 0;
    color: #666;
}

/* Message */

.message {
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
}

.success {
    background: #d1e7dd;
    color: #0f5132;
}

.error {
    background: #f8d7da;
    color: #842029;
}

/* Assignment form */

.form-card {
    background: white;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.form-card h2 {
    margin-top: 0;
    color: #12355b;
    margin-bottom: 22px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.form-group select,
.form-group input {
    width: 100%;
    height: 45px;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 15px;
    background: white;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #1976d2;
    box-shadow: 0 0 0 3px rgba(25,118,210,0.12);
}

.assign-button {
    margin-top: 22px;
    background: #f39c12;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 7px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.assign-button:hover {
    background: #d68910;
}

/* Table */

.table-card {
    background: white;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.table-card h2 {
    margin-top: 0;
    color: #12355b;
    margin-bottom: 20px;
}

.assignment-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
}

.assignment-table th {
    background: #12355b;
    color: white;
    padding: 13px;
    text-align: left;
    font-size: 14px;
}

.assignment-table td {
    padding: 13px;
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
}

.assignment-table tr:hover {
    background: #f7fbff;
}

.patient-name {
    font-weight: bold;
    color: #1976d2;
}

.doctor-name {
    font-weight: bold;
    color: #198754;
}

.remove-btn {
    background: #e53935;
    color: white;
    text-decoration: none;
    padding: 7px 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: bold;
}

.remove-btn:hover {
    background: #c62828;
}

/* Empty message */

.empty-message {
    text-align: center;
    padding: 35px;
    color: #777;
}

/* Footer */

.footer {
    margin-top: 50px;
    background: #12355b;
    color: white;
    text-align: center;
    padding: 20px;
}

/* Responsive */

@media (max-width: 900px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .navbar {
        flex-direction: column;
        gap: 15px;
    }

}

@media (max-width: 500px) {

    .container {
        width: 94%;
    }

    .page-heading h1 {
        font-size: 24px;
    }

    .navbar-links {
        flex-wrap: wrap;
        justify-content: center;
    }

}

</style>

</head>

<body>

<!-- Navbar -->

<div class="navbar">

    <div class="logo">
        🏥 Hospital Management System
    </div>

    <div class="navbar-links">

        <a href="index.php">
            Dashboard
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</div>


<div class="container">

    <!-- Heading -->

    <div class="page-heading">

        <h1>
            🔗 Patient - Doctor Assignment
        </h1>

        <p>
            Assign doctors to patients and manage existing assignments.
        </p>

    </div>


    <!-- Messages -->

    <?php if ($message != "") { ?>

        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php } ?>


    <!-- Assignment Form -->

    <div class="form-card">

        <h2>
            Assign Doctor to Patient
        </h2>

        <form method="POST">

            <div class="form-grid">

                <!-- Patient -->

                <div class="form-group">

                    <label>
                        Select Patient
                    </label>

                    <select name="patient_id" required>

                        <option value="">
                            -- Select Patient --
                        </option>

                        <?php

                        if ($patients && $patients->num_rows > 0) {

                            while ($patient = $patients->fetch_assoc()) {

                        ?>

                            <option value="<?= $patient['PatientID'] ?>">

                                <?= htmlspecialchars($patient['Name']) ?>

                                -
                                <?= htmlspecialchars($patient['Disease']) ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>


                <!-- Doctor -->

                <div class="form-group">

                    <label>
                        Select Doctor
                    </label>

                    <select name="doctor_id" required>

                        <option value="">
                            -- Select Doctor --
                        </option>

                        <?php

                        if ($doctors && $doctors->num_rows > 0) {

                            while ($doctor = $doctors->fetch_assoc()) {

                        ?>

                            <option value="<?= $doctor['DoctorID'] ?>">

                                <?= htmlspecialchars($doctor['Name']) ?>

                                -
                                <?= htmlspecialchars($doctor['Specialization']) ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>


                <!-- Date -->

                <div class="form-group">

                    <label>
                        Assigned Date
                    </label>

                    <input
                        type="date"
                        name="assigned_date"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                name="assign_doctor"
                class="assign-button"
            >
                🔗 Assign Doctor
            </button>

        </form>

    </div>


    <!-- Assignment List -->

    <div class="table-card">

        <h2>
            Current Assignments
        </h2>

        <?php if ($assignments && $assignments->num_rows > 0) { ?>

            <table class="assignment-table">

                <thead>

                    <tr>

                        <th>Assignment ID</th>

                        <th>Patient</th>

                        <th>Disease</th>

                        <th>Doctor</th>

                        <th>Specialization</th>

                        <th>Assigned Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($row = $assignments->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?= $row['AssignmentID'] ?>
                        </td>

                        <td class="patient-name">
                            <?= htmlspecialchars($row['PatientName']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['Disease']) ?>
                        </td>

                        <td class="doctor-name">
                            <?= htmlspecialchars($row['DoctorName']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['Specialization']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['AssignedDate']) ?>
                        </td>

                        <td>

                            <a
                                href="patient_doctor.php?delete=<?= $row['AssignmentID'] ?>"
                                class="remove-btn"
                                onclick="return confirm('Are you sure you want to remove this assignment?');"
                            >
                                Remove
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty-message">

                🔗 No patient-doctor assignments found.

                <br><br>

                Assign a doctor using the form above.

            </div>

        <?php } ?>

    </div>

</div>


<div class="footer">

    Hospital Management System 2026

</div>

</body>

</html>