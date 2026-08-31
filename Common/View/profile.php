<?php

include "../../includes/auth_check.php";
include "../../DB/db.php";
include "../Model/user_model.php";

$user_id = $_SESSION["user_id"];
$user = get_user_by_id($conn, $user_id);

if (!$user) {
    echo "<h2>User not found.</h2>";
    exit();
}

$dashboard_link = "../../index.php";

if ($_SESSION["role"] == "member") {
    $dashboard_link = "../../Member/View/dashboard.php";
}
elseif ($_SESSION["role"] == "team_lead") {
    $dashboard_link = "../../TeamLead/View/dashboard.php";
}
elseif ($_SESSION["role"] == "client") {
    $dashboard_link = "../../Client/View/dashboard.php";
}
elseif ($_SESSION["role"] == "admin") {
    $dashboard_link = "../../Admin/View/dashboard.php";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>My Profile</h1>

    <p>
        <a href="<?php echo $dashboard_link; ?>">Back to Dashboard</a> |
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

    <h2>Profile Information</h2>

    <div>
        <?php if (!empty($user["profile_pic"])): ?>
            <img src="../<?php echo htmlspecialchars($user["profile_pic"]); ?>" alt="Profile Picture" width="120" height="120">
        <?php else: ?>
            <p>No profile picture uploaded.</p>
        <?php endif; ?>
    </div>

    <br>

    <form id="profileForm" action="../Controller/profile_controller.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile">

        <div>
            <label>Name</label><br>
            <input type="text" name="name" id="profile_name" value="<?php echo htmlspecialchars($user["name"]); ?>">
            <small id="profileNameError"></small>
        </div>

        <br>

        <div>
            <label>Email</label><br>
            <input type="text" value="<?php echo htmlspecialchars($user["email"]); ?>" readonly>
            <small>Email cannot be changed.</small>
        </div>

        <br>

        <div>
            <label>Phone</label><br>
            <input type="text" name="phone" id="profile_phone" value="<?php echo htmlspecialchars($user["phone"]); ?>">
            <small id="profilePhoneError"></small>
        </div>

        <br>

        <div>
            <label>Company Name</label><br>
            <input type="text" name="company_name" id="profile_company" value="<?php echo htmlspecialchars($user["company_name"] ?? ""); ?>">
            <small>For client users, company name is useful.</small>
        </div>

        <br>

        <div>
            <label>Profile Picture</label><br>
            <input type="file" name="profile_pic" id="profile_pic">
            <small id="profilePicError"></small>
        </div>

        <br>

        <button type="submit">Update Profile</button>
    </form>

    <hr>

    <h2>Change Password</h2>

    <form id="passwordForm" action="../Controller/profile_controller.php" method="POST">
        <input type="hidden" name="action" value="change_password">

        <div>
            <label>Current Password</label><br>
            <input type="password" name="current_password" id="current_password">
            <small id="currentPasswordError"></small>
        </div>

        <br>

        <div>
            <label>New Password</label><br>
            <input type="password" name="new_password" id="new_password">
            <small id="newPasswordError"></small>
        </div>

        <br>

        <div>
            <label>Confirm New Password</label><br>
            <input type="password" name="confirm_password" id="profile_confirm_password">
            <small id="profileConfirmPasswordError"></small>
        </div>

        <br>

        <button type="submit">Change Password</button>
    </form>

    <script src="../assets/js/validation.js"></script>
</body>
</html>