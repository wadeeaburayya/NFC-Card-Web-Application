<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companyId = isset($_POST['companyId']) ? intval($_POST['companyId']) : 0;
    $cusersId = isset($_POST['cusersId']) ? intval($_POST['cusersId']) : 0;

    if ($companyId > 0) {
        if ($cusersId > 0) {
            $result1 = $conn->query("UPDATE users SET company_id = 1 WHERE company_id = $companyId");
        }
        $result = $conn->query("UPDATE company SET statu = 1 WHERE company_id = $companyId");
        if ($result) {
            echo "Company deactivated successfully";
        } else {
            echo "Error deactivating company";
        }
    } else {
        echo "Invalid company ID";
    }
} else {
    echo "Invalid request method";
}
