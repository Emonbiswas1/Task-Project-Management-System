<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/workspace_model.php";

$owner_id = $_SESSION["user_id"];
$workspaces = get_workspaces_by_owner($conn, $owner_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Workspaces</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Manage Workspaces</h1>

    <p>
        <a href="dashboard.php">Back to Dashboard</a> |
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

    <h2>Create Workspace</h2>

    <form id="workspaceForm" action="../Controller/workspace_controller.php" method="POST">
        <input type="hidden" name="action" value="create_workspace">

        <div>
            <label>Workspace Name</label><br>
            <input type="text" name="name" id="workspace_name">
            <small id="workspaceNameError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="workspace_description"></textarea>
            <small id="workspaceDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Plan</label><br>
            <select name="plan" id="workspace_plan">
                <option value="free">Free</option>
                <option value="pro">Pro</option>
            </select>
        </div>

        <br>

        <button type="submit">Create Workspace</button>
    </form>

    <hr>

    <h2>My Workspaces</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Invite Code</th>
            <th>Plan</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>

        <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
            <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                <tr>
                    <td><?php echo $workspace["id"]; ?></td>
                    <td><?php echo htmlspecialchars($workspace["name"]); ?></td>
                    <td><?php echo htmlspecialchars($workspace["description"]); ?></td>
                    <td><?php echo htmlspecialchars($workspace["invite_code"]); ?></td>
                    <td><?php echo htmlspecialchars($workspace["plan"]); ?></td>
                    <td>
                        <?php
                        if ($workspace["is_active"] == 1) {
                            echo "Active";
                        } else {
                            echo "Inactive";
                        }
                        ?>
                    </td>
                    <td><?php echo $workspace["created_at"]; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No workspace found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>