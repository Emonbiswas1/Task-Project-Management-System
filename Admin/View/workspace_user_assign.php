<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/workspace_model.php";
include "../Model/user_model.php";

$workspaces = get_all_workspaces_for_admin($conn);
$users = get_all_users_for_admin($conn);
$workspace_members = get_all_workspace_members_admin($conn);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign User to Workspace</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Assign User to Workspace</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="workspaces.php">Manage Workspaces</a> |
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

    <h2>Add User to Workspace</h2>

    <form id="adminWorkspaceUserAssignForm" action="../Controller/admin_workspace_controller.php" method="POST">
        <input type="hidden" name="action" value="assign_user_to_workspace">

        <div>
            <label>Workspace</label><br>
            <select name="workspace_id" id="admin_assign_workspace">
                <option value="">Select Workspace</option>

                <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
                    <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                        <option value="<?php echo $workspace["id"]; ?>">
                            <?php echo htmlspecialchars($workspace["name"] . " - Owner: " . ($workspace["owner_name"] ?? "Unknown")); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="adminAssignWorkspaceError"></small>
        </div>

        <br>

        <div>
            <label>User</label><br>
            <select name="user_id" id="admin_assign_user">
                <option value="">Select User</option>

                <?php if ($users && mysqli_num_rows($users) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?php echo $user["id"]; ?>">
                            <?php echo htmlspecialchars($user["name"] . " - " . $user["email"] . " (" . $user["role"] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="adminAssignUserError"></small>
        </div>

        <br>

        <div>
            <label>Workspace Role</label><br>
            <select name="workspace_role" id="admin_assign_workspace_role">
                <option value="">Select Workspace Role</option>
                <option value="lead">Lead</option>
                <option value="member">Member</option>
                <option value="client">Client</option>
            </select>
            <small id="adminAssignRoleError"></small>
        </div>

        <br>

        <button type="submit">Assign User</button>
    </form>

    <hr>

    <h2>Current Workspace Members</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>User</th>
            <th>Email</th>
            <th>System Role</th>
            <th>Workspace Role</th>
            <th>Joined At</th>
        </tr>

        <?php if ($workspace_members && mysqli_num_rows($workspace_members) > 0): ?>
            <?php while ($member = mysqli_fetch_assoc($workspace_members)): ?>
                <tr>
                    <td><?php echo $member["id"]; ?></td>
                    <td><?php echo htmlspecialchars($member["workspace_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($member["user_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($member["user_email"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($member["system_role"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($member["workspace_role"]); ?></td>
                    <td><?php echo htmlspecialchars($member["joined_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No workspace member found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>