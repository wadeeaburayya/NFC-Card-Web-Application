<?php

session_start();

if (isset($_SESSION['member_id'])) {
  header("Location: Dashboard.php");
}
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
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
            <a class="navbar-brand" href="#">
              <img src="img/logo/icardlogo.png" alt="image">
            </a>
          </div>
          <!-- Comment Form Section -->
          <div class="signup_form">
            <div class="section_title">
              <h2> <span>Create</span> an account</h2>
              <p>Fill all fields to create new account</p>
            </div>
            <form id="signup-form">
              <div class="form-group">
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Name Surname" required>
              </div>
              <div class="form-group">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
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
              <div id="error-message" class="alert alert-danger" style="display: none;"></div>
              <div class="form-group">
                <button class="btn puprple_btn" type="submit" id="signup-button" disabled>Sign Up</button>
              </div>
              <div id="signup-response" class="alert alert-danger d-none"></div>
              <div id="preloader" class="d-none">
                <div id="loader"></div>
              </div>
              <input type="hidden" id="passwordStrengthInput" name="password_strength" value="weak">
            </form>

            <div class="or_option">
              <p>Already have an account? <a href="sign-in.php">Sign In here</a></p>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
  <!-- Page-wrapper-End -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- bootstrap-js-Link -->
  <script>
    $(document).ready(function() {
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
        if ($('#password').val() !== "") {
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

      $('#password').on('input', function() {
        var password = $(this).val();
        updatePasswordStrengthIndicator(checkPasswordStrength(password));
        updatePasswordRequirements(password);

        // Enable or disable the submit button based on password strength
        var strength = checkPasswordStrength(password);
        if (strength === 'strong') {
          $('#signup-button').prop('disabled', false);
        } else {
          $('#signup-button').prop('disabled', true);
        }
      });
      // Add form submit event listener
      $('#signup-form').submit(function(e) {
        e.preventDefault();
        var fullname = $('#fullname').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();

        if (password !== confirm_password) {
          $('#signup-response').html("Passwords do not match.");
          $('#signup-response').removeClass('d-none');
        } else {
          $.ajax({
            type: 'POST',
            url: '/signup.php',
            data: {
              fullname: fullname,
              email: email,
              password: password
            },
            success: function(response) {
              // You can perform actions based on the response from the server here
              if (response === "Sign up successful.") {
                // Redirect to the login page after a delay
                window.location.href = "Dashboard.php";
              } else {
                $('#signup-response').html(response);
                $('#signup-response').removeClass('d-none');
              }
            },
            error: function() {
              $('#signup-response').html("An error occurred during the signup process.");
              $('#signup-response').removeClass('d-none');
            }
          });
        }
      });
    });
  </script>

  <script src="assets/js/bootstrap.min.js"></script>
  <!-- aos-js-Link -->
  <script src="assets/js/aos.js"></script>
  <!-- main-js-Link -->
  <script src="assets/js/main.js"></script>
</body>

</html>