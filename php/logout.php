<?php

session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["error" => "POST request required"]);
    exit;
}

$_SESSION = [];

session_destroy();

echo json_encode(["success" => true]);

?>
