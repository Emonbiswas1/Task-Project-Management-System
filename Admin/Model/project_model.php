<?php

function count_all_projects($conn) {
    $sql = "SELECT COUNT(*) AS total FROM projects";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_active_projects($conn) {
    $sql = "SELECT COUNT(*) AS total FROM projects WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_teamlead_active_projects($conn, $team_lead_id) {
    $sql = "SELECT COUNT(*) AS total 
            FROM projects 
            WHERE workspace_id IN (
                SELECT workspace_id 
                FROM workspaces 
                WHERE owner_id = ?
            )
            AND status = 'active'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}

function create_project($conn, $workspace_id, $name, $description, $client_id, $deadline, $color_label, $status, $visibility) {
    $sql = "INSERT INTO projects (workspace_id, name, description, client_id, deadline, color_label, status, visibility)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ississss", $workspace_id, $name, $description, $client_id, $deadline, $color_label, $status, $visibility);

    return mysqli_stmt_execute($stmt);
}

function get_projects_by_teamlead($conn, $team_lead_id) {
    $sql = "SELECT p.*, w.name AS workspace_name, u.name AS client_name
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE w.owner_id = ?
            ORDER BY p.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function add_project_member($conn, $project_id, $user_id) {
    $sql = "INSERT INTO project_members (project_id, user_id) VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $user_id);

    return mysqli_stmt_execute($stmt);
}

function check_project_member_exists($conn, $project_id, $user_id) {
    $sql = "SELECT id FROM project_members WHERE project_id = ? AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result) > 0;
}
