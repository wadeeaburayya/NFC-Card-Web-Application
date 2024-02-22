<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["package_id"])) {
    $userId = $_SESSION['member_id'];
    require_once 'config.php';

    $packageId = $_POST["package_id"];


    $packageResult = $conn->query("SELECT * FROM package WHERE packageid = '$packageId'");
    $package = $packageResult->fetch_assoc();


    $_SESSION['selected_package'] = $package;



    header("Location: packages.php");
    exit();
} else {

    header("Location: error.php");
    exit();
}
