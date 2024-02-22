<?php
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["member_id"]) || $_SESSION["member_id"] !== true) {
    header("location: sign-in.php");
    exit;
} elseif (isset($_SESSION["member_id"]) && $_SESSION["member_id"] === true) {
    header("location: Dashboard.php");
    exit;
}
require_once "config.php";
