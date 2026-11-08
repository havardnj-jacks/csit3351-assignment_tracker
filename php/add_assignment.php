
<?php

header("Content-Type: application/json");

include "database.php";

$title = trim($_POST["title"] ?? "");
$className = trim($_POST["className"] ?? "");
$dueDate = $_POST["dueDate"] ?? "";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["error" => "POST request required"]);
    exit;
}

if ($title == "") {
    echo json_encode(["error" => "Please enter a task title"]);
    exit;
}

if ($dueDate == "") {
    $dueDate = null;
}

$classId = null;

if ($className != "") {
    $sql = "SELECT id FROM classes WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $className);
    $stmt->execute();

    $result = $stmt->get_result();
    $class = $result->fetch_assoc();

    if (!$class) {
        echo json_encode(["error" => "Class not found"]);
        exit;
    }

    $classId = $class["id"];
}

$sql = "INSERT INTO assignments (title, class_id, due_date, done)
        VALUES (?, ?, ?, 0)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $title, $classId, $dueDate);
$stmt->execute();

echo json_encode(["success" => true]);

?>
