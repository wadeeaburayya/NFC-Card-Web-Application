<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cardId = isset($_POST['cardId']) ? intval($_POST['cardId']) : 0;

    if ($cardId > 0) {
        // Perform the deletion by updating the card's status to 1
        $result = $conn->query("UPDATE users SET statu = 1 WHERE cardid = $cardId");

        if ($result) {
            echo "Card deactivated successfully";
        } else {
            echo "Error deactivating card";
        }
    } else {
        echo "Invalid card ID";
    }
} else {
    echo "Invalid request method";
}
?>
