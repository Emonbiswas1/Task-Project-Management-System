<?php

function add_time_log($conn, $task_id, $user_id, $hours_logged, $note) {
    $sql = "INSERT INTO time_logs 
            (task_id, user_id, hours_logged, note)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iids", $task_id, $user_id, $hours_logged, $note);

    return mysqli_stmt_execute($stmt);
}
function get_time_logs_by_task_and_member($conn, $task_id, $member_id) {
    $sql = "SELECT 
                tl.*,
                u.name AS member_name
            FROM time_logs tl
            LEFT JOIN users u ON tl.user_id = u.id
            WHERE tl.task_id = ?
            AND tl.user_id = ?
            ORDER BY tl.logged_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $task_id, $member_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_total_hours_by_task_and_member($conn, $task_id, $member_id) {
    $sql = "SELECT SUM(hours_logged) AS total_hours
            FROM time_logs
            WHERE task_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "ii", $task_id, $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row["total_hours"] == null) {
        return 0;
    }

    return $row["total_hours"];
}