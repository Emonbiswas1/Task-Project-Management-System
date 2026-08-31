<?php

session_start();

include "../../DB/db.php";
include "../Model/user_model.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"] ?? "";

    if ($action == "register") {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";
        $role = $_POST["role"] ?? "";
        $company_name = trim($_POST["company_name"] ?? "");

        if ($name == "") {
            $errors[] = "Name is required.";
        }

        if ($email == "") {
            $errors[] = "Email is required.";
        } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        if ($password == "") {
            $errors[] = "Password is required.";
        } 
        elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        if ($password != $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        $allowed_roles = ["member", "team_lead", "client", "admin"];

        if (!in_array($role, $allowed_roles)) {
            $errors[] = "Please select a valid role.";
        }

        if ($role == "client" && $company_name == "") {
            $errors[] = "Company name is required for client registration.";
        }

        $existing_user = get_user_by_email($conn, $email);

        if ($existing_user) {
            $errors[] = "This email is already registered.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/register.php");
            exit();
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $created = create_user($conn, $name, $email, $password_hash, $phone, $role, $company_name);

        if ($created) {
            $_SESSION["success"] = "Registration successful. Please login.";
            header("Location: ../View/login.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Registration failed. Please try again."];
            header("Location: ../View/register.php");
            exit();
        }
    }

    if ($action == "login") {
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($email == "") {
            $errors[] = "Email is required.";
        }

        if ($password == "") {
            $errors[] = "Password is required.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/login.php");
            exit();
        }

        $user = get_user_by_email($conn, $email);

        if (!$user) {
            $_SESSION["errors"] = ["Invalid email or password."];
            header("Location: ../View/login.php");
            exit();
        }

        if ($user["is_active"] != 1) {
            $_SESSION["errors"] = ["Your account is inactive. Please contact admin."];
            header("Location: ../View/login.php");
            exit();
        }

        if (!password_verify($password, $user["password_hash"])) {
            $_SESSION["errors"] = ["Invalid email or password."];
            header("Location: ../View/login.php");
            exit();
        }

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == "member") {
            header("Location: ../../Member/View/dashboard.php");
            exit();
        } 
        elseif ($user["role"] == "team_lead") {
            header("Location: ../../TeamLead/View/dashboard.php");
            exit();
        } 
        elseif ($user["role"] == "client") {
            header("Location: ../../Client/View/dashboard.php");
            exit();
        } 
        elseif ($user["role"] == "admin") {
            header("Location: ../../Admin/View/dashboard.php");
            exit();
        } 
        else {
            header("Location: ../View/login.php");
            exit();
        }
    }
}