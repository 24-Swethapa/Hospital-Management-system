<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

// Delete doctor
if (isset($_GET['delete'])) {

    $doctor_id = intval($_GET['delete']);

    // First remove doctor assignments
    $stmt1 = $conn->prepare(
        "DELETE FROM patient_doctor WHERE DoctorID = ?"
    );
    $stmt1->bind_param("i", $doctor_id);
    $stmt1->execute();
    $stmt1->close();

    // Then delete doctor
    $stmt2 = $conn->prepare(
        "DELETE FROM doctors WHERE DoctorID = ?"
    );
    $stmt2->bind_param("i", $doctor_id);

    if ($stmt2->execute()) {
        $message = "Doctor deleted successfully.";
    } else {
        $error = "Unable to delete doctor.";
    }

    $stmt2->close();
}

// Get doctors
$result = $conn->query(
    "SELECT DoctorID, Name, Specialization
     FROM doctors
     ORDER BY DoctorID DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Doctors - Hospital Management System</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 25px;
        }

        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            display: inline-block;
        }

        .back-btn {
            background: #555;
        }

        .add-btn {
            background: #198754;
        }

        .message {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #1f3c88;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f5f8ff;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
        }

        .delete-btn:hover {
            background: #b02a37;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>👨‍⚕️ Doctor List</h1>

    <div class="top-buttons">
        <a href="index.php" class="btn back-btn">← Dashboard</a>
        <a href="add_doctor.php" class="btn add-btn">+ Add Doctor</a>
    </div>

    <?php if ($message != ""): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <table>

        <tr>
            <th>Doctor ID</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Action</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>
                    <td><?= htmlspecialchars($row['DoctorID']) ?></td>

                    <td>
                        <?= htmlspecialchars($row['Name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['Specialization']) ?>
                    </td>

                    <td>
                        <a
                            href="view_doctor.php?delete=<?= $row['DoctorID'] ?>"
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this doctor?');"
                        >
                            Delete
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="4" style="text-align:center;">
                    No doctors found.
                </td>
            </tr>

        <?php endif; ?>

    </table>

</div>

</body>
</html>