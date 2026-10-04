<?php
include 'db.php';
session_start();

/* Check login */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Count patients */
$patient_result = $conn->query("SELECT COUNT(*) AS total FROM patients");
$patient_count = $patient_result->fetch_assoc()['total'];

/* Count doctors */
$doctor_result = $conn->query("SELECT COUNT(*) AS total FROM doctors");
$doctor_count = $doctor_result->fetch_assoc()['total'];

/* Count patient-doctor assignments */
$assignment_count = 0;

$assignment_result = $conn->query("SHOW TABLES LIKE 'patient_doctor'");

if ($assignment_result && $assignment_result->num_rows > 0) {
    $count_result = $conn->query(
        "SELECT COUNT(*) AS total FROM patient_doctor"
    );

    if ($count_result) {
        $assignment_count = $count_result->fetch_assoc()['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Management System</title>

    <link rel="stylesheet" href="style.css">

    <style>

        /* ==============================
           DASHBOARD PAGE
        ============================== */

        body {
            background: #eef5fb;
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
            font-size: 22px;
            font-weight: bold;
            color: white;
        }

        .navbar-links {
            display: flex;
            align-items: center;
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

        /* Main */

        .dashboard-container {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }

        /* Welcome Box */

        .welcome-box {
            background: white;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 6px solid #1976d2;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .welcome-box h1 {
            margin: 0 0 10px 0;
            color: #12355b;
            font-size: 30px;
        }

        .welcome-box p {
            margin: 0;
            color: #666;
            font-size: 16px;
        }

        /* Statistics */

        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-bottom: 35px;
        }

        .stat-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }

        .stat-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: #1976d2;
        }

        .stat-icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        .stat-title {
            color: #777;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #12355b;
        }

        /* Different stat colours */

        .patients-stat::before {
            background: #1976d2;
        }

        .doctors-stat::before {
            background: #198754;
        }

        .assignment-stat::before {
            background: #f39c12;
        }

        /* Feature Heading */

        .section-heading {
            color: #12355b;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Feature Cards */

        .feature-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .feature-card {
            background: white;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: 0.25s;
            border-top: 5px solid #1976d2;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(0,0,0,0.13);
        }

        .feature-icon {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .feature-card h2 {
            color: #12355b;
            font-size: 21px;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
            min-height: 50px;
            margin-bottom: 22px;
        }

        /* Buttons */

        .dashboard-btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            margin-right: 5px;
            color: white;
        }

        .blue-btn {
            background: #1976d2;
        }

        .blue-btn:hover {
            background: #125ea7;
        }

        .green-btn {
            background: #198754;
        }

        .green-btn:hover {
            background: #146c43;
        }

        .orange-btn {
            background: #f39c12;
        }

        .orange-btn:hover {
            background: #d68910;
        }

        /* Footer */

        .dashboard-footer {
            margin-top: 50px;
            background: #12355b;
            color: white;
            text-align: center;
            padding: 20px;
        }

        /* Responsive */

        @media (max-width: 900px) {

            .stats-container {
                grid-template-columns: 1fr;
            }

            .feature-container {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

        }

        @media (max-width: 500px) {

            .dashboard-container {
                width: 94%;
            }

            .welcome-box h1 {
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

<!-- ==============================
     NAVIGATION BAR
============================== -->

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


<!-- ==============================
     MAIN DASHBOARD
============================== -->

<div class="dashboard-container">


    <!-- Welcome -->

    <div class="welcome-box">

        <h1>
            Welcome, <?= htmlspecialchars($_SESSION['admin']) ?>! 👋
        </h1>

        <p>
            Manage patients, doctors and patient-doctor assignments
            from one place.
        </p>

    </div>


    <!-- ==========================
         STATISTICS
    =========================== -->

    <div class="stats-container">


        <!-- Patients -->

        <div class="stat-box patients-stat">

            <div class="stat-icon">
                👤
            </div>

            <div class="stat-title">
                Total Patients
            </div>

            <div class="stat-number">
                <?= $patient_count ?>
            </div>

        </div>


        <!-- Doctors -->

        <div class="stat-box doctors-stat">

            <div class="stat-icon">
                👨‍⚕️
            </div>

            <div class="stat-title">
                Total Doctors
            </div>

            <div class="stat-number">
                <?= $doctor_count ?>
            </div>

        </div>


        <!-- Assignments -->

        <div class="stat-box assignment-stat">

            <div class="stat-icon">
                🔗
            </div>

            <div class="stat-title">
                Doctor Assignments
            </div>

            <div class="stat-number">
                <?= $assignment_count ?>
            </div>

        </div>


    </div>


    <!-- ==========================
         MANAGEMENT SECTION
    =========================== -->

    <h2 class="section-heading">
        Management
    </h2>


    <div class="feature-container">


        <!-- PATIENT CARD -->

        <div class="feature-card">

            <div class="feature-icon">
                👤
            </div>

            <h2>
                Patient Management
            </h2>

            <p>
                Add new patients, view patient details and
                update existing patient information.
            </p>

            <a href="add_patient.php"
               class="dashboard-btn blue-btn">
                Add Patient
            </a>

            <a href="view_patient.php"
               class="dashboard-btn blue-btn">
                View Patients
            </a>

        </div>


        <!-- DOCTOR CARD -->

        <div class="feature-card">

            <div class="feature-icon">
                👨‍⚕️
            </div>

            <h2>
                Doctor Management
            </h2>

            <p>
                Add doctors and manage their names and
                medical specializations.
            </p>

            <a href="add_doctor.php"
               class="dashboard-btn green-btn">
                Add Doctor
            </a>

            <a href="view_doctor.php"
               class="dashboard-btn green-btn">
                View Doctors
            </a>

        </div>


        <!-- PATIENT DOCTOR CARD -->

        <div class="feature-card">

            <div class="feature-icon">
                🔗
            </div>

            <h2>
                Patient - Doctor
            </h2>

            <p>
                Assign doctors to patients and manage
                existing doctor-patient assignments.
            </p>

            <a href="patient_doctor.php"
               class="dashboard-btn orange-btn">
                Manage Assignments
            </a>

        </div>


    </div>

</div>


<!-- ==============================
     FOOTER
============================== -->

<div class="dashboard-footer">

    Hospital Management System 2026

</div>


</body>

</html>