<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["member"]);

include "../../DB/db.php";
include "../Model/task_model.php";

$member_id = $_SESSION["user_id"];

$total_tasks = count_member_total_tasks($conn, $member_id);
$todo_tasks = count_member_tasks_by_status($conn, $member_id, "todo");
$in_progress_tasks = count_member_tasks_by_status($conn, $member_id, "in_progress");
$review_tasks = count_member_tasks_by_status($conn, $member_id, "review");
$done_tasks = count_member_tasks_by_status($conn, $member_id, "done");
$overdue_tasks = count_member_overdue_tasks($conn, $member_id);

$tasks = get_tasks_by_member($conn, $member_id);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Member Dashboard</h1>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>

    <p>
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="tasks.php">My Tasks</a> |
        <a href="productivity.php">Productivity Summary</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Task Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Tasks</h3>
            <p><?php echo $total_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>To Do</h3>
            <p><?php echo $todo_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>In Progress</h3>
            <p><?php echo $in_progress_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Review</h3>
            <p><?php echo $review_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Done</h3>
            <p><?php echo $done_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Overdue</h3>
            <p><?php echo $overdue_tasks; ?></p>
        </div>

    </div>

    <hr>

    <h2>Recent Assigned Tasks</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Title</th>
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
                    <td><?php echo htmlspecialchars($task["priority"]); ?></td>
                    <td>
                            <select class="member-task-status-select" data-task-id="<?php echo $task["id"]; ?>">
                                  <option value="todo" <?php if ($task["status"] == "todo") echo "selected"; ?>>To Do</option>
                                  <option value="in_progress" <?php if ($task["status"] == "in_progress") echo "selected"; ?>>In Progress</option>
                                  <option value="review" <?php if ($task["status"] == "review") echo "selected"; ?>>Review</option>
                                  <option value="done" <?php if ($task["status"] == "done") echo "selected"; ?>>Done</option>
                            </select>
                            <small class="member-task-status-message" id="member-task-status-message-dashboard-<?php echo $task["id"]; ?>"></small>
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
                <td colspan="9">No assigned task found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <script src="../../assets/js/ajax.js"></script>
</body>
</html>