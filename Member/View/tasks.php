<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["member"]);

include "../../DB/db.php";
include "../Model/task_model.php";

$member_id = $_SESSION["user_id"];
$tasks = get_tasks_by_member($conn, $member_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>My Tasks</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Title</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Estimated Hours</th>
            <th>Action</th>
        </tr>

        <?php if ($tasks && mysqli_num_rows($tasks) > 0): ?>
            <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                <tr>
                    <td><?php echo $task["id"]; ?></td>
                    <td><?php echo htmlspecialchars($task["project_name"] ?? "No Project"); ?></td>
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
                    <td><?php echo htmlspecialchars($task["description"]); ?></td>
                    <td><?php echo htmlspecialchars($task["created_by_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($task["priority"]); ?></td>
                    <td>
                        <select class="member-task-status-select" data-task-id="<?php echo $task["id"]; ?>">
                            <option value="todo" <?php if ($task["status"] == "todo") echo "selected"; ?>>To Do</option>
                            <option value="in_progress" <?php if ($task["status"] == "in_progress") echo "selected"; ?>>In Progress</option>
                            <option value="review" <?php if ($task["status"] == "review") echo "selected"; ?>>Review</option>
                            <option value="done" <?php if ($task["status"] == "done") echo "selected"; ?>>Done</option>
                        </select>
                             <small class="member-task-status-message" id="member-task-status-message-<?php echo $task["id"]; ?>"></small>
                    </td>
                    <td><?php echo htmlspecialchars($task["due_date"]); ?></td>
                    <td><?php echo htmlspecialchars($task["estimated_hours"]); ?></td>
                    <td>
                        <a href="task_detail.php?task_id=<?php echo $task["id"]; ?>">View Details</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">No assigned task found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <script src="../../assets/js/ajax.js"></script>

</body>
</html>