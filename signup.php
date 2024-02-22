<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!preg_match('/^[a-zA-ZçÇğĞıİıIöÖşŞüÜ\s\'-]*$/u', $fullname)) {
        echo "Only letters, Turkish letters, spaces, hyphens, and single quotes are allowed for Full Name.";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format";
        } else {
            $check_query = "SELECT * FROM members WHERE member_email = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                echo "Email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_query = "INSERT INTO members (namesurname, member_email, member_password) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("sss", $fullname, $email, $hashed_password);

                if ($insert_stmt->execute()) {
                    // Retrieve the auto-generated member_id
                    $member_id = mysqli_insert_id($conn);
                    session_start();
                    // Set the member_id in the session
                    $_SESSION['member_id'] = $member_id;

                    $response = "Sign up successful.";
                    echo $response;
                } else {
                    $response = "Sign up failed. Please try again later. Error: " . mysqli_error($conn);
                    echo $response;
                }
            }

            $check_stmt->close();
        }
    }
} else {
    header("Location: sign-up.php");
    exit(); // Make sure to stop execution after redirect
}

$conn->close();
?>
