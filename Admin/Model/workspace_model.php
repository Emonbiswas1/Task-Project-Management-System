<?php

function count_all_workspaces($conn) {
    $sql = "SELECT COUNT(*) AS total FROM workspaces";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function count_active_workspaces($conn) {
    $sql = "SELECT COUNT(*) AS total FROM workspaces WHERE is_active = 1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)["total"];
}

function create_workspace($conn, $name, $description, $owner_id, $invite_code, $plan) {
    $sql = "INSERT INTO workspaces (name, description, owner_id, invite_code, plan) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssiss", $name, $description, $owner_id, $invite_code, $plan);

    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }

    return false;
}

function add_workspace_member($conn, $workspace_id, $user_id, $workspace_role) {
    $sql = "INSERT INTO workspace_members (workspace_id, user_id, workspace_role) 
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iis", $workspace_id, $user_id, $workspace_role);

    return mysqli_stmt_execute($stmt);
}

function get_workspaces_by_owner($conn, $owner_id) {
    $sql = "SELECT * FROM workspaces 
            WHERE owner_id = ? 
            ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $owner_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_workspace_by_id_and_owner($conn, $workspace_id, $owner_id) {
    $sql = "SELECT * FROM workspaces 
            WHERE id = ? AND owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $workspace_id, $owner_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_all_workspaces_admin($conn, $search = "") {
    if ($search != "") {
        $search_param = "%" . $search . "%";

        $sql = "SELECT w.*, u.name AS owner_name, u.email AS owner_email
                FROM workspaces w
                LEFT JOIN users u ON w.owner_id = u.id
                WHERE w.name LIKE ?
                OR w.description LIKE ?
                OR w.invite_code LIKE ?
                OR w.plan LIKE ?
                OR u.name LIKE ?
                ORDER BY w.created_at DESC";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $search_param,
            $search_param,
            $search_param,
            $search_param,
            $search_param
        );

        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    $sql = "SELECT w.*, u.name AS owner_name, u.email AS owner_email
            FROM workspaces w
            LEFT JOIN users u ON w.owner_id = u.id
            ORDER BY w.created_at DESC";

    return mysqli_query($conn, $sql);
}

function get_workspace_by_id_admin($conn, $workspace_id) {
    $sql = "SELECT * FROM workspaces WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $workspace_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_workspace_status_admin($conn, $workspace_id, $is_active) {
    $sql = "UPDATE workspaces SET is_active = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $is_active, $workspace_id);

    return mysqli_stmt_execute($stmt);
}

function delete_workspace_admin($conn, $workspace_id) {
    $sql = "DELETE FROM workspaces WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $workspace_id);

    return mysqli_stmt_execute($stmt);
}

function get_workspace_members_admin($conn, $workspace_id) {
    $sql = "SELECT wm.*, u.name, u.email, u.phone, u.role
            FROM workspace_members wm
            LEFT JOIN users u ON wm.user_id = u.id
            WHERE wm.workspace_id = ?
            ORDER BY wm.joined_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $workspace_id);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function get_workspace_member_by_id_admin($conn, $workspace_member_id) {
    $sql = "SELECT * FROM workspace_members WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $workspace_member_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function remove_workspace_member_admin($conn, $workspace_member_id) {
    $sql = "DELETE FROM workspace_members WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $workspace_member_id);

    return mysqli_stmt_execute($stmt);
}

function get_all_workspaces_for_admin($conn) {
    $sql = "SELECT 
                w.*,
                u.name AS owner_name
            FROM workspaces w
            LEFT JOIN users u ON w.owner_id = u.id
            ORDER BY w.created_at DESC";

    return mysqli_query($conn, $sql);
}

function get_all_workspace_members_admin($conn) {
    $sql = "SELECT 
                wm.*,
                w.name AS workspace_name,
                u.name AS user_name,
                u.email AS user_email,
                u.role AS system_role
            FROM workspace_members wm
            LEFT JOIN workspaces w ON wm.workspace_id = w.id
            LEFT JOIN users u ON wm.user_id = u.id
            ORDER BY wm.joined_at DESC";

    return mysqli_query($conn, $sql);
}

function check_workspace_member_exists($conn, $workspace_id, $user_id) {
    $sql = "SELECT * FROM workspace_members 
            WHERE workspace_id = ? 
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $workspace_id, $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function add_workspace_member_by_admin($conn, $workspace_id, $user_id, $workspace_role) {
    $sql = "INSERT INTO workspace_members 
            (workspace_id, user_id, workspace_role)
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iis", $workspace_id, $user_id, $workspace_role);

    return mysqli_stmt_execute($stmt);
}