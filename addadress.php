<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit;
}
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acompany_id = $_POST['acompany_id'];
    $aname = $_POST['aname'];
    $address = $_POST['address'];
    if (empty($aname)) {
        echo "Address Name is empty";
        exit;
    }
    if (empty($address)) {
        echo "Address Text is empty";
        exit;
    }
    $insertQuery = "INSERT INTO address (acompany_id, aname, address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
        $stmt->bind_param("iss", $acompany_id, $aname, $address);
        if ($stmt->execute()) {
            echo "Address created successfully";
        } else {
            echo "Address creation failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Unable to prepare statement";
    }
} else {
    echo "Invalid request method";
}
$conn->close();
