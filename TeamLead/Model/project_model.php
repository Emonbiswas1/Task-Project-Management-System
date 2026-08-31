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

function get_project_members_by_teamlead($conn, $team_lead_id) {
    $sql = "SELECT 
                pm.id,
                pm.project_id,
                pm.user_id,
                pm.assigned_at,
                p.name AS project_name,
                u.name AS member_name,
                u.email AS member_email
            FROM project_members pm
            LEFT JOIN projects p ON pm.project_id = p.id
            LEFT JOIN users u ON pm.user_id = u.id
            WHERE p.workspace_id IN (
                SELECT id FROM workspaces WHERE owner_id = ?
            )
            ORDER BY pm.assigned_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function remove_project_member($conn, $project_member_id) {
    $sql = "DELETE FROM project_members WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $project_member_id);

    return mysqli_stmt_execute($stmt);
}

function get_project_member_by_id_and_teamlead($conn, $project_member_id, $team_lead_id) {
    $sql = "SELECT pm.*
            FROM project_members pm
            LEFT JOIN projects p ON pm.project_id = p.id
            WHERE pm.id = ?
            AND p.workspace_id IN (
                SELECT id FROM workspaces WHERE owner_id = ?
            )";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_member_id, $team_lead_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_all_projects_admin($conn, $workspace_id = "", $status = "", $client_id = "") {
    $sql = "SELECT 
                p.*,
                w.name AS workspace_name,
                u.name AS client_name
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE 1";

    $types = "";
    $params = [];

    if ($workspace_id != "") {
        $sql .= " AND p.workspace_id = ?";
        $types .= "i";
        $params[] = $workspace_id;
    }

    if ($status != "") {
        $sql .= " AND p.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    if ($client_id != "") {
        $sql .= " AND p.client_id = ?";
        $types .= "i";
        $params[] = $client_id;
    }

    $sql .= " ORDER BY p.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_project_by_id_and_teamlead($conn, $project_id, $team_lead_id) {
    $sql = "SELECT p.*
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE p.id = ? AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $team_lead_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_project_by_teamlead($conn, $project_id, $workspace_id, $name, $description, $client_id, $deadline, $color_label, $status, $visibility, $team_lead_id) {
    $sql = "UPDATE projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            SET p.workspace_id = ?,
                p.name = ?,
                p.description = ?,
                p.client_id = ?,
                p.deadline = ?,
                p.color_label = ?,
                p.status = ?,
                p.visibility = ?
            WHERE p.id = ? AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ississssii",
        $workspace_id,
        $name,
        $description,
        $client_id,
        $deadline,
        $color_label,
        $status,
        $visibility,
        $project_id,
        $team_lead_id
    );

    return mysqli_stmt_execute($stmt);
}

function archive_project_by_teamlead($conn, $project_id, $team_lead_id) {
    $sql = "UPDATE projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            SET p.status = 'archived'
            WHERE p.id = ?
            AND w.owner_id = ?
            AND p.status = 'completed'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $team_lead_id);

    return mysqli_stmt_execute($stmt);
}

function get_archived_projects_by_teamlead($conn, $team_lead_id) {
    $sql = "SELECT p.*, w.name AS workspace_name, u.name AS client_name
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE w.owner_id = ?
            AND p.status = 'archived'
            ORDER BY p.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}


function get_projects_by_client($conn, $client_id) {
    $sql = "SELECT 
                p.*,
                w.name AS workspace_name
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'
            ORDER BY p.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_client_project_by_id($conn, $project_id, $client_id) {
    $sql = "SELECT 
                p.*,
                w.name AS workspace_name
            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE p.id = ?
            AND p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $project_id, $client_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function count_client_projects($conn, $client_id) {
    $sql = "SELECT COUNT(*) AS total 
            FROM projects 
            WHERE client_id = ?
            AND visibility = 'client_visible'
            AND status != 'archived'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}