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


function get_client_visible_attachments($conn, $client_id, $project_id = "") {
    $sql = "SELECT 
                ta.*,
                t.title AS task_title,
                p.name AS project_name,
                u.name AS uploaded_by_name,
                u.role AS uploaded_by_role
            FROM task_attachments ta
            LEFT JOIN tasks t ON ta.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON ta.uploaded_by = u.id
            WHERE p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'
            AND ta.is_client_visible = 1";

    $types = "i";
    $params = [$client_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    $sql .= " ORDER BY ta.uploaded_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_project_attachments_by_teamlead($conn, $team_lead_id, $project_id = "") {
    $sql = "SELECT 
                ta.*,
                t.title AS task_title,
                p.id AS project_id,
                p.name AS project_name,
                w.name AS workspace_name,
                u.name AS uploaded_by_name,
                u.role AS uploaded_by_role
            FROM task_attachments ta
            LEFT JOIN tasks t ON ta.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON ta.uploaded_by = u.id
            WHERE w.owner_id = ?";

    $types = "i";
    $params = [$team_lead_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    $sql .= " ORDER BY ta.uploaded_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}