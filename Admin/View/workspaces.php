<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/workspace_model.php";

$search = trim($_GET["search"] ?? "");
$workspaces = get_all_workspaces_admin($conn, $search);

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
        <a href="dashboard.php">Dashboard</a> |
        <a href="workspace_user_assign.php">Assign User to Workspace</a> |
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

    <h2>Search Workspaces</h2>

    <form method="GET" action="workspaces.php">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search workspace">
        <button type="submit">Search</button>
        <a href="workspaces.php">Reset</a>
    </form>

    <hr>

    <h2>Workspace List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Owner</th>
            <th>Invite Code</th>
            <th>Plan</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Members</th>
            <th>Change Status</th>
            <th>Delete</th>
        </tr>

        <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
            <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                <tr>
                    <td><?php echo $workspace["id"]; ?></td>
                    <td><?php echo htmlspecialchars($workspace["name"]); ?></td>
                    <td><?php echo htmlspecialchars($workspace["description"]); ?></td>
                    <td>
                        <?php
                        echo htmlspecialchars(($workspace["owner_name"] ?? "No Owner") . " (" . ($workspace["owner_email"] ?? "") . ")");
                        ?>
                    </td>
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

                    <td>
                        <a href="workspace_members.php?workspace_id=<?php echo $workspace["id"]; ?>">
                            View Members
                        </a>
                    </td>

                    <td>
                        <form class="admin-workspace-status-form" action="../Controller/admin_workspace_controller.php" method="POST">
                            <input type="hidden" name="action" value="change_workspace_status">
                            <input type="hidden" name="workspace_id" value="<?php echo $workspace["id"]; ?>">

                            <select name="is_active">
                                <option value="1" <?php if ($workspace["is_active"] == 1) echo "selected"; ?>>Active</option>
                                <option value="0" <?php if ($workspace["is_active"] == 0) echo "selected"; ?>>Inactive</option>
                            </select>

                            <button type="submit">Update</button>
                        </form>
                    </td>

                    <td>
                        <form class="admin-workspace-delete-form" action="../Controller/admin_workspace_controller.php" method="POST">
                            <input type="hidden" name="action" value="delete_workspace">
                            <input type="hidden" name="workspace_id" value="<?php echo $workspace["id"]; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">No workspace found.</td>
            </tr>
        <?php endif; ?>
    </table>
<script src="../../assets/js/validation.js"></script>

</body>
</html>