<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $disease = trim($_POST['disease']);
    $admitted_date = $_POST['admitted_date'];

    if ($name == "" || $disease == "" || $admitted_date == "") {
        $error = "Please fill all fields.";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO patients (Name, Disease, AdmittedDate)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $name, $disease, $admitted_date);

        if ($stmt->execute()) {
            $message = "Patient added successfully.";
        } else {
            $error = "Error adding patient: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Patient</title>

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

.container {
    width: 90%;
    max-width: 650px;
    margin: 45px auto;
}

.form-card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.form-card h1 {
    color: #12355b;
    margin-top: 0;
}

.subtitle {
    color: #777;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 15px;
}

input:focus {
    outline: none;
    border-color: #1976d2;
}

button {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 7px;
    background: #1976d2;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #125ea7;
}

.success {
    background: #d1fae5;
    color: #065f46;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 20px;
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 20px;
}

.back {
    display: inline-block;
    margin-top: 20px;
    color: #1976d2;
    text-decoration: none;
    font-weight: bold;
}

</style>

</head>

<body>

<div class="navbar">

    <div class="logo">
        🏥 Hospital Management System
    </div>

    <a href="index.php">Dashboard</a>

</div>


<div class="container">

<div class="form-card">

<h1>👤 Add Patient</h1>

<p class="subtitle">
Register a new patient in the hospital system.
</p>

<?php if ($message != "") { ?>

<div class="success">
    <?= htmlspecialchars($message) ?>
</div>

<?php } ?>

<?php if ($error != "") { ?>

<div class="error">
    <?= htmlspecialchars($error) ?>
</div>

<?php } ?>


<form method="POST">

<div class="form-group">

<label>Patient Name</label>

<input
    type="text"
    name="name"
    placeholder="Enter patient name"
    required
>

</div>


<div class="form-group">

<label>Disease</label>

<input
    type="text"
    name="disease"
    placeholder="Enter disease"
    required
>

</div>


<div class="form-group">

<label>Admitted Date</label>

<input
    type="date"
    name="admitted_date"
    required
>

</div>


<button type="submit">
    Add Patient
</button>

</form>


<a href="view_patient.php" class="back">
    ← View Patient Records
</a>

</div>

</div>

</body>
</html>