<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["member"]);

include "../../DB/db.php";
include "../Model/report_model.php";

$member_id = $_SESSION["user_id"];

$summary = get_member_productivity_summary($conn, $member_id);
$total_hours = get_member_total_logged_hours($conn, $member_id);
$recent_time_logs = get_member_recent_time_logs($conn, $member_id);
$recent_comments = get_member_recent_comments($conn, $member_id);
$recent_attachments = get_member_recent_attachments($conn, $member_id);

$total_tasks = $summary["total_tasks"] ?? 0;
$completed_tasks = $summary["completed_tasks"] ?? 0;
$pending_tasks = $summary["pending_tasks"] ?? 0;
$overdue_tasks = $summary["overdue_tasks"] ?? 0;

if ($total_tasks > 0) {
    $completion_percentage = round(($completed_tasks / $total_tasks) * 100, 2);
} else {
    $completion_percentage = 0;
}

?>