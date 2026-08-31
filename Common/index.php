<!-- <?php

<?php

session_start();

header("Content-Type: application/json");

include "../../DB/db.php";
include "../Model/task_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access.",
        "tasks" => []
    ]);
    exit();
}