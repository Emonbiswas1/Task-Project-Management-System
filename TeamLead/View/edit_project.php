<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/workspace_model.php";
include "../Model/user_model.php";

$team_lead_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

if ($project_id == "" || !is_numeric($project_id)) {
    echo "<h2>Invalid project selected.</h2>";
    echo "<p><a href='projects.php'>Back to Projects</a></p>";
    exit();
}

$project = get_project_by_id_and_teamlead($conn, $project_id, $team_lead_id);

if (!$project) {
    echo "<h2>Project not found or access denied.</h2>";
    echo "<p><a href='projects.php'>Back to Projects</a></p>";
    exit();
}

$workspaces = get_workspaces_by_owner($conn, $team_lead_id);
$clients = get_users_by_role($conn, "client");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Edit Project</h1>

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

    <form id="editProjectForm" action="../Controller/project_controller.php" method="POST">
        <input type="hidden" name="action" value="update_project">
        <input type="hidden" name="project_id" value="<?php echo $project["id"]; ?>">

        <div>
            <label>Workspace</label><br>
            <select name="workspace_id" id="edit_project_workspace">
                <option value="">Select Workspace</option>
                <?php if ($workspaces && mysqli_num_rows($workspaces) > 0): ?>
                    <?php while ($workspace = mysqli_fetch_assoc($workspaces)): ?>
                        <option value="<?php echo $workspace["id"]; ?>" <?php if ($project["workspace_id"] == $workspace["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($workspace["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="editProjectWorkspaceError"></small>
        </div>

        <br>

        <div>
            <label>Project Name</label><br>
            <input type="text" name="name" id="edit_project_name" value="<?php echo htmlspecialchars($project["name"]); ?>">
            <small id="editProjectNameError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="edit_project_description"><?php echo htmlspecialchars($project["description"]); ?></textarea>
            <small id="editProjectDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Client</label><br>
            <select name="client_id" id="edit_project_client">
                <option value="">Select Client</option>
                <?php if ($clients && mysqli_num_rows($clients) > 0): ?>
                    <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                        <option value="<?php echo $client["id"]; ?>" <?php if ($project["client_id"] == $client["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($client["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="editProjectClientError"></small>
        </div>

        <br>

        <div>
            <label>Deadline</label><br>
            <input type="text" name="deadline" id="edit_project_deadline" value="<?php echo htmlspecialchars($project["deadline"]); ?>" placeholder="YYYY-MM-DD">
            <small id="editProjectDeadlineError"></small>
        </div>

        <br>

        <div>
            <label>Color Label</label><br>
            <input type="text" name="color_label" id="edit_project_color" value="<?php echo htmlspecialchars($project["color_label"]); ?>">
            <small id="editProjectColorError"></small>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="status" id="edit_project_status">
                <option value="">Select Status</option>
                <option value="planning" <?php if ($project["status"] == "planning") echo "selected"; ?>>Planning</option>
                <option value="active" <?php if ($project["status"] == "active") echo "selected"; ?>>Active</option>
                <option value="on_hold" <?php if ($project["status"] == "on_hold") echo "selected"; ?>>On Hold</option>
                <option value="completed" <?php if ($project["status"] == "completed") echo "selected"; ?>>Completed</option>
                <option value="archived" <?php if ($project["status"] == "archived") echo "selected"; ?>>Archived</option>
            </select>
            <small id="editProjectStatusError"></small>
        </div>

        <br>

        <div>
            <label>Visibility</label><br>
            <select name="visibility" id="edit_project_visibility">
                <option value="">Select Visibility</option>
                <option value="internal" <?php if ($project["visibility"] == "internal") echo "selected"; ?>>Internal</option>
                <option value="client_visible" <?php if ($project["visibility"] == "client_visible") echo "selected"; ?>>Client Visible</option>
            </select>
            <small id="editProjectVisibilityError"></small>
        </div>

        <br>

        <button type="submit">Update Project</button>
    </form>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>