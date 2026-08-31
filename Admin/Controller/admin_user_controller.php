<?php

session_start();

include "../../DB/db.php";
include "../Model/user_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST["action"] ?? "";
    
    if ($action == "create_user") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $company_name = trim($_POST["company_name"] ?? "");
    $role = $_POST["role"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($name == "") {
        $errors[] = "Name is required .";
    }

    if ($email == "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $existing_user = get_user_by_email($conn, $email);

        if ($existing_user) {
            $errors[] = "Email already exists.";
        }
    }

    if ($phone == "") {
        $errors[] = "Phone is required.";
    }

    $allowed_roles = ["member", "team_lead", "client", "admin"];

    if (!in_array($role, $allowed_roles)) {
        $errors[] = "Invalid role selected.";
    }

    if ($password == "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($confirm_password == "") {
        $errors[] = "Confirm password is required.";
    }

    if ($password != $confirm_password) {
        $errors[] = "Password and confirm password do not match.";
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        header("Location: ../View/create_user.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $created_user_id = create_user_by_admin(
        $conn,
        $name,
        $email,
        $password_hash,
        $role,
        $phone,
        $company_name
    );

    if ($created_user_id) {

            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    null,
                    null,
                    $_SESSION["user_id"],
                    "admin_user_created",
                    "Admin created new " . $role . " account: " . $name
                );
            }

            $_SESSION["success"] = "User created successfully.";
            header("Location: ../View/users.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to create user."];
            header("Location: ../View/create_user.php");
            exit();
        }
    }

    if ($action == "change_status") {
        $user_id = $_POST["user_id"] ?? "";
        $is_active = $_POST["is_active"] ?? "";

        if ($user_id == "" || !is_numeric($user_id)) {
            $errors[] = "Invalid user selected.";
        }

        if ($is_active !== "0" && $is_active !== "1") {
            $errors[] = "Invalid status selected.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/users.php");
            exit();
        }

        $user = get_user_by_id($conn, $user_id);

        if (!$user) {
            $_SESSION["errors"] = ["User not found."];
            header("Location: ../View/users.php");
            exit();
        }

        if ($user["id"] == $_SESSION["user_id"] && $is_active == "0") {
            $_SESSION["errors"] = ["You cannot deactivate your own account."];
            header("Location: ../View/users.php");
            exit();
        }

        $updated = update_user_status($conn, $user_id, $is_active);

        if ($updated) {
            $_SESSION["success"] = "User status updated successfully.";
            header("Location: ../View/users.php");
            exit();
        } else {
            $_SESSION["errors"] = ["Failed to update user status."];
            header("Location: ../View/users.php");
            exit();
        }
    }

    if ($action == "change_role") {
        $user_id = $_POST["user_id"] ?? "";
        $role = $_POST["role"] ?? "";

        if ($user_id == "" || !is_numeric($user_id)) {
            $errors[] = "Invalid user selected.";
        }

        $allowed_roles = ["member", "team_lead", "client", "admin"];

        if (!in_array($role, $allowed_roles)) {
            $errors[] = "Invalid role selected.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/users.php");
            exit();
        }

        $user = get_user_by_id($conn, $user_id);

        if (!$user) {
            $_SESSION["errors"] = ["User not found."];
            header("Location: ../View/users.php");
            exit();
        }

        if ($user["id"] == $_SESSION["user_id"] && $role != "admin") {
            $_SESSION["errors"] = ["You cannot remove your own admin role."];
            header("Location: ../View/users.php");
            exit();
        }

        $updated = update_user_role($conn, $user_id, $role);

        if ($updated) {
            $_SESSION["success"] = "User role updated successfully.";
            header("Location: ../View/users.php");
            exit();
        } else {
            $_SESSION["errors"] = ["Failed to update user role."];
            header("Location: ../View/users.php");
         exit();
        }
    }
}