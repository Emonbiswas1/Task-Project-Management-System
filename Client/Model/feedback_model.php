<?php

function create_client_feedback($conn, $milestone_id, $client_id, $feedback_text, $approval_status) {
    $sql = "INSERT INTO client_feedback 
            (milestone_id, client_id, feedback_text, approval_status)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iiss", $milestone_id, $client_id, $feedback_text, $approval_status);

    return mysqli_stmt_execute($stmt);
}

function get_feedback_by_client($conn, $client_id) {
    $sql = "SELECT 
                cf.*,
                m.title AS milestone_title,
                p.name AS project_name
            FROM client_feedback cf
            LEFT JOIN milestones m ON cf.milestone_id = m.id
            LEFT JOIN projects p ON m.project_id = p.id
            WHERE cf.client_id = ?
            ORDER BY cf.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_feedback_by_milestone_and_client($conn, $milestone_id, $client_id) {
    $sql = "SELECT * FROM client_feedback 
            WHERE milestone_id = ? AND client_id = ?
            ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $milestone_id, $client_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_feedback_by_teamlead($conn, $team_lead_id) {
    $sql = "SELECT 
                cf.*,
                m.title AS milestone_title,
                p.name AS project_name,
                u.name AS client_name,
                u.email AS client_email
            FROM client_feedback cf
            LEFT JOIN milestones m ON cf.milestone_id = m.id
            LEFT JOIN projects p ON m.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            LEFT JOIN users u ON cf.client_id = u.id
            WHERE w.owner_id = ?
            ORDER BY cf.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $team_lead_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_feedback_by_id_and_teamlead($conn, $feedback_id, $team_lead_id) {
    $sql = "SELECT 
                cf.*,
                m.title AS milestone_title,
                p.id AS project_id,
                p.name AS project_name,
                w.id AS workspace_id
            FROM client_feedback cf
            LEFT JOIN milestones m ON cf.milestone_id = m.id
            LEFT JOIN projects p ON m.project_id = p.id
            LEFT JOIN workspaces w ON p.workspace_id = w.id
            WHERE cf.id = ?
            AND w.owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $feedback_id, $team_lead_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function acknowledge_client_feedback($conn, $feedback_id) {
    $sql = "UPDATE client_feedback 
            SET is_acknowledged = 1
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $feedback_id);

    return mysqli_stmt_execute($stmt);
}