<?php
session_start();
require_once 'config.php';

function generateHash($hashStr, $secret, $password)
{
    return base64_encode(hash_hmac('sha256', $hashStr . $password, $secret, true));
}
function generateUniqueOrderID()
{
    // Get current timestamp
    $timestamp = time();
    // Generate a random string (you can customize the length as needed)
    $randomString = bin2hex(random_bytes(8)); // 8 bytes will result in a 16-character string
    // Combine timestamp and random string
    $orderID = $timestamp . $randomString;
    // Trim to a maximum of 50 characters
    $orderID = substr($orderID, 0, 50);
    return $orderID;
}
// Set your merchant credentials and secret key
$merchantId = '';
$secretKey = '';
$password = '';
// Set the payment parameters
$orderId = generateUniqueOrderID();
$currency = 'TRY';
$amount = $_POST['Amount'];
$bclimit = $_POST['CardQuantity'];
$installment = 0;
$urlOk = 'success.php?orderid=' . $orderId . '&bclimit=' . $bclimit;
$urlFail = 'failure.php?orderid=' . $orderId;
$cardName = $_POST['name'];
$cardNo =  $_POST['no'];
$cardCvv =  $_POST['cvv'];
$cardExpireMonth =  $_POST['em'];
$cardExpireYear =  $_POST['ey'];
$cardType = '';
$userIp = $_SERVER['REMOTE_ADDR'];
$userName = $_POST['name'];
$userEmail = $_POST['email'];
$userPhone = $_POST['phone'];
$userAddress = $_POST['address'];
$userLang = 'tr';
$description = 'Payment description';
// Generate the hash code
$hashStr = $merchantId . $userIp . $orderId . $urlOk . $urlFail . $amount . $currency . $installment . '0';
$hash = generateHash($hashStr, $secretKey, $password);
$bmid = $_SESSION['member_id'];
$bpid = $_SESSION['basket'];
$bclimit = $_POST['CardQuantity'];

// Calculate bblimit or bclimit / 5 based on bpid
$bblimit = ($bpid == 1) ? 0 : $bclimit / 5;
$status = 0; // Set the status value
$btotal = $amount; // Assuming the amount is the same as btotal in your case

// Insert order details into the billing table
$stmt = $conn->prepare("INSERT INTO billing (order_id, bpid, bmid, bclimit, bblimit, btotal, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, now())");
if (!$stmt) {
    die("Error in prepare statement: " . $conn->error);
}

$stmt->bind_param("siiiiii", $orderId, $bpid, $bmid, $bclimit, $bblimit, $btotal, $status);
if (!$stmt->execute()) {
    die("Error in execute statement: " . $stmt->error);
}
$stmt->close();


// Create the HTML form
echo '<form id="paymentForm" method="POST" action="https://www.tikokart.com/api-sanalpos/gateway/pay3d">';
echo '<input type="hidden" name="MerchantId" value="' . $merchantId . '">';
echo '<input type="hidden" name="OrderId" value="' . $orderId . '">';
echo '<input type="hidden" name="Currency" value="' . $currency . '">';
echo '<input type="hidden" name="Amount" value="' . $amount . '">';
echo '<input type="hidden" name="Installment" value="' . $installment . '">';
echo '<input type="hidden" name="UrlOk" value="' . $urlOk . '">';
echo '<input type="hidden" name="UrlFail" value="' . $urlFail . '">';
echo '<input type="hidden" name="CardName" value="' . $cardName . '">';
echo '<input type="hidden" name="CardNo" value="' . $cardNo . '">';
echo '<input type="hidden" name="CardCvv" value="' . $cardCvv . '">';
echo '<input type="hidden" name="CardExpireMonth" value="' . $cardExpireMonth . '">';
echo '<input type="hidden" name="CardExpireYear" value="' . $cardExpireYear . '">';
echo '<input type="hidden" name="CardType" value="' . $cardType . '">';
echo '<input type="hidden" name="UserIp" value="' . $userIp . '">';
echo '<input type="hidden" name="UserName" value="' . $userName . '">';
echo '<input type="hidden" name="UserEmail" value="' . $userEmail . '">';
echo '<input type="hidden" name="UserPhone" value="' . $userPhone . '">';
echo '<input type="hidden" name="UserAddress" value="' . $userAddress . '">';
echo '<input type="hidden" name="UserLang" value="' . $userLang . '">';
echo '<input type="hidden" name="Description" value="' . $description . '">';
echo '<input type="hidden" name="Hash" value="' . $hash . '">';
echo '</form>';
echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("paymentForm").submit();
        });
      </script>';
