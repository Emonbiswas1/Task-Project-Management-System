<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/task_model.php";

$team_lead_id = $_SESSION["user_id"];
$tasks = get_tasks_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Tasks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <h1>Manage Tasks</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="create_task.php">Create New Task</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <?php
    if (isset($_SESSION["success"])) {
        echo "<div style='color:green;'>";
        echo "<p>" . htmlspecialchars($_SESSION["success"]) . "</p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }

    if (isset($_SESSION["errors"])) {
        echo "<div style='color:red;'>";
        foreach ($_SESSION["errors"] as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
        unset($_SESSION["errors"]);
    }
    ?>

    <h2>Task List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Title</th>
            <th>Assigned To</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Estimated Hours</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php if ($tasks && mysqli_num_rows($tasks) > 0): ?>
            <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                <tr>
                    <td><?php echo $task["id"]; ?></td>
                    <td><?php echo htmlspecialchars($task["project_name"]); ?></td>
                    <td>
                        <?php
                        if ($task["milestone_title"]) {
                            echo htmlspecialchars($task["milestone_title"]);
                        } else {
                            echo "No Milestone";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($task["title"]); ?></td>
                    <td><?php echo htmlspecialchars($task["assigned_member"] ?? "Not Assigned"); ?></td>
                    <td><?php echo htmlspecialchars($task["priority"]); ?></td>
                    <td>
                        <select class="task-status-select" data-task-id="<?php echo $task["id"]; ?>">
                            <option value="todo" <?php if ($task["status"] == "todo")
                                echo "selected"; ?>>To Do</option>
                            <option value="in_progress" <?php if ($task["status"] == "in_progress")
                                echo "selected"; ?>>In
                                Progress</option>
                            <option value="review" <?php if ($task["status"] == "review")
                                echo "selected"; ?>>Review</option>
                            <option value="done" <?php if ($task["status"] == "done")
                                echo "selected"; ?>>Done</option>
                        </select>

                        <small class="task-status-message" id="task-status-message-<?php echo $task["id"]; ?>"></small>
                    </td>
                    <td><?php echo htmlspecialchars($task["due_date"]); ?></td>
                    <td><?php echo htmlspecialchars($task["estimated_hours"]); ?></td>
                    <td><?php echo $task["created_at"]; ?></td>
                    <td>
                        <a href="edit_task.php?task_id=<?php echo $task["id"]; ?>">Edit</a>

                        <form action="../Controller/task_controller.php" method="POST" class="delete-task-form" style="display:inline;">
                            <input type="hidden" name="action" value="delete_task">
                            <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">No task found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/ajax.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>

</html>