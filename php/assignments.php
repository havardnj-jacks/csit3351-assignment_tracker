
<?php

include "database.php";

$sql = "SELECT
            a.id,
            a.title,
            COALESCE(c.name, '') AS className,
            COALESCE(DATE_FORMAT(a.due_date, '%Y-%m-%d'), '') AS dueDate,
            a.done
        FROM assignments AS a
        LEFT JOIN classes AS c ON a.class_id = c.id
        ORDER BY a.due_date";

$result = $conn->query($sql);

$assignments = [];

while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

header("Content-Type: application/json");

echo json_encode($assignments);

?>
