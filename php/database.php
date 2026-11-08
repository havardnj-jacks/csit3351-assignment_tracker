<?php

$conn = new mysqli("localhost", "root", "", "assignment_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>