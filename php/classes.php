<?php

include "database.php";

$sql = "SELECT id, name, meeting_time AS time, color FROM classes";

$result = $conn->query($sql);

$classes = [];


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

header("Content-Type: application/json");

echo json_encode($classes);

?>