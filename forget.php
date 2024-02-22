<?php
require 'config.php'; // Database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if there is already an active request for the given email
    $checkExistingRequestQuery = "SELECT status FROM forgetpassword WHERE fmember_id IN (SELECT member_id FROM members WHERE member_email = ?) AND status = 0";
    $checkExistingRequestStmt = $conn->prepare($checkExistingRequestQuery);
    $checkExistingRequestStmt->bind_param("s", $email);
    $checkExistingRequestStmt->execute();
    $checkExistingRequestStmt->store_result();

    if ($checkExistingRequestStmt->num_rows > 0) {
        echo "An active password reset request already exists for this email.";
        exit;
    }

    // Continue with the code if there is no active request

    // Check if the email exists in the users table
    $checkEmailQuery = "SELECT member_id, member_email FROM members WHERE member_email = ?";
    $checkStmt = $conn->prepare($checkEmailQuery);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Generate a random secure code
        $secureCode = bin2hex(random_bytes(8));

        // Insert the secure code and status into the forgetpassword table
        $insertCodeQuery = "INSERT INTO forgetpassword (secure_code, status, fmember_id) VALUES (?, 0, ?)";
        $insertCodeStmt = $conn->prepare($insertCodeQuery);
        $member_id = null;

        $checkStmt->bind_result($member_id, $email);
        $checkStmt->fetch();

        $insertCodeStmt->bind_param("si", $secureCode, $member_id);
        if ($insertCodeStmt->execute()) {
            // Send an email with the secure code and reset password link
            $mail = new PHPMailer(true);
            //Server settings
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'mail.example.cool'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'info@icard.cool'; // SMTP username
            $mail->Password = ''; // SMTP password
            $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, [ICODE]ssl[/ICODE] also accepted
            $mail->Port = 465; // TCP port to connect to    

            $mail->setFrom('info@icard.cool', 'iCard Cool'); // Set the "From" address
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset";
            $mail->Body = "To reset your password, click the following link: <br>";
            $mail->Body .= "Secure Code: $secureCode<br>";
            $mail->Body .= "Reset Password Link: <a target=\"_blank\" href='reset_password.php?email=$email&code=$secureCode'>reset_password.php?email=$email&code=$secureCode";

            try {
                // Send the email
                $mail->send();
                echo "success";
            } catch (Exception $e) {
                echo "Error: Email could not be sent. Please try again later.";
                exit;
            }
            $insertCodeStmt->close();
        } else {
            echo "Error: Secure code insertion failed.";
            exit;
        }
    } else {
        echo "Email does not exist in our records.";
        exit;
    }

    $checkStmt->close();
    $conn->close();
    $checkExistingRequestStmt->close();
}
?>
