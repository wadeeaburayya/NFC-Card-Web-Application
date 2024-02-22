<?php
require_once 'config.php';
ob_start(); // Start output buffering
$merchantId = '';
$secretKey = '';
$password = '';
$orderId = isset($_GET['orderid']) ? $_GET['orderid'] : null;

if ($orderId) {

    function generateHash($hashStr, $secret, $password)
    {
        return base64_encode(hash_hmac('sha256', $hashStr . $password, $secret, true));
    }

    function queryPaymentResult($merchantId, $orderId, $secretKey, $password)
    {
        // Set the TIKO payment result query API endpoint
        $apiUrl = 'https://www.tikokart.com/api-sanalpos/payment/status';
        // Generate the hash code for the query
        $hashStr = $merchantId . $orderId;
        $hash = generateHash($hashStr, $secretKey, $password);
        // Prepare the POST data
        $postData = [
            'MerchantId' => $merchantId,
            'OrderId' => $orderId,
            'Hash' => $hash,
        ];
        // Initialize cURL session
        $ch = curl_init($apiUrl);
        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (not recommended for production)
        // Execute cURL session
        $response = curl_exec($ch);
        // Close cURL session
        curl_close($ch);
        // Decode the JSON response
        $result = json_decode($response, true);
        return $result;
    }

    // Query the payment result
    $result = queryPaymentResult($merchantId, $orderId, $secretKey, $password);

    if ($result['Status'] == '200') {
        
        // Update the status in the billing table to 1
        $stmt = $conn->prepare("UPDATE billing SET status = 1 WHERE order_id = ?");
        if (!$stmt) {
            die("Error in prepare statement: " . $conn->error);
        }

        $stmt->bind_param("s", $orderId);
        if (!$stmt->execute()) {
            die("Error in execute statement: " . $stmt->error);
        }

        $stmt->close();

        header("Location: billing.php");
    } else {
        echo $result['Status'];
    }
    
    // Close the database connection
    $conn->close();
} else {
    header("Location: Dashboard.php");
}

ob_end_flush(); // Flush the output buffer and send the header
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
    <!--<meta http-equiv="refresh" content="3;url=billing.php">-->
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
                        <div class="content">
                            <h1><i class="fas fa-check-circle text-success"></i> Payment Successful!</h1>
                            <p>You will be redirected to the billing page in 3 seconds.</p>
                            <p>If you are not redirected, <a href="billing.php">click here</a>.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- Page-wrapper-End -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- bootstrap-js-Link -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- aos-js-Link -->
    <script src="assets/js/aos.js"></script>
    <!-- main-js-Link -->
    <script src="assets/js/main.js"></script>
</body>

</html>