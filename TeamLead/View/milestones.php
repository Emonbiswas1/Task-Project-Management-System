<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/milestone_model.php";

$team_lead_id = $_SESSION["user_id"];
$projects = get_projects_by_teamlead($conn, $team_lead_id);
$milestones = get_milestones_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Milestones</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Manage Milestones</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
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

    <h2>Create Milestone</h2>

    <form id="milestoneForm" action="../Controller/milestone_controller.php" method="POST">
        <input type="hidden" name="action" value="create_milestone">

        <div>
            <label>Project</label><br>
            <select name="project_id" id="milestone_project">
                <option value="">Select Project</option>
                <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                        <option value="<?php echo $project["id"]; ?>">
                            <?php echo htmlspecialchars($project["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="milestoneProjectError"></small>
        </div>

        <br>

        <div>
            <label>Milestone Title</label><br>
            <input type="text" name="title" id="milestone_title">
            <small id="milestoneTitleError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="milestone_description"></textarea>
            <small id="milestoneDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Due Date</label><br>
            <input type="text" name="due_date" id="milestone_due_date" placeholder="YYYY-MM-DD">
            <small id="milestoneDueDateError"></small>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="status" id="milestone_status">
                <option value="">Select Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
            <small id="milestoneStatusError"></small>
        </div>

        <br>

        <div>
            <label>Client Visible?</label><br>
            <select name="is_client_visible" id="milestone_client_visible">
                <option value="">Select Visibility</option>
                <option value="1">Yes, visible to client</option>
                <option value="0">No, internal only</option>
            </select>
            <small id="milestoneClientVisibleError"></small>
        </div>

        <br>

        <button type="submit">Create Milestone</button>
    </form>

    <hr>

    <h2>Milestone List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Title</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Client Visible</th>
            <th>Completed At</th>
            <th>Action</th>
        </tr>

        <?php if ($milestones && mysqli_num_rows($milestones) > 0): ?>
            <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?>
                <tr>
                    <td><?php echo $milestone["id"]; ?></td>
                    <td><?php echo htmlspecialchars($milestone["project_name"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["title"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["description"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["due_date"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["status"]); ?></td>
                    <td>
                        <?php
                        if ($milestone["is_client_visible"] == 1) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($milestone["completed_at"]) {
                            echo htmlspecialchars($milestone["completed_at"]);
                        } else {
                            echo "Not completed";
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($milestone["status"] != "completed"): ?>
                            <form action="../Controller/milestone_controller.php" method="POST">
                                <input type="hidden" name="action" value="mark_completed">
                                <input type="hidden" name="milestone_id" value="<?php echo $milestone["id"]; ?>">
                                <button type="submit">Mark Completed</button>
                            </form>
                        <?php else: ?>
                            Completed
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No milestone found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>