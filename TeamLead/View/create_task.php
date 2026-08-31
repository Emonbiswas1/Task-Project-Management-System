<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/user_model.php";
include "../Model/milestone_model.php";

$team_lead_id = $_SESSION["user_id"];
$projects = get_projects_by_teamlead($conn, $team_lead_id);
$members = get_users_by_role($conn, "member");
$milestones = get_milestones_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Create Task</h1>

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

    <form id="taskForm" action="../Controller/task_controller.php" method="POST">
        <input type="hidden" name="action" value="create_task">

        <div>
            <label>Project</label><br>
            <select name="project_id" id="task_project">
                <option value="">Select Project</option>
                <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                        <option value="<?php echo $project["id"]; ?>">
                            <?php echo htmlspecialchars($project["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="taskProjectError"></small>
        </div>

        <br>

        <div>
            <label>Milestone Optional</label><br>
            <select name="milestone_id" id="task_milestone">
                <option value="">No Milestone</option>
                <?php if ($milestones && mysqli_num_rows($milestones) > 0): ?>
                    <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?>
                        <option value="<?php echo $milestone["id"]; ?>">
                            <?php echo htmlspecialchars($milestone["project_name"] . " - " . $milestone["title"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Task Title</label><br>
            <input type="text" name="title" id="task_title">
            <small id="taskTitleError"></small>
        </div>

        <br>

        <div>
            <label>Description</label><br>
            <textarea name="description" id="task_description"></textarea>
            <small id="taskDescriptionError"></small>
        </div>

        <br>

        <div>
            <label>Assign To</label><br>
            <select name="assigned_to" id="task_assigned_to">
                <option value="">Select Member</option>
                <?php if ($members && mysqli_num_rows($members) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($members)): ?>
                        <option value="<?php echo $member["id"]; ?>">
                            <?php echo htmlspecialchars($member["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="taskAssignedError"></small>
        </div>

        <br>

        <div>
            <label>Priority</label><br>
            <select name="priority" id="task_priority">
                <option value="">Select Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
            <small id="taskPriorityError"></small>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="status" id="task_status">
                <option value="">Select Status</option>
                <option value="todo">To Do</option>
                <option value="in_progress">In Progress</option>
                <option value="review">Review</option>
                <option value="done">Done</option>
            </select>
            <small id="taskStatusError"></small>
        </div>

        <br>

        <div>
            <label>Due Date</label><br>
            <input type="text" name="due_date" id="task_due_date" placeholder="YYYY-MM-DD">
            <small id="taskDueDateError"></small>
        </div>

        <br>

        <div>
            <label>Estimated Hours</label><br>
            <input type="text" name="estimated_hours" id="task_estimated_hours">
            <small id="taskEstimatedHoursError"></small>
        </div>

        <br>

        <button type="submit">Create Task</button>
    </form>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>