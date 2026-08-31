<?php

session_start();

header("Content-Type: application/json");

include "../../DB/db.php";
include "../Model/user_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access.",
        "users" => []
    ]);
    exit();
}

$search = trim($_GET["search"] ?? "");

$result = search_users_for_admin($conn, $search);

$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [
            "id" => $row["id"],
            "name" => $row["name"],
            "email" => $row["email"],
            "phone" => $row["phone"],
            "company_name" => $row["company_name"],
            "profile_pic" => $row["profile_pic"],
            "role" => $row["role"],
            "is_active" => $row["is_active"],
            "created_at" => $row["created_at"]
        ];
    }

    echo json_encode([
        "success" => true,
        "users" => $users
    ]);
    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Failed to search users.",
    "users" => []
]);
exit();