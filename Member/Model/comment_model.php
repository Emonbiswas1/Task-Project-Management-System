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

function get_project_comments_by_teamlead($conn, $project_id, $team_lead_id) {
    $sql = "SELECT 
                c.*,
                t.title AS task_title,
                p.name AS project_name,
                u.name AS user_name,
                u.role AS user_role
            FROM comments c
            LEFT JOIN tasks t ON c.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON c.user_id = u.id
            WHERE p.id = ?
            AND w.owner_id = ?
            ORDER BY c.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}


function get_client_visible_comments($conn, $client_id, $project_id = "") {
    $sql = "SELECT 
                c.*,
                t.title AS task_title,
                p.name AS project_name,
                u.name AS user_name,
                u.role AS user_role
            FROM comments c
            LEFT JOIN tasks t ON c.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON c.user_id = u.id
            WHERE p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'
            AND c.is_internal = 0";

    $types = "i";
    $params = [$client_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    $sql .= " ORDER BY c.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}