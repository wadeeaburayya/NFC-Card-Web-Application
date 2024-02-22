<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit();
}
require_once 'config.php';
$userId = $_SESSION['member_id'];
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$name = $fullName['namesurname'];
$memberid = $fullName['member_id'];
$email = $fullName['member_email'];
$address = $fullName['address'];
$phone = $fullName['phone_number'];
$pp = $fullName['pp'];
if (isset($_SESSION['basket'])) {
    $selectedPackageId = $_SESSION['basket'];
    $packageResult = $conn->query("SELECT * FROM package WHERE packageid = '$selectedPackageId'");
    $package = $packageResult->fetch_assoc();
    if ($selectedPackageId == 1) {
        $cardQuantity = 1;
    } else {
        $cardQuantity = 5;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_quantity'])) {
        $cardQuantity = intval($_POST['card_quantity']);
        $cardQuantity = max(1, $cardQuantity);
    }
    $totalPrice = $package['pprice'] * $cardQuantity;
} else {
    header("Location: packages.php");
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
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-auto mb-3 mb-md-0 me-auto">
                            <div class="w-auto sw-md-30">
                                <a href="#" class="muted-link pb-1 d-inline-block breadcrumb-back">
                                    <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                                    <span class="text-small align-middle">Basket</span>
                                </a>
                                <h1 class="mb-0 pb-0 display-4" id="title">Checkout</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Display the selected package -->
                    <div class="col-12 col-lg-9 order-1 order-lg-0">
                        <h2 class="small-title">Items</h2>
                        <div class="mb-5">
                            <div class="card mb-2">
                                <div class="row g-0 sh-18 sh-md-14">
                                    <div class="col position-relative h-100">
                                        <div class="card-body">
                                            <div class="row h-100 align-items-center">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <div class="h6 mb-0 clamp-line" data-line="1">
                                                        <?php echo $package['pname']; ?>
                                                    </div>
                                                </div>
                                                <!-- Form to update card quantity -->
                                                <form id="quantityForm" action="" method="post" class="col-md-3 pe-0 d-flex align-items-center">
                                                    <div class="input-group spinner sw-11" data-trigger="spinner">
                                                        <div class="input-group-text">
                                                            <button type="button" class="spin-down single px-2 d-flex justify-content-center" data-spin="down" onclick="updateQuantity(-1)">-</button>
                                                        </div>
                                                        <input type="text" class="form-control text-center px-0" value="<?php echo $cardQuantity; ?>" name="card_quantity" id="cardQuantity" data-rule="quantity">
                                                        <div class="input-group-text">
                                                            <button type="button" class="spin-up single px-2 d-flex justify-content-center" data-spin="up" onclick="updateQuantity(1)">+</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="col-md-3 d-flex justify-content-center align-items-center">
                                                    <div class="h6 mb-0">₺ <?php echo $package['pprice']; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Display the summary -->
                    <div class="col-12 col-lg-3">
                        <h2 class="small-title">Summary</h2>
                        <div class="card mb-5">
                            <div class="card-body">
                                <form action="process_payment.php" method="post">
                                    <input type="hidden" value="<?php echo $name; ?>" name="username" />
                                    <input type="hidden" value="<?php echo $phone; ?>" name="phone" />
                                    <input type="hidden" value="<?php echo $email; ?>" name="email" />
                                    <input type="hidden" value="<?php echo $address; ?>" name="address" />
                                    <div class="row g-3">
                                        <div class="col-sm-auto mb-3">
                                            <h2>Total: <span id="summaryTotalPrice">₺<?php echo number_format($totalPrice, 2); ?></span></h2>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-auto mb-3">
                                            <label class="form-label">Card Number</label>
                                            <input required name="no" class="form-control w-100 sw-sm-35" />
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-auto mb-3">
                                            <label class="form-label">Name on the Card</label>
                                            <input required name="name" class="form-control w-100 sw-sm-35" />
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-auto mb-0">
                                            <label class="form-label">Expiration Date</label>
                                            <div class="d-flex">
                                                <select required name="em" class="form-select me-2" id="expirationMonth">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                                <select required name="ey" class="form-select" id="expirationYear">
                                                    <?php
                                                    $currentYear = date("y");
                                                    for ($i = 0; $i < 10; $i++) {
                                                        $year = ($currentYear + $i) % 100;
                                                        echo "<option value=\"$year\">$year</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-auto mb-3">
                                            <label class="form-label">CVV</label>
                                            <input required name="cvv" class="form-control sw-8" />
                                        </div>
                                    </div>
                                    <input type="hidden" id="displayCardQuantity" value="<?php echo $cardQuantity; ?>" name="CardQuantity" readonly />
                                    <input type="hidden" id="displayTotalPrice" value="<?php echo number_format($totalPrice, 2); ?>" name="Amount" readonly />
                                    <input class="btn btn-icon btn-icon-end btn-primary w-100" value="Pay Now" type="submit">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <?php include 'subpages/footer.php'; ?>
        </footer>
    </div>
    <?php include 'subpages/footer-js.php'; ?>
    <script>
        // Get the input element by its id
        var expirationDateInput = document.getElementById('expirationDate');
        // Get the selected value (date) from the input
        var selectedDate = expirationDateInput.value;
        // Split the date into an array [year, month, day]
        var dateArray = selectedDate.split('-');
        // Extract the year and month
        var cardExpireYear = dateArray[0].slice(-2); // Extract the last two characters
        var cardExpireMonth = dateArray[1];
        // Add leading zero if necessary
        cardExpireMonth = cardExpireMonth.padStart(2, '0');
        // Log or use the values as needed
        console.log('Year: ' + cardExpireYear);
        console.log('Month: ' + cardExpireMonth);
        // Function to update quantity asynchronously
        function updateQuantity(delta) {
            var quantityInput = document.getElementById('cardQuantity');
            var displayQuantity = document.getElementById('displayCardQuantity');
            var newQuantity = parseInt(quantityInput.value) + delta;
            // Make sure the quantity is not negative
            if (<?php echo $selectedPackageId; ?> == 1) {
                newQuantity = Math.max(1, newQuantity);
                quantityInput.value = newQuantity;
                displayQuantity.value = newQuantity;
            } else {
                newQuantity = Math.max(5, newQuantity);
                quantityInput.value = newQuantity;
                displayQuantity.value = newQuantity;
            }
            // Update the total price on the client side
            var unitPrice = <?php echo $package['pprice']; ?>;
            var newTotalPrice = unitPrice * newQuantity;
            document.getElementById('displayTotalPrice').value = newTotalPrice.toFixed(2);
            // Update the total price in the summary section
            document.getElementById('summaryTotalPrice').innerText = '₺' + newTotalPrice.toFixed(2);
            // Update the quantity on the server side using AJAX
            $.ajax({
                type: 'POST',
                url: '/update_quantity.php',
                data: {
                    card_quantity: newQuantity,
                    package_id: <?php echo $selectedPackageId; ?>
                },
                success: function(response) {
                    // Handle success if needed
                    console.log(response);
                },
                error: function(error) {
                    // Handle error if needed
                    console.error(error);
                }
            });
        }
    </script>
</body>
</html>