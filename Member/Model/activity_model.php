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
