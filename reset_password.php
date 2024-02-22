<?php
if (!isset($_GET['email'])) {
    header("Location: sign-in.php");
}
$userEmail = $_GET['email'];
require_once 'config.php';
$getFullname = $conn->query("SELECT * FROM members WHERE member_email = '$userEmail'");
$fullName = $getFullname->fetch_assoc();
$memberid = $fullName['member_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCard.Cool</title>
    <!-- icofont-css-link -->
    <link rel="stylesheet" href="assets/css/icofont.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

    <!-- Owl-Carosal-Style-link -->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <!-- Bootstrap-Style-link -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Aos-Style-link -->
    <link rel="stylesheet" href="assets/css/aos.css">
    <!-- Coustome-Style-link -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Responsive-Style-link -->
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- waveanimation-Style-link -->
    <link rel="stylesheet" href="assets/css/wave-animation-style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/354cf03340.js" crossorigin="anonymous" async></script>
    <!-- Add this CSS for the password strength indicator styles -->
    <style>
        .strength-container {
            display: none;
            /* Initially hide the password strength indicator */
            margin-top: 5px;
        }

        .strength-bar {
            height: 10px;
            background-color: #ddd;
            border-radius: 5px;
            overflow: hidden;
            display: flex;
        }

        .strength-segment {
            height: 100%;
            transition: width 0.3s ease;
        }

        .segment-weak {
            background-color: #ff6b6b;
            /* Red */
        }

        .segment-medium {
            background-color: #f7b731;
            /* Yellow */
        }

        .segment-strong {
            background-color: #4cd137;
            /* Green */
        }

        .indicator-text {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
    </style>



</head>

<body>
    <!-- Page-wrapper-Start -->
    <div class="page_wrapper">
        <!-- Preloader -->
        <div id="preloader">
            <div id="loader"></div>
        </div>
        <div class="full_bg">
            <div class="container">
                <section class="signup_section">
                    <div class="top_part">
                        <a class="navbar-brand" href="dashboard.php">
                            <img src="img/logo/icardlogo.png" alt="image">
                        </a>
                    </div>
                    <div class="signup_form">
                        <div class="section_title">
                            <h2> Reset <span>Password</span> </h2>
                            <p>Create a new password.</p>
                        </div>
                        <form id="resetForm" method="POST" action="reset.php">
                            <input type="hidden" id="fmember_id" name="fmember_id" value="<?php echo $memberid; ?>">
                            <h3 class="text-center mb-3">Welcome <span style="font-style: italic;"><?php echo $fullName['namesurname']; ?></span></h3>
                            <div class="form-group">

                                <?php if (isset($_GET['code'])) {
                                ?>
                                    <input type="text" class="form-control" id="secure_code" name="secure_code" value="<?php echo $_GET['code']; ?>">
                                <?php
                                } else { ?>
                                    <input type="text" class="form-control" id="secure_code" name="secure_code" placeholder="Secure Code">
                                <?php } ?>
                            </div>
                            <div class="form-group">

                                <input type="password" class="form-control" id="password" name="password" placeholder="New Password">
                            </div>


                            <div class="form-group">

                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password">
                            </div>
                            <div id="passwordRequirements" class="text-muted ml-2">
                                Password must have:
                                <ul>
                                    <li id="lengthReq"><i id="lengthIcon" class="fas fa-times"></i> At least 8 characters</li>
                                    <li id="numberReq"><i id="numberIcon" class="fas fa-times"></i> At least one number</li>
                                    <li id="specialCharReq"><i id="specialCharIcon" class="fas fa-times"></i> At least one special character</li>
                                    <!-- Add more requirements as needed -->
                                </ul>
                            </div>
                            <!-- Add this div for the animated password strength indicator -->
                            <div id="passwordStrength" class="mb-3">
                                <span id="indicatorText" class="indicator-text"></span>
                                <div class="strength-container">
                                    <div id="strengthBar" class="strength-bar">
                                        <div class="strength-segment segment-weak"></div>
                                        <div class="strength-segment segment-medium"></div>
                                        <div class="strength-segment segment-strong"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="errorMessage" class="alert alert-danger d-none"></div>

                            <div class="form-group">
                                <button id="changepass" class="btn puprple_btn" type="submit">Change My Password</button>
                            </div>
                            <input type="hidden" id="passwordStrengthInput" name="password_strength" value="weak">
                        </form>

                        <div id="loading" class="d-none">Please wait...</div>
                        <div id="successMessage" class="text-center d-none">
                            <h5>Password changed successfully. <a href="sign-in.php"><span style="color: mediumseagreen;">Click here</span></a> to sign in</h5>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- Page-wrapper-End -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Bootstrap-js-Link -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Aos-js-Link -->
    <script src="assets/js/aos.js"></script>
    <!-- Main-js-Link -->
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            var passwordInput = $("#password");
            var strengthSegments = $(".strength-segment");
            var indicatorText = $("#indicatorText");

            passwordInput.on('input', function() {
                // Check password strength and update the indicator
                var password = passwordInput.val();
                var strength = checkPasswordStrength(password);
                updatePasswordStrengthIndicator(strength);
                updatePasswordRequirements(password);
                var strength = checkPasswordStrength(password);
                if (strength === 'strong') {
                    $('#changepass').prop('disabled', false);
                } else {
                    $('#changepass').prop('disabled', true);
                }
            });

            $("#resetForm").submit(function(e) {
                e.preventDefault();

                // Show loading animation
                $("#loading").removeClass("d-none");

                // Serialize form data
                var formData = $(this).serialize();

                // Make AJAX request
                $.ajax({
                    type: "POST",
                    url: "/reset.php",
                    data: formData,
                    success: function(response) {
                        // Hide loading animation
                        $("#loading").addClass("d-none");

                        if (response === "Password Changed Successfully") {
                            // Hide the form and show success message
                            $("#resetForm").addClass("d-none");
                            $("#successMessage").removeClass("d-none");
                        } else {
                            // Show error message
                            $("#errorMessage").text(response).removeClass("d-none");
                        }
                    },
                    error: function(error) {
                        // Hide loading animation
                        $("#loading").addClass("d-none");

                        // Show error message
                        $("#errorMessage").text("An error occurred. Please try again.").removeClass("d-none");
                    }
                });
            });

            // Function to check password strength
            function checkPasswordStrength(password) {
                // Use zxcvbn for password strength estimation
                var result = zxcvbn(password);

                // Show or hide the strength indicator based on whether the password is empty
                var strengthContainer = $(".strength-container");
                if (password === "") {
                    strengthContainer.hide();
                } else {
                    strengthContainer.show();
                }

                // Map zxcvbn score to strength levels
                if (result.score < 3) {
                    updatePasswordStrengthIndicator("weak");
                } else if (result.score < 4) {
                    updatePasswordStrengthIndicator("medium");
                } else {
                    updatePasswordStrengthIndicator("strong");
                }

                // Return "weak", "medium", or "strong" based on the zxcvbn score
                if (result.score < 3) {
                    return "weak";
                } else if (result.score < 4) {
                    return "medium";
                } else {
                    return "strong";
                }
            }

            // Function to update password strength indicator
            function updatePasswordStrengthIndicator(strength) {
                var strengthSegments = $(".strength-segment");
                var indicatorText = $("#indicatorText");

                // Reset widths and update color based on strength
                strengthSegments.css("width", "0");
                if (strength === "weak") {
                    strengthSegments.eq(0).css("width", "33.33%");
                    indicatorText.text("Weak").removeClass("text-warning text-success").addClass("text-danger");
                } else if (strength === "medium") {
                    strengthSegments.eq(0).css("width", "33.33%");
                    strengthSegments.eq(1).css("width", "33.33%");
                    indicatorText.text("Medium").removeClass("text-danger text-success").addClass("text-warning");
                } else if (strength === "strong") {
                    strengthSegments.css("width", "100%");
                    indicatorText.text("Strong").removeClass("text-danger text-warning").addClass("text-success");
                } else {
                    // Empty password case
                    indicatorText.text("").removeClass("text-danger text-warning text-success");
                    strengthSegments.css("width", "0"); // Add this line to reset the width when the password is empty
                }

                // Show or hide the strength indicator based on whether the password is empty
                var strengthContainer = $(".strength-container");
                if (passwordInput.val() !== "") {
                    strengthContainer.show();
                } else {
                    indicatorText.text("").removeClass("text-danger text-warning text-success");
                    strengthContainer.hide();
                }
            }



            function updatePasswordRequirements(password) {
                var lengthReq = $('#lengthReq');
                var lengthIcon = $('#lengthIcon');
                var numberReq = $('#numberReq');
                var numberIcon = $('#numberIcon');
                var specialCharReq = $('#specialCharReq');
                var specialCharIcon = $('#specialCharIcon');
                // Reset requirements and icons
                lengthReq.removeClass('text-success').addClass('text-muted');
                lengthIcon.removeClass('fa-check text-success').addClass('fa-times text-danger');
                numberReq.removeClass('text-success').addClass('text-muted');
                numberIcon.removeClass('fa-check text-success').addClass('fa-times text-danger');
                specialCharReq.removeClass('text-success').addClass('text-muted');
                specialCharIcon.removeClass('fa-check text-success').addClass('fa-times text-danger');

                // Check and update requirements based on the password
                if (password.length >= 8) {
                    lengthReq.removeClass('text-muted').addClass('text-success');
                    lengthIcon.removeClass('fa-times text-danger').addClass('fa-check text-success');
                }
                if (/\d/.test(password)) {
                    numberReq.removeClass('text-muted').addClass('text-success');
                    numberIcon.removeClass('fa-times text-danger').addClass('fa-check text-success');
                }
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    specialCharReq.removeClass('text-muted').addClass('text-success');
                    specialCharIcon.removeClass('fa-times text-danger').addClass('fa-check text-success');
                }
                // Add more requirements as needed
            }


        });
    </script>
</body>

</html>