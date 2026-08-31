<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    
    <h1>Task & Project Management Tool</h1>
    <p>Manage workspaces, projects, tasks, milestones, feedback, and team collaboration.</p>

    <h2>Login</h2>

    <?php
    if (isset($_SESSION["errors"])) {
        echo "<div style='color:red;'>";
        foreach ($_SESSION["errors"] as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
        unset($_SESSION["errors"]);
    }

    if (isset($_SESSION["success"])) {
        echo "<div style='color:green;'>";
        echo "<p>" . htmlspecialchars($_SESSION["success"]) . "</p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }
    ?>

    <form id="loginForm" action="../Controller/auth_controller.php" method="POST">
        <input type="hidden" name="action" value="login">

        <div>
            <label>Email</label><br>
            <input type="text" name="email" id="login_email">
            <small id="loginEmailError"></small>
        </div>

        <br>

        <div>
            <label>Password</label><br>
            <input type="password" name="password" id="login_password">
            <small id="loginPasswordError"></small>
        </div>

        <br>

        <button type="submit">Login</button>
    </form>

    <p>Do not have an account? <a href="register.php">Register here</a></p>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>