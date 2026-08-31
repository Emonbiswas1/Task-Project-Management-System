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
