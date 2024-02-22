<?php
require_once 'config.php';
session_start();
unset($_SESSION["member_id"]);
header("Location:sign-in.php");
?>