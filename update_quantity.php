<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_quantity'], $_POST['package_id'])) {
    $cardQuantity = intval($_POST['card_quantity']);
    $packageId = $_POST['package_id'];

    // Update the session variable with the new quantity
    $_SESSION['basket'] = $packageId;

    // You may also update the database with the new quantity if needed
    // Example: $conn->query("UPDATE package SET quantity = $cardQuantity WHERE packageid = '$packageId'");
    
    echo 'Quantity updated successfully!';
} else {
    echo 'Invalid request!';
}
?>