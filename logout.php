<?php

session_start();
session_unset();
session_destroy();

header("Location: Common/View/login.php");
exit();