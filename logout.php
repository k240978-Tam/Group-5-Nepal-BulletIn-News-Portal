<?php
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

session_unset();
session_destroy();
session_start();

$_SESSION['success_message'] = "You have been logged out successfully.";
redirect('/index.php');
?>
