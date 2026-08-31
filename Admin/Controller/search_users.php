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
