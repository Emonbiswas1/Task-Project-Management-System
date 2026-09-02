<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/user_model.php";

$team_lead_id = $_SESSION["user_id"];
$projects = get_projects_by_teamlead($conn, $team_lead_id);
$members = get_users_by_role($conn, "member");
$project_members = get_project_members_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Project Members</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Manage Project Members</h1>

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

    <h2>Assign Member to Project</h2>

    <form id="projectMemberForm" action="../Controller/project_member_controller.php" method="POST">
        <input type="hidden" name="action" value="assign_member">

        <div>
            <label>Project</label><br>
            <select name="project_id" id="pm_project">
                <option value="">Select Project</option>
                <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                        <option value="<?php echo $project["id"]; ?>">
                            <?php echo htmlspecialchars($project["name"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="pmProjectError"></small>
        </div>

        <br>

        <div>
            <label>Member</label><br>
            <select name="user_id" id="pm_user">
                <option value="">Select Member</option>
                <?php if ($members && mysqli_num_rows($members) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($members)): ?>
                        <option value="<?php echo $member["id"]; ?>">
                            <?php echo htmlspecialchars($member["name"] . " - " . $member["email"]); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="pmUserError"></small>
        </div>

        <br>

        <button type="submit">Assign Member</button>
    </form>

    <hr>

    <h2>Assigned Project Members</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Member Name</th>
            <th>Email</th>
            <th>Assigned At</th>
            <th>Action</th>
        </tr>

        <?php if ($project_members && mysqli_num_rows($project_members) > 0): ?>
            <?php while ($pm = mysqli_fetch_assoc($project_members)): ?>
                <tr>
                    <td><?php echo $pm["id"]; ?></td>
                    <td><?php echo htmlspecialchars($pm["project_name"]); ?></td>
                    <td><?php echo htmlspecialchars($pm["member_name"]); ?></td>
                    <td><?php echo htmlspecialchars($pm["member_email"]); ?></td>
                    <td><?php echo $pm["assigned_at"]; ?></td>
                    <td>
                        <form action="../Controller/project_member_controller.php" method="POST">
                            <input type="hidden" name="action" value="remove_member">
                            <input type="hidden" name="project_member_id" value="<?php echo $pm["id"]; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No project member assigned yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>