<?php
session_start();
require_once '../../config/security.php';
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
?>
