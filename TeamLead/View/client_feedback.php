<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/feedback_model.php";

$team_lead_id = $_SESSION["user_id"];
$feedbacks = get_feedback_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Feedback</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Client Feedback</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">Projects</a> |
        <a href="milestones.php">Milestones</a> |
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

    <h2>Feedback List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Client</th>
            <th>Client Email</th>
            <th>Feedback</th>
            <th>Approval Status</th>
            <th>Acknowledged</th>
            <th>Submitted At</th>
            <th>Action</th>
        </tr>

        <?php if ($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
            <?php while ($feedback = mysqli_fetch_assoc($feedbacks)): ?>
                <tr>
                    <td><?php echo $feedback["id"]; ?></td>
                    <td><?php echo htmlspecialchars($feedback["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($feedback["milestone_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($feedback["client_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($feedback["client_email"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($feedback["feedback_text"]); ?></td>
                    <td><?php echo htmlspecialchars($feedback["approval_status"]); ?></td>
                    <td>
                        <?php
                        if ($feedback["is_acknowledged"] == 1) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }
                        ?>
                    </td>
                    <td><?php echo $feedback["created_at"]; ?></td>
                    <td>
                        <?php if ($feedback["is_acknowledged"] != 1): ?>
                            <form class="acknowledge-feedback-form" action="../../Common/Controller/feedback_controller.php" method="POST">
                                <input type="hidden" name="action" value="acknowledge_feedback">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback["id"]; ?>">
                                <button type="submit">Acknowledge</button>
                            </form>
                        <?php else: ?>
                            Acknowledged
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No client feedback found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>