<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companyId = isset($_POST['companyId']) ? intval($_POST['companyId']) : 0;

    if ($companyId > 0) {
        $result = $conn->query("UPDATE company SET statu = 0 WHERE company_id = $companyId");

        if ($result) {
            echo "Company activated successfully";
        } else {
            echo "Error deleting company";
        }
    } else {
        echo "Invalid company ID";
    }
} else {
    echo "Invalid request method";
}
