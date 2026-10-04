<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);

    if ($name == "" || $specialization == "") {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO doctors (Name, Specialization)
             VALUES (?, ?)"
        );

        if (!$stmt) {

            $message = "Database error: " . $conn->error;
            $message_type = "error";

        } else {

            $stmt->bind_param(
                "ss",
                $name,
                $specialization
            );

            if ($stmt->execute()) {

                $message = "Doctor added successfully!";
                $message_type = "success";

                $_POST['name'] = "";
                $_POST['specialization'] = "";

            } else {

                $message = "Unable to add doctor: " . $stmt->error;
                $message_type = "error";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Doctor</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
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
    color: white;
    font-size: 22px;
    font-weight: bold;
}

.navbar a {
    color: white;
    text-decoration: none;
    padding: 9px 15px;
    border-radius: 7px;
    font-weight: bold;
}

.navbar a:hover {
    background: rgba(255,255,255,0.18);
}

.logout {
    background: #e53935;
}

/* Container */

.container {
    width: 90%;
    max-width: 700px;
    margin: 45px auto;
}

/* Card */

.card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.card h1 {
    text-align: center;
    color: #12355b;
    margin-top: 0;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
}

/* Message */

.message {
    padding: 13px;
    border-radius: 7px;
    margin-bottom: 20px;
    text-align: center;
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

/* Form */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

.form-group input {
    width: 100%;
    height: 46px;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 15px;
}

.form-group input:focus {
    outline: none;
    border-color: #1976d2;
    box-shadow: 0 0 0 3px rgba(25,118,210,0.12);
}

.add-button {
    width: 100%;
    height: 46px;
    border: none;
    border-radius: 7px;
    background: #198754;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.add-button:hover {
    background: #146c43;
}

.back-button {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #1976d2;
    text-decoration: none;
    font-weight: bold;
}

.back-button:hover {
    text-decoration: underline;
}

</style>

</head>

<body>

<div class="navbar">

    <div class="logo">
        🏥 Hospital Management System
    </div>

    <div>
        <a href="index.php">Dashboard</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

</div>

<div class="container">

    <div class="card">

        <h1>👨‍⚕️ Add Doctor</h1>

        <p class="subtitle">
            Enter doctor details below
        </p>

        <?php if ($message != "") { ?>

            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>Doctor Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter doctor name"
                    value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>Specialization</label>

                <input
                    type="text"
                    name="specialization"
                    placeholder="Enter specialization"
                    value="<?= isset($_POST['specialization']) ? htmlspecialchars($_POST['specialization']) : '' ?>"
                    required
                >

            </div>

            <button
                type="submit"
                class="add-button"
            >
                ➕ Add Doctor
            </button>

        </form>

        <a href="view_doctor.php" class="back-button">
            View All Doctors
        </a>

    </div>

</div>

</body>

</html>