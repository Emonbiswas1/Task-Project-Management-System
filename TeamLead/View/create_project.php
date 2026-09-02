<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/workspace_model.php";
include "../Model/user_model.php";

$team_lead_id = $_SESSION["user_id"];
$workspaces = get_workspaces_by_owner($conn, $team_lead_id);
$clients = get_users_by_role($conn, "client");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Project</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Create Project</h1>

    <p>
        <a href="projects.php">Back to Projects</a> |
        <a href="dashboard.php">Dashboard</a> |
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
    ?>

    <form id="projectForm" action="../Controller/project_controller.php" method="POST">
        <input type="hidden" name="action" value="create_project">

        <div>
            <label>Workspace</label><br>
            <select name="workspace_id" id="project_workspace">
                <option value="">Select Workspace</option>
                <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
                    <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                        <option value="<?php echo $workspace["id"]; ?>">
                            <?php echo htmlspecialchars($workspace["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="projectWorkspaceError"></small>
        </div>

        <br>

        <div>
            <label>Project Name</label><br>
            <input type="text" name="name" id="project_name">
            <small id="projectNameError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="project_description"></textarea>
            <small id="projectDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Client</label><br>
            <select name="client_id" id="project_client">
                <option value="">Select Client</option>
                <?php if ($clients && mysqli_num_rows($clients) > 0): ?>
                    <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                        <option value="<?php echo $client["id"]; ?>">
                            <?php echo htmlspecialchars($client["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="projectClientError"></small>
        </div>

        <br>

        <div>
            <label>Deadline</label><br>
            <input type="text" name="deadline" id="project_deadline" placeholder="YYYY-MM-DD">
            <small id="projectDeadlineError"></small>
        </div>

        <br>

        <div>
            <label>Color Label</label><br>
            <input type="text" name="color_label" id="project_color" placeholder="blue / green / red">
            <small id="projectColorError"></small>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="status" id="project_status">
                <option value="">Select Status</option>
                <option value="planning">Planning</option>
                <option value="active">Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
            </select>
            <small id="projectStatusError"></small>
        </div>

        <br>

        <div>
            <label>Visibility</label><br>
            <select name="visibility" id="project_visibility">
                <option value="">Select Visibility</option>
                <option value="internal">Internal</option>
                <option value="client_visible">Client Visible</option>
            </select>
            <small id="projectVisibilityError"></small>
        </div>

        <br>

        <button type="submit">Create Project</button>
    </form>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>