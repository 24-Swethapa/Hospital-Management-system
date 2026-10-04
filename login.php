<?php
include 'db.php';
session_start();

if (isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $result = $conn->query(
        "SELECT * FROM admin
         WHERE username='$username'
         AND password='$password'"
    );

    if ($result && $result->num_rows > 0) {

        $_SESSION['admin'] = $username;

        header("Location: index.php");
        exit();

    } else {

        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>Admin Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #0f4c81, #1976d2);
        }

        .login-page {
            min-height: 100vh;
            width: 100%;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 20px;
        }

        .login-box {
            width: 400px;
            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .hospital-icon {
            text-align: center;
            font-size: 45px;
            margin-bottom: 10px;
        }

        .login-box h1 {
            text-align: center;
            color: #12355b;
            font-size: 27px;
            margin: 0 0 8px 0;
        }

        .login-subtitle {
            text-align: center;
            color: #777;
            font-size: 15px;
            margin: 0 0 28px 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            height: 45px;

            padding: 10px 12px;

            border: 1px solid #ccc;
            border-radius: 7px;

            font-size: 15px;

            outline: none;
        }

        .form-group input:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.12);
        }

        .login-button {
            width: 100%;
            height: 45px;

            border: none;
            border-radius: 7px;

            background: #1976d2;
            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        .login-button:hover {
            background: #125ea7;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;

            padding: 11px;

            border-radius: 7px;

            margin-bottom: 18px;

            text-align: center;

            font-size: 14px;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 13px;
        }

        @media (max-width: 500px) {

            .login-box {
                width: 100%;
                padding: 28px 22px;
            }

        }

    </style>

</head>

<body>

<div class="login-page">

    <div class="login-box">

        <div class="hospital-icon">
            🏥
        </div>

        <h1>Hospital Management</h1>

        <p class="login-subtitle">
            Admin Login
        </p>

        <?php if ($error != "") { ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    placeholder="Enter username"
                    value="admin"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <button
                type="submit"
                class="login-button"
            >
                Login
            </button>

        </form>

        <div class="login-footer">
            Hospital Management System
        </div>

    </div>

</div>

</body>
</html>