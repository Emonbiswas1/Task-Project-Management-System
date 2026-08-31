<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Create New User</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="users.php">Manage Users</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

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
    <form id="adminCreateUserForm" action="../Controller/admin_user_controller.php" method="POST">
        <input type="hidden" name="action" value="create_user">

        <div>
            <label>Name</label><br>
            <input type="text" name="name" id="admin_create_name">
            <small id="adminCreateNameError"></small>
        </div>

        <br>

        <div>
            <label>Email</label><br>
            <input type="text" name="email" id="admin_create_email">
            <small id="adminCreateEmailError"></small>
        </div>

        <br>

        <div>
            <label>Phone</label><br>
            <input type="text" name="phone" id="admin_create_phone">
            <small id="adminCreatePhoneError"></small>
        </div>

        <br>

        <div>
            <label>Company Name</label><br>
            <input type="text" name="company_name" id="admin_create_company">
            <small>Optional for member/team lead/admin, useful for client.</small>
        </div>

        <br>

        <div>
            <label>Role</label><br>
            <select name="role" id="admin_create_role">
                <option value="">Select Role</option>
                <option value="member">Member</option>
                <option value="team_lead">Team Lead</option>
                <option value="client">Client</option>
                <option value="admin">Admin</option>
            </select>
            <small id="adminCreateRoleError"></small>
        </div>

        <br>

        <div>
            <label>Password</label><br>
            <input type="password" name="password" id="admin_create_password">
            <small id="adminCreatePasswordError"></small>
        </div>

        <br>

        <div>
            <label>Confirm Password</label><br>
            <input type="password" name="confirm_password" id="admin_create_confirm_password">
            <small id="adminCreateConfirmPasswordError"></small>
        </div>

        <br>

        <button type="submit">Create User</button>
    </form>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>