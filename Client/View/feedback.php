<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/milestone_model.php";
include "../Model/feedback_model.php";

$client_id = $_SESSION["user_id"];

$milestones = get_client_visible_milestones_all($conn, $client_id);
$feedbacks = get_feedback_by_client($conn, $client_id);

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
        <a href="projects.php">My Projects</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
         <a href="activity_feed.php">Activity Feed</a> |
          <a href="comments_files.php">Comments & Files</a> |
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

    <h2>Submit Milestone Feedback</h2>

    <form id="clientFeedbackForm" action="../../Common/Controller/feedback_controller.php" method="POST">
        <input type="hidden" name="action" value="submit_feedback">

        <div>
            <label>Milestone</label><br>
            <select name="milestone_id" id="feedback_milestone">
                <option value="">Select Milestone</option>
                <?php if ($milestones && mysqli_num_rows($milestones) > 0): ?>
                    <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?>
                        <option value="<?php echo $milestone["id"]; ?>">
                            <?php echo htmlspecialchars($milestone["project_name"] . " - " . $milestone["title"] . " (" . $milestone["status"] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <small id="feedbackMilestoneError"></small>
        </div>

        <br>

        <div>
            <label>Approval Status</label><br>
            <select name="approval_status" id="feedback_status">
                <option value="">Select Status</option>
                <option value="approved">Approved</option>
                <option value="revision_requested">Revision Requested</option>
            </select>
            <small id="feedbackStatusError"></small>
        </div>

        <br>

        <div>
            <label>Feedback Text</label><br>
            <textarea name="feedback_text" id="feedback_text"></textarea>
            <small id="feedbackTextError"></small>
        </div>

        <br>

        <button type="submit">Submit Feedback</button>
    </form>

    <hr>

    <h2>My Submitted Feedback</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Feedback</th>
            <th>Approval Status</th>
            <th>Acknowledged</th>
            <th>Submitted At</th>
        </tr>

        <?php if ($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
            <?php while ($feedback = mysqli_fetch_assoc($feedbacks)): ?>
                <tr>
                    <td><?php echo $feedback["id"]; ?></td>
                    <td><?php echo htmlspecialchars($feedback["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($feedback["milestone_title"] ?? "N/A"); ?></td>
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
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No feedback submitted yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>