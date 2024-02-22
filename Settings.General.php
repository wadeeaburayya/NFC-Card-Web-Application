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
                            <h2 class="small-title">Details</h2>
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
                                    <form id="user-edit-form" enctype="multipart/form-data">
                                        <input type="hidden" name="member_id" value="<?php echo $memberid; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="namesurname"
                                                value="<?php echo $name; ?>" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="member_email"
                                                value="<?php echo $email; ?>" required />
                                        </div>
                                        <div class="mb-0">
                                            <label for="FuTaskDoc" class="btn btn-info">Choose Picture
                                                <!--<i class="fa-solid fa-cloud-arrow-up"></i>-->
                                            </label>
                                            <input type="file" id="FuTaskDoc" name="fileToUpload" hidden="hidden">
                                            <a href="change-password.php" class="btn btn-info">Change Password</a>
                                            <button id="update-button" type="button"
                                                class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto">
                                                <i data-acorn-icon="save"></i>
                                                <span>Save</span>
                                            </button>
                                        </div>
                                    </form>
                                    <div class="mt-3" id="error-message"></div>
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
        $('#update-button').click(function() {
            var formData = new FormData($('#user-edit-form')[0]);

            var fileInput = $('#user-edit-form input[name="fileToUpload"]')[0];
            formData.append('fileToUpload', fileInput.files[0]);

            // Check if the name is valid before making the AJAX request
            var nameInput = $('#user-edit-form input[name="namesurname"]');
            if (!isValidName(nameInput.val())) {
                $('#error-message').text("Name cannot contain special characters like @, ., *, or -.")
                    .addClass('alert alert-danger');
                return;
            }

            // Check if the email is valid before making the AJAX request
            var emailInput = $('#user-edit-form input[name="member_email"]');
            if (!isValidEmail(emailInput.val())) {
                $('#error-message').text("Please enter a valid email address.").addClass(
                    'alert alert-danger');
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/updatemember.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.trim() === "Card data updated successfully.") {
                        window.location.reload();
                    } else {
                        $('#error-message').text(response).addClass('alert alert-danger');
                    }
                },
                error: function() {
                    $('#error-message').text(
                        "An error occurred while processing the request.").addClass(
                        'alert alert-danger');
                }
            });
        });

        // Function to check if the entered name is valid
        function isValidName(name) {
            var nameRegex = /^[^@.*-]+$/;
            return nameRegex.test(name);
        }

        // Function to check if the entered email is valid
        function isValidEmail(email) {
            var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }
    });
    </script>
    <?php include 'subpages/footer-js.php'; ?>
</body>

</html>