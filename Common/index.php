<!-- <?php

<?php

session_start();

<<<<<<< HEAD
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
=======
include "../../DB/db.php";
include "../Model/user_model.php";

$errors = [];
>>>>>>> 934eeb2f65a2d3eeb85af00d58247bd3720a7688
