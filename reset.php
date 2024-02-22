<?php
require_once 'config.php'; // Database connection

/*function isPasswordStrong($password) {
    // Implement your password strength criteria here
    // For example, at least 8 characters with a mix of uppercase, lowercase, numbers, and special characters
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fmember_id = $_POST['fmember_id'];
    $secure_code = $_POST['secure_code'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Check if the combination of fmember_id, secure_code, and status is valid
    $checkCodeQuery = "SELECT fmember_id FROM forgetpassword WHERE fmember_id = ? AND secure_code = ? AND status = 0";
    $checkCodeStmt = $conn->prepare($checkCodeQuery);
    $checkCodeStmt->bind_param("is", $fmember_id, $secure_code);
    $checkCodeStmt->execute();
    $checkCodeStmt->store_result();

    if ($checkCodeStmt->num_rows > 0) {
        // Check if passwords are not empty
        if (empty($password) || empty($cpassword)) {
            echo "Passwords cannot be empty";
        } elseif ($password != $cpassword) {
            echo "Passwords do not match";
        } else {
            // Update members table with the new password
            $updatePasswordQuery = "UPDATE members SET member_password = ? WHERE member_id = ?";
            $updatePasswordStmt = $conn->prepare($updatePasswordQuery);
            
            // Use a separate variable for binding parameters
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updatePasswordStmt->bind_param("si", $hashedPassword, $fmember_id);

            // Update status in forgetpassword table
            $updateStatusQuery = "UPDATE forgetpassword SET status = 1 WHERE fmember_id = ?";
            $updateStatusStmt = $conn->prepare($updateStatusQuery);
            $updateStatusStmt->bind_param("i", $fmember_id);

            if ($updatePasswordStmt->execute() && $updateStatusStmt->execute()) {
                echo "Password Changed Successfully";
            } else {
                echo "Error updating password";
            }

            $updatePasswordStmt->close();
            $updateStatusStmt->close();
        }
    } else {
        echo "Invalid Secure Code";
    }

    $checkCodeStmt->close();
    $conn->close();
} else {
    // Handle cases where the request method is not POST
    echo "Invalid request method.";
}
?>
