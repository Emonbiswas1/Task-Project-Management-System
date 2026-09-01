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

