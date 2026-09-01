<?php

function add_task_attachment($conn, $task_id, $uploaded_by, $file_name, $file_path, $file_size, $is_client_visible) {
    $sql = "INSERT INTO task_attachments 
            (task_id, uploaded_by, file_name, file_path, file_size, is_client_visible)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iissii",
        $task_id,
        $uploaded_by,
        $file_name,
        $file_path,
        $file_size,
        $is_client_visible
    );

    return mysqli_stmt_execute($stmt);
}

function get_attachments_by_task($conn, $task_id) {
    $sql = "SELECT 
                ta.*,
                u.name AS uploaded_by_name,
                u.role AS uploaded_by_role
            FROM task_attachments ta
            LEFT JOIN users u ON ta.uploaded_by = u.id
            WHERE ta.task_id = ?
            ORDER BY ta.uploaded_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}
