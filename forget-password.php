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
          <div class="signup_form">
            <div class="section_title">
              <h2> Forget <span>Password</span> </h2>
              <p>Write your email to recover password.</p>
            </div>
            <form id="login">
              <div class="form-group">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email">
              </div>

              <div id="login-response" class="alert alert-danger d-none"></div>
              <div id="login-success" class="alert alert-success d-none"></div>

              <div class="form-group">
                <button class="btn puprple_btn" type="submit">Send E-mail</button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>
  <!-- Page-wrapper-End -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- bootstrap-js-Link -->

  <script>
    $(document).ready(function() {
      $('#login').submit(function(e) {
        e.preventDefault(); // Prevent the form from submitting normally
        var email = $('#email').val();
        var submitBtn = $('#login button');

        // Disable submit button and show loading animation
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Please wait...');

        $.ajax({
          type: 'POST',
          url: '/forget.php',
          data: {
            email: email
          },
          success: function(response) {
            console.log("Response: " + response);

            // Clear previous messages
            $('#login-response').addClass('d-none').html("");
            $('#login-success').addClass('d-none').html("");

            if (response === "success") {
              $('#login-success').html("A password reset email has been sent. Redirecting to the reset form in 3 seconds.");
              $('#login-success').removeClass('d-none'); // Remove the "d-none" class

              // Wait for 3 seconds and then redirect
              setTimeout(function() {
                window.location.href = "reset_password.php?email=" + email;
              }, 3000);
            } else {
              $('#login-response').html(response);
              $('#login-response').removeClass('d-none'); // Remove the "d-none" class
            }
          },
          error: function(error) {
            console.log("Error: " + error);

            // Clear previous messages
            $('#login-response').addClass('d-none').html("");
            $('#login-success').addClass('d-none').html("");

            $('#login-response').html("Error: Something went wrong. Please try again later.");
            $('#login-response').removeClass('d-none'); // Remove the "d-none" class
          },
          complete: function() {
            // Enable submit button and restore its original text
            submitBtn.prop('disabled', false);
            submitBtn.html('Send E-mail');
          }
        });
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