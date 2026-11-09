<?php

session_start();
header("Content-Type: application/json");
include "database.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["error" => "POST request required"]);
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email == "" || $password == "") {
    echo json_encode(["error" => "Please fill in both fields"]);
    exit;
}

$sql = "SELECT id, username, password_hash
        FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password_hash"])) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["error" => "Invalid email or password"]);
}

?>
