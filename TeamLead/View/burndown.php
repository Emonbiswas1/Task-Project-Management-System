<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/report_model.php";

$team_lead_id = $_SESSION["user_id"];
$burndown_data = get_teamlead_burndown_summary($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Burndown Summary</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Burndown-style Summary</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="reports.php">Reports</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Tasks Completed vs Remaining</h2>

    <p>
        This summary shows how many tasks are completed and how many tasks are still remaining based on task creation date.
    </p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Date</th>
            <th>Completed Tasks</th>
            <th>Remaining Tasks</th>
            <th>Total Tasks</th>
            <th>Completion Percentage</th>
        </tr>

        <?php if ($burndown_data && mysqli_num_rows($burndown_data) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($burndown_data)): ?>
                <?php
                    $completed_tasks = $row["completed_tasks"] ?? 0;
                    $remaining_tasks = $row["remaining_tasks"] ?? 0;
                    $total_tasks = $row["total_tasks"] ?? 0;

                    if ($total_tasks > 0) {
                        $completion_percentage = round(($completed_tasks / $total_tasks) * 100, 2);
                    } else {
                        $completion_percentage = 0;
                    }
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($row["task_date"]); ?></td>
                    <td><?php echo $completed_tasks; ?></td>
                    <td><?php echo $remaining_tasks; ?></td>
                    <td><?php echo $total_tasks; ?></td>
                    <td><?php echo $completion_percentage; ?>%</td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No burndown data found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>