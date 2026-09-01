<?php

function add_time_log($conn, $task_id, $user_id, $hours_logged, $note) {
    $sql = "INSERT INTO time_logs 
            (task_id, user_id, hours_logged, note)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iids", $task_id, $user_id, $hours_logged, $note);

    return mysqli_stmt_execute($stmt);
}
