<?php

function report_total_users($conn) {
    $sql = "SELECT COUNT(*) AS total FROM users";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_total_workspaces($conn) {
    $sql = "SELECT COUNT(*) AS total FROM workspaces";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_total_projects($conn) {
    $sql = "SELECT COUNT(*) AS total FROM projects";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_total_tasks($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tasks";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_completed_tasks($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tasks WHERE status = 'done'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_pending_milestones($conn) {
    $sql = "SELECT COUNT(*) AS total FROM milestones WHERE status = 'pending'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_completed_milestones($conn) {
    $sql = "SELECT COUNT(*) AS total FROM milestones WHERE status = 'completed'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function report_total_hours_logged($conn) {
    $sql = "SELECT SUM(hours_logged) AS total FROM time_logs";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row["total"] == null) {
        return 0;
    }

    return $row["total"];
}

function report_recent_activity_logs($conn) {
    $sql = "SELECT 
                al.*,
                u.name AS user_name,
                w.name AS workspace_name,
                p.name AS project_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN workspaces w ON al.workspace_id = w.id
            LEFT JOIN projects p ON al.project_id = p.id
            ORDER BY al.created_at DESC
            LIMIT 10";

    return mysqli_query($conn, $sql);
}

function get_teamlead_member_workload($conn, $team_lead_id) {
    $sql = "SELECT 
                u.id AS member_id,
                u.name AS member_name,
                u.email AS member_email,

                SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END) AS todo_count,
                SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                SUM(CASE WHEN t.status = 'review' THEN 1 ELSE 0 END) AS review_count,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) AS done_count,
                SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) AS overdue_count,
                COUNT(t.id) AS total_tasks

            FROM users u
            LEFT JOIN tasks t ON u.id = t.assigned_to
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id

            WHERE u.role = 'member'
            AND w.owner_id = ?

            GROUP BY u.id, u.name, u.email
            ORDER BY total_tasks DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_teamlead_project_progress($conn, $team_lead_id) {
    $sql = "SELECT 
                p.id AS project_id,
                p.name AS project_name,
                p.deadline,
                p.status,

                COUNT(DISTINCT t.id) AS total_tasks,
                SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END) AS todo_count,
                SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                SUM(CASE WHEN t.status = 'review' THEN 1 ELSE 0 END) AS review_count,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) AS done_count,

                COUNT(DISTINCT m.id) AS total_milestones,
                SUM(CASE WHEN m.status = 'completed' THEN 1 ELSE 0 END) AS completed_milestones,

                DATEDIFF(p.deadline, CURDATE()) AS days_remaining

            FROM projects p
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN tasks t ON p.id = t.project_id
            LEFT JOIN milestones m ON p.id = m.project_id

            WHERE w.owner_id = ?
            AND p.status != 'archived'

            GROUP BY p.id, p.name, p.deadline, p.status
            ORDER BY p.deadline ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_teamlead_burndown_summary($conn, $team_lead_id) {
    $sql = "SELECT 
                DATE(t.created_at) AS task_date,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN t.status != 'done' THEN 1 ELSE 0 END) AS remaining_tasks,
                COUNT(t.id) AS total_tasks
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE w.owner_id = ?
            GROUP BY DATE(t.created_at)
            ORDER BY task_date ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_member_productivity_summary($conn, $member_id) {
    $sql = "SELECT 
                COUNT(t.id) AS total_tasks,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN t.status != 'done' THEN 1 ELSE 0 END) AS pending_tasks,
                SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) AS overdue_tasks
            FROM tasks t
            WHERE t.assigned_to = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_member_total_logged_hours($conn, $member_id) {
    $sql = "SELECT SUM(hours_logged) AS total_hours
            FROM time_logs
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row["total_hours"] == null) {
        return 0;
    }

    return $row["total_hours"];
}

function get_member_recent_time_logs($conn, $member_id) {
    $sql = "SELECT 
                tl.*,
                t.title AS task_title,
                p.name AS project_name
            FROM time_logs tl
            LEFT JOIN tasks t ON tl.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            WHERE tl.user_id = ?
            ORDER BY tl.logged_at DESC
            LIMIT 10";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_member_recent_comments($conn, $member_id) {
    $sql = "SELECT 
                c.*,
                t.title AS task_title,
                p.name AS project_name
            FROM comments c
            LEFT JOIN tasks t ON c.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
            LIMIT 10";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_member_recent_attachments($conn, $member_id) {
    $sql = "SELECT 
                ta.*,
                t.title AS task_title,
                p.name AS project_name
            FROM task_attachments ta
            LEFT JOIN tasks t ON ta.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            WHERE ta.uploaded_by = ?
            ORDER BY ta.uploaded_at DESC
            LIMIT 10";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_teamlead_project_time_report($conn, $team_lead_id, $project_id = "") {
    $sql = "SELECT 
                p.id AS project_id,
                p.name AS project_name,
                t.id AS task_id,
                t.title AS task_title,
                u.id AS member_id,
                u.name AS member_name,
                u.email AS member_email,
                SUM(tl.hours_logged) AS total_hours,
                MAX(tl.logged_at) AS last_logged_at
            FROM time_logs tl
            LEFT JOIN tasks t ON tl.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON tl.user_id = u.id
            WHERE w.owner_id = ?";

    $types = "i";
    $params = [$team_lead_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    $sql .= " GROUP BY p.id, p.name, t.id, t.title, u.id, u.name, u.email
              ORDER BY p.name ASC, u.name ASC, t.title ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_teamlead_project_total_hours($conn, $team_lead_id, $project_id = "") {
    $sql = "SELECT SUM(tl.hours_logged) AS total_hours
            FROM time_logs tl
            LEFT JOIN tasks t ON tl.task_id = t.id
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE w.owner_id = ?";

    $types = "i";
    $params = [$team_lead_id];

    if ($project_id != "") {
        $sql .= " AND p.id = ?";
        $types .= "i";
        $params[] = $project_id;
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row["total_hours"] == null) {
        return 0;
    }

    return $row["total_hours"];
}