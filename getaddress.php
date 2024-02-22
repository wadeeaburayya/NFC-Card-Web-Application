<?php
session_start();
if (isset($_POST['address_id'])) {
    $addressId = $_POST['address_id'];
    require_once 'config.php';
    $stmt = $conn->prepare("SELECT * FROM address WHERE address_id = ?");
    $stmt->bind_param("i", $addressId);
    $stmt->execute();
    // Bind variables to all columns in the address table
    $stmt->bind_result($address_id, $acompany_id, $aname, $address);
    $stmt->fetch();
    $stmt->close();
    if (isset($address_id)) {
        $addressDetails = array(
            'address_id' => $address_id,
            'acompany_id' => $acompany_id,
            'aname' => $aname,
            'address' => $address,
            /* add more columns if needed */
        );
        echo json_encode($addressDetails);
    } else {
        echo json_encode(array('error' => 'Address not found.'));
    }
    $conn->close();
} else {
    echo json_encode(array('error' => 'Address ID is not set.'));
}
