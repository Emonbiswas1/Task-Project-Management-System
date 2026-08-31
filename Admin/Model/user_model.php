<?php

function get_user_by_email($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function create_user($conn, $name, $email, $password_hash, $phone, $role, $company_name = null) {
    $sql = "INSERT INTO users (name, email, password_hash, phone, role, company_name) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $password_hash, $phone, $role, $company_name);

    return mysqli_stmt_execute($stmt);
}

function count_all_users($conn) {
    $sql = "SELECT COUNT(*) AS total FROM users";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_users_by_role($conn, $role) {
    $sql = "SELECT COUNT(*) AS total FROM users WHERE role = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row["total"];
}

function get_users_by_role($conn, $role) {
    $sql = "SELECT * FROM users 
            WHERE role = ? AND is_active = 1 
            ORDER BY name ASC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_all_users($conn, $search = "") {
    if ($search != "") {
        $search_param = "%" . $search . "%";

        $sql = "SELECT * FROM users 
                WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR role LIKE ?
                ORDER BY created_at DESC";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ssss", $search_param, $search_param, $search_param, $search_param);
        mysqli_stmt_execute($stmt);

        return mysqli_stmt_get_result($stmt);
    } else {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        return mysqli_query($conn, $sql);
    }
}

function get_user_by_id($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_user_status($conn, $user_id, $is_active) {
    $sql = "UPDATE users SET is_active = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $is_active, $user_id);

    return mysqli_stmt_execute($stmt);
}

function update_user_role($conn, $user_id, $role) {
    $sql = "UPDATE users SET role = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "si", $role, $user_id);

    return mysqli_stmt_execute($stmt);
}
