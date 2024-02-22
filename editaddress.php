<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit;
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_id = $_POST['address_id'];
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

    $updateQuery = "UPDATE address SET aname = ?, address = ? WHERE address_id = ?";
    $stmt = $conn->prepare($updateQuery);

    if ($stmt) {
        $stmt->bind_param("ssi", $aname, $address, $address_id);

        if ($stmt->execute()) {
            echo "Address updated successfully";
        } else {
            echo "Address update failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Unable to prepare statement";
    }
} else {
    echo "Invalid request method";
}

$conn->close();
?>
