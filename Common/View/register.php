<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h2>Register</h2>

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

    <form id="registerForm" action="../Controller/auth_controller.php" method="POST">
        <input type="hidden" name="action" value="register">

        <div>
            <label>Name</label><br>
            <input type="text" name="name" id="name">
            <small id="nameError"></small>
        </div>

        <br>

        <div>
            <label>Email</label><br>
            <input type="text" name="email" id="email">
            <small id="emailError"></small>
        </div>

        <br>

        <div>
            <label>Phone</label><br>
            <input type="text" name="phone" id="phone">
            <small id="phoneError"></small>
        </div>

        <br>

        <div>
            <label>Role</label><br>
            <select name="role" id="role">
                <option value="">Select Role</option>
                <option value="member">Member</option>
                <option value="team_lead">Team Lead</option>
                <option value="client">Client</option>
                <option value="admin">Admin</option>
            </select>
            <small id="roleError"></small>
        </div>

        <br>

        <div>
            <p><b>Note: </b>Company name optional for member/team lead/admin, useful for client.</p>
            <label>Company Name</label><br>
            <input type="text" name="company_name" id="company_name">
            <small id="companyError"></small>
        </div>

        <br>

        <div>
            <label>Password</label><br>
            <input type="password" name="password" id="password">
            <small id="passwordError"></small>
        </div>

        <br>

        <div>
            <label>Confirm Password</label><br>
            <input type="password" name="confirm_password" id="confirm_password">
            <small id="confirmPasswordError"></small>
        </div>

        <br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>