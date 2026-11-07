<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "logged_in" => true,
        "user_name" => $_SESSION['user_name']
    ]);
} else {
    echo json_encode(["logged_in" => false]);
}
