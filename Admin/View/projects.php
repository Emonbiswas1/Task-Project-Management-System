<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/workspace_model.php";
include "../Model/user_model.php";

$workspace_id = $_GET["workspace_id"] ?? "";
$status = $_GET["status"] ?? "";
$client_id = $_GET["client_id"] ?? "";

$projects = get_all_projects_admin($conn, $workspace_id, $status, $client_id);
$workspaces = get_all_workspaces_admin($conn);
$clients = get_users_by_role($conn, "client");

?>

<!DOCTYPE html>
<html>
<head>
    <title>All Projects</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>All Projects</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="workspaces.php">Workspaces</a> |
        <a href="tasks.php">All Tasks</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter Projects</h2>

    <form method="GET" action="projects.php">
        <label>Workspace</label><br>
        <select name="workspace_id">
            <option value="">All Workspaces</option>
            <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
                <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                    <option value="<?php echo $workspace["id"]; ?>" <?php if ($workspace_id == $workspace["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($workspace["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <br><br>

        <label>Status</label><br>
        <select name="status">
            <option value="">All Status</option>
            <option value="planning" <?php if ($status == "planning") echo "selected"; ?>>Planning</option>
            <option value="active" <?php if ($status == "active") echo "selected"; ?>>Active</option>
            <option value="on_hold" <?php if ($status == "on_hold") echo "selected"; ?>>On Hold</option>
            <option value="completed" <?php if ($status == "completed") echo "selected"; ?>>Completed</option>
            <option value="archived" <?php if ($status == "archived") echo "selected"; ?>>Archived</option>
        </select>

        <br><br>

        <label>Client</label><br>
        <select name="client_id">
            <option value="">All Clients</option>
            <?php if ($clients && mysqli_num_rows($clients) > 0): ?>
                <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                    <option value="<?php echo $client["id"]; ?>" <?php if ($client_id == $client["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($client["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <br><br>

        <button type="submit">Apply Filter</button>
        <a href="projects.php">Reset</a>
    </form>

    <hr>

    <h2>Project List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project Name</th>
            <th>Description</th>
            <th>Client</th>
            <th>Deadline</th>
            <th>Color</th>
            <th>Status</th>
            <th>Visibility</th>
            <th>Created At</th>
        </tr>

        <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
            <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                <tr>
                    <td><?php echo $project["id"]; ?></td>
                    <td><?php echo htmlspecialchars($project["workspace_name"] ?? "No Workspace"); ?></td>
                    <td><?php echo htmlspecialchars($project["name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["description"]); ?></td>
                    <td><?php echo htmlspecialchars($project["client_name"] ?? "No Client"); ?></td>
                    <td><?php echo htmlspecialchars($project["deadline"]); ?></td>
                    <td><?php echo htmlspecialchars($project["color_label"]); ?></td>
                    <td><?php echo htmlspecialchars($project["status"]); ?></td>
                    <td><?php echo htmlspecialchars($project["visibility"]); ?></td>
                    <td><?php echo $project["created_at"]; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No project found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>