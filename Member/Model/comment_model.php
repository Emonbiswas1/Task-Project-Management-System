<?php

function create_task_comment($conn, $task_id, $user_id, $body, $is_internal) {
    $sql = "INSERT INTO comments 
            (task_id, user_id, body, is_internal)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iisi", $task_id, $user_id, $body, $is_internal);

    return mysqli_stmt_execute($stmt);
}

function get_comments_by_task($conn, $task_id) {
    $sql = "SELECT 
                c.*,
                u.name AS user_name,
                u.role AS user_role
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.task_id = ?
            ORDER BY c.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

