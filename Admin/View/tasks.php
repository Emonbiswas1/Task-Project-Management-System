<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/task_model.php";
include "../Model/user_model.php";

$status = $_GET["status"] ?? "";
$priority = $_GET["priority"] ?? "";
$assignee_id = $_GET["assignee_id"] ?? "";

$tasks = get_all_tasks_admin($conn, $status, $priority, $assignee_id);
$members = get_users_by_role($conn, "member");

?>

<!DOCTYPE html>
<html>
<head>
    <title>All Tasks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>All Tasks</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">All Projects</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter Tasks</h2>

    <form method="GET" action="tasks.php">
        <label>Status</label><br>
        <select name="status">
            <option value="">All Status</option>
            <option value="todo" <?php if ($status == "todo") echo "selected"; ?>>To Do</option>
            <option value="in_progress" <?php if ($status == "in_progress") echo "selected"; ?>>In Progress</option>
            <option value="review" <?php if ($status == "review") echo "selected"; ?>>Review</option>
            <option value="done" <?php if ($status == "done") echo "selected"; ?>>Done</option>
        </select>

        <br><br>

        <label>Priority</label><br>
        <select name="priority">
            <option value="">All Priority</option>
            <option value="low" <?php if ($priority == "low") echo "selected"; ?>>Low</option>
            <option value="medium" <?php if ($priority == "medium") echo "selected"; ?>>Medium</option>
            <option value="high" <?php if ($priority == "high") echo "selected"; ?>>High</option>
            <option value="critical" <?php if ($priority == "critical") echo "selected"; ?>>Critical</option>
        </select>

        <br><br>

        <label>Assignee</label><br>
        <select name="assignee_id">
            <option value="">All Members</option>
            <?php if ($members && mysqli_num_rows($members) > 0): ?>
                <?php while ($member = mysqli_fetch_assoc($members)): ?>
                    <option value="<?php echo $member["id"]; ?>" <?php if ($assignee_id == $member["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($member["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <br><br>

        <button type="submit">Apply Filter</button>
        <a href="tasks.php">Reset</a>
    </form>

    <hr>

    <h2>Task List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Title</th>
            <th>Description</th>
            <th>Assigned To</th>
            <th>Created By</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Estimated Hours</th>
            <th>Created At</th>
        </tr>

        <?php if ($tasks && mysqli_num_rows($tasks) > 0): ?>
            <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                <tr>
                    <td><?php echo $task["id"]; ?></td>
                    <td><?php echo htmlspecialchars($task["project_name"] ?? "No Project"); ?></td>
                    <td><?php echo htmlspecialchars($task["title"]); ?></td>
                    <td><?php echo htmlspecialchars($task["description"]); ?></td>
                    <td><?php echo htmlspecialchars($task["assigned_member"] ?? "Not Assigned"); ?></td>
                    <td><?php echo htmlspecialchars($task["created_by_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($task["priority"]); ?></td>
                    <td><?php echo htmlspecialchars($task["status"]); ?></td>
                    <td><?php echo htmlspecialchars($task["due_date"]); ?></td>
                    <td><?php echo htmlspecialchars($task["estimated_hours"]); ?></td>
                    <td><?php echo $task["created_at"]; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">No task found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>