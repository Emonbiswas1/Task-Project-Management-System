<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/report_model.php";

$team_lead_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

if ($project_id != "" && !is_numeric($project_id)) {
    echo "<h2>Invalid project selected.</h2>";
    echo "<p><a href='time_report.php'>Back</a></p>";
    exit();
}

$projects = get_projects_by_teamlead($conn, $team_lead_id);
$time_report = get_teamlead_project_time_report($conn, $team_lead_id, $project_id);
$total_hours = get_teamlead_project_total_hours($conn, $team_lead_id, $project_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Time Report</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Project Time Report</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="reports.php">Reports</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter Report</h2>

    <form method="GET" action="time_report.php">
        <label>Project</label><br>
        <select name="project_id">
            <option value="">All Projects</option>

            <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                    <option value="<?php echo $project["id"]; ?>" <?php if ($project_id == $project["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($project["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <button type="submit">Filter</button>
        <a href="time_report.php">Reset</a>
    </form>

    <hr>

    <h2>Total Logged Hours</h2>

    <div class="dashboard-card">
        <h3>Total Hours</h3>
        <p><?php echo $total_hours; ?> hour(s)</p>
    </div>

    <hr>

    <h2>Task-wise / Member-wise Time Logs</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Project</th>
            <th>Task</th>
            <th>Member</th>
            <th>Member Email</th>
            <th>Total Hours</th>
            <th>Last Logged At</th>
        </tr>

        <?php if ($time_report && mysqli_num_rows($time_report) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($time_report)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($row["task_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($row["member_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($row["member_email"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($row["total_hours"]); ?></td>
                    <td><?php echo htmlspecialchars($row["last_logged_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No time logs found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>