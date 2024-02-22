<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
}
$userId = $_SESSION['member_id'];
require_once 'config.php';
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$name = $fullName['namesurname'];
$memberid = $fullName['member_id'];
$email = $fullName['member_email'];
$pp = $fullName['pp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = $_SESSION['member_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if any password field is empty
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo "Please fill in all password fields.";
        exit();
    }

    // Check if the current password is correct
    $checkPassword = $conn->prepare("SELECT member_password FROM members WHERE member_id = ?");
    $checkPassword->bind_param("i", $memberId);
    $checkPassword->execute();
    $checkPassword->bind_result($hashedPassword);
    $checkPassword->fetch();
    $checkPassword->close();

    if (!password_verify($currentPassword, $hashedPassword)) {
        echo "Incorrect current password.";
    } elseif ($newPassword !== $confirmPassword) {
        echo "New password and confirm password do not match.";
    } else {
        // Update the password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updatePassword = $conn->prepare("UPDATE members SET member_password = ? WHERE member_id = ?");
        $updatePassword->bind_param("si", $hashedNewPassword, $memberId);
        $updatePassword->execute();
        $updatePassword->close();

        echo "Password changed successfully.";
    }

    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-footer="true">

<head>
    <?php include 'subpages/header.php'; ?>
</head>

<body>
    <div id="root">
        <?php include 'subpages/navbar.php'; ?>
        <main>
            <div class="container">
                <!-- Title and Top Buttons Start -->
                <div class="page-title-container">
                    <div class="row g-0">
                        <!-- Title Start -->
                        <div class="col-auto mb-3 mb-md-0 me-auto">
                            <div class="w-auto sw-md-30">
                                <a href="#" class="muted-link pb-1 d-inline-block breadcrumb-back">
                                    <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                                    <span class="text-small align-middle">Settings</span>
                                </a>
                                <h1 class="mb-0 pb-0 display-4" id="title">General Settings</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->
                <div class="row mb-n5">
                    <div class="col-xl-4">
                        <div class="mb-5">
                            <h2 class="small-title">Change Password</h2>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center flex-column">
                                        <div class="mb-3 d-flex align-items-center flex-column">
                                            <div
                                                class="sw-14 sh-14 mb-3   d-inline-block bg-primary d-flex justify-content-center align-items-center rounded-xl">
                                                <img src="uploads/<?php echo $pp; ?>"
                                                    style="border: 2px solid white; border-radius:50%;" width="105"
                                                    height="105" alt="image">
                                            </div>
                                            <div class="h5 mb-1"><?php echo $name; ?></div>
                                        </div>
                                    </div>
                                    <form id="change-password-form">
                                        <div class="mb-3">
                                            <label for="currentPassword" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="currentPassword"
                                                name="current_password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="newPassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="newPassword"
                                                name="new_password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirmPassword"
                                                name="confirm_password" required>
                                        </div>
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary"
                                                id="change-password-button">Change Password</button>
                                        </div>
                                        <div class="mt-3" id="error-message"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Layout Footer Start -->
        <footer>
            <?php include 'subpages/footer.php'; ?>
        </footer>
        <!-- Layout Footer End -->
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- bootstrap-js-Link -->
    <script>
    $(document).ready(function() {
        $('#change-password-button').click(function() {
            var formData = new FormData($('#change-password-form')[0]);

            $.ajax({
                type: 'POST',
                url: '/change-password.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.trim() === "Password changed successfully.") {
                        $('#error-message').text(response).removeClass('alert alert-danger')
                            .addClass('alert alert-success');
                    } else {
                        $('#error-message').text(response).removeClass(
                            'alert alert-success').addClass('alert alert-danger');
                    }
                },
                error: function() {
                    $('#error-message').text(
                        "An error occurred while processing the request.").addClass(
                        'alert alert-danger');
                }
            });
        });
    });
    </script>
    <?php include 'subpages/footer-js.php'; ?>
</body>

</html>