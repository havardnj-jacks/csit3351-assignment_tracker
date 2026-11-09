
<?php

header("Content-Type: application/json");

include "database.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["error" => "POST request required"]);
    exit;
}

$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($username == "" || $email == "" || $password == "") {
    echo json_encode(["error" => "Please fill in all fields"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email address"]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["error" => "Password must be at least 8 characters"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password_hash)
        VALUES (?, ?, ?)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo json_encode(["error" => "Username or email already exists"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Registration failed"]);
    }
}
