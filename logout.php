<?php
session_start();
session_unset();
session_destroy();
echo json_encode(['logged_in' => false]);
?>