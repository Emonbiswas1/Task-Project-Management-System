<?php

function count_all_tasks($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tasks";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_tasks_created_today($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE DATE(created_at) = CURDATE()";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_overdue_tasks($conn) {
    $sql = "SELECT COUNT(*) AS total 
            FROM tasks 
            WHERE due_date < CURDATE() 
            AND status != 'done'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_teamlead_total_tasks($conn, $team_lead_id) {
    $sql = "SELECT COUNT(*) AS total
            FROM tasks
            WHERE project_id IN (
                SELECT id 
                FROM projects 
                WHERE workspace_id IN (
                    SELECT id 
                    FROM workspaces 
                    WHERE owner_id = ?
                )
            )";

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

function count_teamlead_overdue_tasks($conn, $team_lead_id) {
    $sql = "SELECT COUNT(*) AS total
            FROM tasks
            WHERE due_date < CURDATE()
            AND status != 'done'
            AND project_id IN (
                SELECT id 
                FROM projects 
                WHERE workspace_id IN (
                    SELECT id 
                    FROM workspaces 
                    WHERE owner_id = ?
                )
            )";

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

function create_task($conn, $project_id, $milestone_id, $title, $description, $assigned_to, $created_by, $priority, $status, $due_date, $estimated_hours) {
    $sql = "INSERT INTO tasks 
            (project_id, milestone_id, title, description, assigned_to, created_by, priority, status, due_date, estimated_hours)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iissiisssd",
        $project_id,
        $milestone_id,
        $title,
        $description,
        $assigned_to,
        $created_by,
        $priority,
        $status,
        $due_date,
        $estimated_hours
    );

    return mysqli_stmt_execute($stmt);
}

function get_tasks_by_teamlead($conn, $team_lead_id) {
    $sql = "SELECT 
                t.*,
                p.name AS project_name,
                u.name AS assigned_member,
                m.title AS milestone_title
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN milestones m ON t.milestone_id = m.id
            WHERE p.workspace_id IN (
                SELECT id FROM workspaces WHERE owner_id = ?
            )
            ORDER BY t.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function update_task_status_by_teamlead($conn, $task_id, $status, $team_lead_id) {
    $sql = "UPDATE tasks 
            SET status = ?,
                completed_at = CASE WHEN ? = 'done' THEN NOW() ELSE completed_at END
            WHERE id = ?
            AND project_id IN (
                SELECT id FROM projects
                WHERE workspace_id IN (
                    SELECT id FROM workspaces WHERE owner_id = ?
                )
            )";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssii", $status, $status, $task_id, $team_lead_id);

    return mysqli_stmt_execute($stmt);
}

function get_all_tasks_admin($conn, $status = "", $priority = "", $assignee_id = "") {
    $sql = "SELECT 
                t.*,
                p.name AS project_name,
                u.name AS assigned_member,
                creator.name AS created_by_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN users creator ON t.created_by = creator.id
            WHERE 1";

    $types = "";
    $params = [];

    if ($status != "") {
        $sql .= " AND t.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    if ($priority != "") {
        $sql .= " AND t.priority = ?";
        $types .= "s";
        $params[] = $priority;
    }

    if ($assignee_id != "") {
        $sql .= " AND t.assigned_to = ?";
        $types .= "i";
        $params[] = $assignee_id;
    }

    $sql .= " ORDER BY t.created_at DESC";

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

function get_task_by_id_and_teamlead($conn, $task_id, $team_lead_id) {
    $sql = "SELECT t.*
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE t.id = ? AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $task_id, $team_lead_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_task_by_teamlead($conn, $task_id, $project_id, $milestone_id, $title, $description, $assigned_to, $priority, $status, $due_date, $estimated_hours, $team_lead_id) {
    $sql = "UPDATE tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            SET t.project_id = ?,
                t.milestone_id = ?,
                t.title = ?,
                t.description = ?,
                t.assigned_to = ?,
                t.priority = ?,
                t.status = ?,
                t.due_date = ?,
                t.estimated_hours = ?,
                t.completed_at = CASE WHEN ? = 'done' THEN NOW() ELSE t.completed_at END
            WHERE t.id = ? AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iississsdsii",
        $project_id,
        $milestone_id,
        $title,
        $description,
        $assigned_to,
        $priority,
        $status,
        $due_date,
        $estimated_hours,
        $status,
        $task_id,
        $team_lead_id
    );

    return mysqli_stmt_execute($stmt);
}

function delete_task_by_teamlead($conn, $task_id, $team_lead_id) {
    $sql = "DELETE t
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE t.id = ? AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $task_id, $team_lead_id);

    return mysqli_stmt_execute($stmt);
}
function count_member_tasks_by_status($conn, $member_id, $status) {
    $sql = "SELECT COUNT(*) AS total 
            FROM tasks 
            WHERE assigned_to = ? 
            AND status = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "is", $member_id, $status);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}

function count_member_total_tasks($conn, $member_id) {
    $sql = "SELECT COUNT(*) AS total 
            FROM tasks 
            WHERE assigned_to = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}

function count_member_overdue_tasks($conn, $member_id) {
    $sql = "SELECT COUNT(*) AS total 
            FROM tasks 
            WHERE assigned_to = ?
            AND due_date < CURDATE()
            AND status != 'done'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}

function get_tasks_by_member($conn, $member_id) {
    $sql = "SELECT 
                t.*,
                p.name AS project_name,
                m.title AS milestone_title,
                creator.name AS created_by_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN milestones m ON t.milestone_id = m.id
            LEFT JOIN users creator ON t.created_by = creator.id
            WHERE t.assigned_to = ?
            ORDER BY t.due_date ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_member_task_by_id($conn, $task_id, $member_id) {
    $sql = "SELECT 
                t.*,
                p.name AS project_name,
                p.description AS project_description,
                m.title AS milestone_title,
                creator.name AS created_by_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN milestones m ON t.milestone_id = m.id
            LEFT JOIN users creator ON t.created_by = creator.id
            WHERE t.id = ?
            AND t.assigned_to = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $task_id, $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
function update_task_status_by_member($conn, $task_id, $status, $member_id) {
    $sql = "UPDATE tasks 
            SET status = ?,
                completed_at = CASE WHEN ? = 'done' THEN NOW() ELSE completed_at END
            WHERE id = ?
            AND assigned_to = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssii", $status, $status, $task_id, $member_id);

    return mysqli_stmt_execute($stmt);
}


function get_client_visible_tasks($conn, $client_id, $project_id = "", $status = "") {
    $sql = "SELECT 
                t.*,
                p.name AS project_name,
                m.title AS milestone_title,
                u.name AS assigned_member
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN milestones m ON t.milestone_id = m.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE p.client_id = ?
            AND p.visibility = 'client_visible'
            AND p.status != 'archived'";

    $types = "i";
    $params = [$client_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    if ($status != "") {
        $sql .= " AND t.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    $sql .= " ORDER BY t.due_date ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}