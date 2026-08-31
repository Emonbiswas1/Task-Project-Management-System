<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/task_model.php";
include "../Model/project_model.php";
include "../Model/user_model.php";
include "../Model/milestone_model.php";

$team_lead_id = $_SESSION["user_id"];
$task_id = $_GET["task_id"] ?? "";

if ($task_id == "" || !is_numeric($task_id)) {
    echo "<h2>Invalid task selected.</h2>";
    echo "<p><a href='tasks.php'>Back to Tasks</a></p>";
    exit();
}

$task = get_task_by_id_and_teamlead($conn, $task_id, $team_lead_id);

if (!$task) {
    echo "<h2>Task not found or access denied.</h2>";
    echo "<p><a href='tasks.php'>Back to Tasks</a></p>";
    exit();
}

$projects = get_projects_by_teamlead($conn, $team_lead_id);
$members = get_users_by_role($conn, "member");
$milestones = get_milestones_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Edit Task</h1>

    <p>
        <a href="tasks.php">Back to Tasks</a> |
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

    <form id="editTaskForm" action="../Controller/task_controller.php" method="POST">
        <input type="hidden" name="action" value="update_task">
        <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">

        <div>
            <label>Project</label><br>
            <select name="project_id" id="edit_task_project">
                <option value="">Select Project</option>
                <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                        <option value="<?php echo $project["id"]; ?>" <?php if ($task["project_id"] == $project["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($project["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="editTaskProjectError"></small>
        </div>

        <br>

        <div>
            <label>Milestone Optional</label><br>
            <select name="milestone_id" id="edit_task_milestone">
                <option value="">No Milestone</option>
                <?php if ($milestones && mysqli_num_rows($milestones) > 0): ?>
                    <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?>
                        <option value="<?php echo $milestone["id"]; ?>" <?php if ($task["milestone_id"] == $milestone["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($milestone["project_name"] . " - " . $milestone["title"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Task Title</label><br>
            <input type="text" name="title" id="edit_task_title" value="<?php echo htmlspecialchars($task["title"]); ?>">
            <small id="editTaskTitleError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="edit_task_description"><?php echo htmlspecialchars($task["description"]); ?></textarea>
            <small id="editTaskDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Assign To</label><br>
            <select name="assigned_to" id="edit_task_assigned_to">
                <option value="">Select Member</option>
                <?php if ($members && mysqli_num_rows($members) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($members)): ?>
                        <option value="<?php echo $member["id"]; ?>" <?php if ($task["assigned_to"] == $member["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($member["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="editTaskAssignedError"></small>
        </div>

        <br>

        <div>
            <label>Priority</label><br>
            <select name="priority" id="edit_task_priority">
                <option value="">Select Priority</option>
                <option value="low" <?php if ($task["priority"] == "low") echo "selected"; ?>>Low</option>
                <option value="medium" <?php if ($task["priority"] == "medium") echo "selected"; ?>>Medium</option>
                <option value="high" <?php if ($task["priority"] == "high") echo "selected"; ?>>High</option>
                <option value="critical" <?php if ($task["priority"] == "critical") echo "selected"; ?>>Critical</option>
            </select>
            <small id="editTaskPriorityError"></small>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="status" id="edit_task_status">
                <option value="">Select Status</option>
                <option value="todo" <?php if ($task["status"] == "todo") echo "selected"; ?>>To Do</option>
                <option value="in_progress" <?php if ($task["status"] == "in_progress") echo "selected"; ?>>In Progress</option>
                <option value="review" <?php if ($task["status"] == "review") echo "selected"; ?>>Review</option>
                <option value="done" <?php if ($task["status"] == "done") echo "selected"; ?>>Done</option>
            </select>
            <small id="editTaskStatusError"></small>
        </div>

        <br>

        <div>
            <label>Due Date</label><br>
            <input type="text" name="due_date" id="edit_task_due_date" value="<?php echo htmlspecialchars($task["due_date"]); ?>" placeholder="YYYY-MM-DD">
            <small id="editTaskDueDateError"></small>
        </div>

        <br>

        <div>
            <label>Estimated Hours</label><br>
            <input type="text" name="estimated_hours" id="edit_task_estimated_hours" value="<?php echo htmlspecialchars($task["estimated_hours"]); ?>">
            <small id="editTaskEstimatedHoursError"></small>
        </div>

        <br>

        <button type="submit">Update Task</button>
    </form>

    <script src="../../assets/js/validation.js"></script>
    
</body>
</html>