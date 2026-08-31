<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/report_model.php";

$team_lead_id = $_SESSION["user_id"];

$workloads = get_teamlead_member_workload($conn, $team_lead_id);
$project_progress = get_teamlead_project_progress($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Lead Reports</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Team Lead Reports</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="milestones.php">Milestones</a> |
        <a href="burndown.php">Burndown Summary</a> |
        <a href="time_report.php">Time Report</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Team Member Workload</h2>

    <p>This report shows task count by status for each team member.</p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Member</th>
            <th>Email</th>
            <th>To Do</th>
            <th>In Progress</th>
            <th>Review</th>
            <th>Done</th>
            <th>Overdue</th>
            <th>Total Tasks</th>
        </tr>

        <?php if ($workloads && mysqli_num_rows($workloads) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($workloads)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["member_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["member_email"]); ?></td>
                    <td><?php echo $row["todo_count"] ?? 0; ?></td>
                    <td><?php echo $row["in_progress_count"] ?? 0; ?></td>
                    <td><?php echo $row["review_count"] ?? 0; ?></td>
                    <td><?php echo $row["done_count"] ?? 0; ?></td>
                    <td><?php echo $row["overdue_count"] ?? 0; ?></td>
                    <td><?php echo $row["total_tasks"] ?? 0; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No workload data found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <hr>

    <h2>Project Progress</h2>

    <p>This report shows project task progress, milestone completion rate, and days remaining.</p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Project</th>
            <th>Status</th>
            <th>Deadline</th>
            <th>Days Remaining</th>
            <th>Total Tasks</th>
            <th>Done Tasks</th>
            <th>Task Progress</th>
            <th>Total Milestones</th>
            <th>Completed Milestones</th>
            <th>Milestone Progress</th>
        </tr>

        <?php if ($project_progress && mysqli_num_rows($project_progress) > 0): ?>
            <?php while ($project = mysqli_fetch_assoc($project_progress)): ?>

                <?php
                    $total_tasks = $project["total_tasks"] ?? 0;
                    $done_tasks = $project["done_count"] ?? 0;

                    if ($total_tasks > 0) {
                        $task_progress = round(($done_tasks / $total_tasks) * 100, 2);
                    } else {
                        $task_progress = 0;
                    }

                    $total_milestones = $project["total_milestones"] ?? 0;
                    $completed_milestones = $project["completed_milestones"] ?? 0;

                    if ($total_milestones > 0) {
                        $milestone_progress = round(($completed_milestones / $total_milestones) * 100, 2);
                    } else {
                        $milestone_progress = 0;
                    }

                    $days_remaining = $project["days_remaining"];

                    if ($days_remaining === null) {
                        $days_text = "No deadline";
                    } elseif ($days_remaining < 0) {
                        $days_text = "Overdue by " . abs($days_remaining) . " day(s)";
                    } else {
                        $days_text = $days_remaining . " day(s)";
                    }
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($project["project_name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["status"]); ?></td>
                    <td><?php echo htmlspecialchars($project["deadline"]); ?></td>
                    <td><?php echo htmlspecialchars($days_text); ?></td>
                    <td><?php echo $total_tasks; ?></td>
                    <td><?php echo $done_tasks; ?></td>
                    <td><?php echo $task_progress; ?>%</td>
                    <td><?php echo $total_milestones; ?></td>
                    <td><?php echo $completed_milestones; ?></td>
                    <td><?php echo $milestone_progress; ?>%</td>
                </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No project progress data found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>