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
