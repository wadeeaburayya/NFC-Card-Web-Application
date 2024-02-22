<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cardId = isset($_POST['cardId']) ? intval($_POST['cardId']) : 0;

    if ($cardId > 0) {
        $result = $conn->query("UPDATE users SET statu = 0 WHERE cardid = $cardId");

        if ($result) {
            echo "Card activated successfully";
        } else {
            echo "Error activating card";
        }
    } else {
        echo "Invalid card ID";
    }
} else {
    echo "Invalid request method";
}
?>
