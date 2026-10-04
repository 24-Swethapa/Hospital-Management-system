<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Patient ID is missing.");
}

$patient_id = intval($_GET['id']);

$message = "";
$error = "";


/* Update patient */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $disease = trim($_POST['disease']);
    $admitted_date = $_POST['admitted_date'];

    $stmt = $conn->prepare(
        "UPDATE patients
         SET Name = ?, Disease = ?, AdmittedDate = ?
         WHERE PatientID = ?"
    );

    $stmt->bind_param(
        "sssi",
        $name,
        $disease,
        $admitted_date,
        $patient_id
    );

    if ($stmt->execute()) {
        $message = "Patient updated successfully.";
    } else {
        $error = "Error updating patient: " . $stmt->error;
    }

    $stmt->close();
}


/* Get patient */

$stmt = $conn->prepare(
    "SELECT PatientID, Name, Disease, AdmittedDate
     FROM patients
     WHERE PatientID = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Patient not found.");
}

$patient = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Patient</title>

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

h1 {
    color: #12355b;
    margin-top: 0;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
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
    background: #198754;
    border: none;
    border-radius: 7px;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #146c43;
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

<h1>✏️ Edit Patient</h1>


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
value="<?= htmlspecialchars($patient['Name']) ?>"
required
>

</div>


<div class="form-group">

<label>Disease</label>

<input
type="text"
name="disease"
value="<?= htmlspecialchars($patient['Disease']) ?>"
required
>

</div>


<div class="form-group">

<label>Admitted Date</label>

<input
type="date"
name="admitted_date"
value="<?= htmlspecialchars($patient['AdmittedDate']) ?>"
required
>

</div>


<button type="submit">
Update Patient
</button>

</form>


<a href="view_patient.php" class="back">
← Back to Patient Records
</a>

</div>

</div>

</body>
</html>