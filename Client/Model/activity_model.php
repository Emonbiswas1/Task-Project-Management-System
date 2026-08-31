<?php

function add_activity_log($conn, $workspace_id, $project_id, $user_id, $action_type, $description) {
    $sql = "INSERT INTO activity_logs 
            (workspace_id, project_id, user_id, action_type, description)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iiiss",
        $workspace_id,
        $project_id,
        $user_id,
        $action_type,
        $description
    );

    return mysqli_stmt_execute($stmt);
}

function get_client_activity_feed($conn, $client_id) {
    $sql = "SELECT 
                al.*,
                u.name AS user_name,
                p.name AS project_name,
                w.name AS workspace_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN projects p ON al.project_id = p.id
            LEFT JOIN workspaces w ON al.workspace_id = w.id
            WHERE p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'
            ORDER BY al.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}