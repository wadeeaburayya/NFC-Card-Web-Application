<?php

require_once 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Check if the member_id exists in the forgetpassword table with status 0
$checkForgetPassword = $conn->prepare("SELECT fmember_id FROM forgetpassword WHERE fmember_id = (SELECT member_id FROM members WHERE member_email = ?) AND status = 0");
$checkForgetPassword->bind_param("s", $email);
$checkForgetPassword->execute();
$checkForgetPassword->store_result();

if ($checkForgetPassword->num_rows > 0) {
    // Update the status to 1
    $updateStatus = $conn->prepare("UPDATE forgetpassword SET status = 1 WHERE fmember_id = (SELECT member_id FROM members WHERE member_email = ?) AND status = 0");
    $updateStatus->bind_param("s", $email);
    $updateStatus->execute();
    $updateStatus->close();
}

$checkForgetPassword->close();

// Proceed with the login check
$stmt = $conn->prepare("SELECT member_id, member_password FROM members WHERE member_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($member_id, $hashed_password);
$stmt->fetch();

if (password_verify($password, $hashed_password)) {
    session_start();
    $_SESSION['member_id'] = $member_id; // Store member_id in the session
    echo "Login successful.";
} else {
    echo "Login failed. Invalid email or password.";
}

$stmt->close();
$conn->close();
?>
