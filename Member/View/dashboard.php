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
