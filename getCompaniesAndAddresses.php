<?php
require_once 'config.php';
session_start();
$userId = $_SESSION['member_id'];
if (isset($_GET['companyId'])) {
    // Fetch addresses for the selected company
    $companyId = $_GET['companyId'];
    $query = "SELECT address_id, address FROM address WHERE acompany_id = $companyId";
    $result = $conn->query($query);
    $addresses = array();
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }
    // Return addresses as JSON
    echo json_encode(['addresses' => $addresses]);
} else {
    // Fetch companies from the database
    $query = "SELECT company_id, cname FROM company WHERE memberid = '$userId '";
    $result = $conn->query($query);
    $companies = array();
    while ($row = $result->fetch_assoc()) {
        $companies[] = $row;
    }
    // Return companies as JSON
    echo json_encode(['companies' => $companies]);
}
$conn->close();
