<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/workspace_model.php";

$workspace_id = $_GET["workspace_id"] ?? "";

if ($workspace_id == "" || !is_numeric($workspace_id)) {
    echo "<h2>Invalid workspace selected.</h2>";
    echo "<p><a href='workspaces.php'>Back to Workspaces</a></p>";
    exit();
}

$workspace = get_workspace_by_id_admin($conn, $workspace_id);

if (!$workspace) {
    echo "<h2>Workspace not found.</h2>";
    echo "<p><a href='workspaces.php'>Back to Workspaces</a></p>";
    exit();
}

$members = get_workspace_members_admin($conn, $workspace_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Workspace Members</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Workspace Members</h1>

    <p>
        <a href="workspaces.php">Back to Workspaces</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2><?php echo htmlspecialchars($workspace["name"]); ?></h2>

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

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Platform Role</th>
            <th>Workspace Role</th>
            <th>Joined At</th>
            <th>Action</th>
        </tr>

        <?php if ($members && mysqli_num_rows($members) > 0): ?>
            <?php while ($member = mysqli_fetch_assoc($members)): ?>
                <tr>
                    <td><?php echo $member["id"]; ?></td>
                    <td><?php echo htmlspecialchars($member["name"]); ?></td>
                    <td><?php echo htmlspecialchars($member["email"]); ?></td>
                    <td><?php echo htmlspecialchars($member["phone"]); ?></td>
                    <td><?php echo htmlspecialchars($member["role"]); ?></td>
                    <td><?php echo htmlspecialchars($member["workspace_role"]); ?></td>
                    <td><?php echo $member["joined_at"]; ?></td>
                    <td>
                        <form class="admin-workspace-member-remove-form" action="../Controller/admin_workspace_controller.php" method="POST">
                            <input type="hidden" name="action" value="remove_workspace_member">
                            <input type="hidden" name="workspace_member_id" value="<?php echo $member["id"]; ?>">
                            <input type="hidden" name="workspace_id" value="<?php echo $workspace_id; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No member found in this workspace.</td>
            </tr>
        <?php endif; ?>
    </table>
<script src="../../assets/js/validation.js"></script>

</body>
</html>