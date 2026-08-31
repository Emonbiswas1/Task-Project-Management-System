<?php

session_start();

include "../../DB/db.php";
include "../Model/user_model.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../View/login.php");
    exit();
}

$errors = [];
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "update_profile") {
        $name = trim($_POST["name"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $company_name = trim($_POST["company_name"] ?? "");
        $profile_pic_path = "";

        if ($name == "") {
            $errors[] = "Name is required.";
        }

        if ($phone == "") {
            $errors[] = "Phone is required.";
        }

        if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["name"] != "") {
            $file_name = $_FILES["profile_pic"]["name"];
            $file_tmp = $_FILES["profile_pic"]["tmp_name"];
            $file_size = $_FILES["profile_pic"]["size"];
            $file_error = $_FILES["profile_pic"]["error"];

            if ($file_error !== 0) {
                $errors[] = "Profile picture upload failed.";
            }

            $allowed_extensions = ["jpg", "jpeg", "png"];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
            }

            if ($file_size > 2 * 1024 * 1024) {
                $errors[] = "Profile picture must be less than 2MB.";
            }

            if (empty($errors)) {
                $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
                $upload_folder = "../assets/uploads/profile_pics/";

                if (!is_dir($upload_folder)) {
                    mkdir($upload_folder, 0777, true);
                }

                $destination = $upload_folder . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $profile_pic_path = "assets/uploads/profile_pics/" . $new_file_name;
                } 
                else {
                    $errors[] = "Failed to save profile picture.";
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/profile.php");
            exit();
        }

        $updated = update_user_profile($conn, $user_id, $name, $phone, $company_name, $profile_pic_path);

        if ($updated) {
            $_SESSION["name"] = $name;
            $_SESSION["success"] = "Profile updated successfully.";
            header("Location: ../View/profile.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to update profile."];
            header("Location: ../View/profile.php");
            exit();
        }
    }

    if ($action == "change_password") {
        $current_password = $_POST["current_password"] ?? "";
        $new_password = $_POST["new_password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";

        if ($current_password == "") {
            $errors[] = "Current password is required.";
        }

        if ($new_password == "") {
            $errors[] = "New password is required.";
        } 
        elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters.";
        }

        if ($confirm_password == "") {
            $errors[] = "Confirm password is required.";
        }

        if ($new_password != $confirm_password) {
            $errors[] = "New password and confirm password do not match.";
        }

        $user = get_user_by_id($conn, $user_id);

        if (!$user) {
            $errors[] = "User not found.";
        } 
        elseif (!password_verify($current_password, $user["password_hash"])) {
            $errors[] = "Current password is incorrect.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/profile.php");
            exit();
        }

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $updated = update_user_password($conn, $user_id, $password_hash);

        if ($updated) {
            $_SESSION["success"] = "Password changed successfully.";
            header("Location: ../View/profile.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to change password."];
            header("Location: ../View/profile.php");
            exit();
        }
    }
}

header("Location: ../View/profile.php");
exit();