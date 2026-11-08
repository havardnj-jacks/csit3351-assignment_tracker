<?php

header("Content-Type: application/json");

include "database.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["error" => "POST request required"]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$time = trim($_POST["time"] ?? "");
$color = $_POST["color"] ?? "#6c5ce7";

if ($name == "" || $time == "") {
    echo json_encode(["error" => "Please fill in both boxes"]);
    exit;
}

$sql = "INSERT INTO classes (name, meeting_time, color)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $time, $color);
$stmt->execute();

echo json_encode(["success" => true]);

?>