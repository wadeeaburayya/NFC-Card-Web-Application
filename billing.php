<?php
session_start();
if (!isset($_SESSION['member_id'])) {
  header("Location: sign-in.php");
}
require_once 'config.php';
$userId = $_SESSION['member_id'];
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$name = $fullName['namesurname'];
$memberid = $fullName['member_id'];
$email = $fullName['member_email'];
$pp = $fullName['pp'];
function getPaymentsData($conn, $userId)
{
  $paymentData = array();
  $query = "SELECT b.*, p.*
              FROM billing b
              INNER JOIN package p ON b.bpid = p.packageid
              WHERE b.bmid = '$userId'
              ORDER BY b.date DESC";

  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $paymentData[] = $row;
    }
  }

  return $paymentData;
}

$paymentData = getPaymentsData($conn, $userId);

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
          <div class="row">
            <!-- Title Start -->
            <div class="col-auto mb-3 mb-md-0 me-auto">
              <div class="w-auto sw-md-30">
                <a href="#" class="muted-link pb-1 d-inline-block breadcrumb-back">
                  <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                  <span class="text-small align-middle">Storefront</span>
                </a>
                <h1 class="mb-0 pb-0 display-4" id="title">Billing</h1>
              </div>
            </div>
            <!-- Title End -->
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-5">
            <div class="card mb-2 bg-transparent no-shadow d-none d-lg-block">
              <div class="row g-0 sh-3">
                <div class="col">
                  <div class="card-body pt-0 pb-0 h-100">
                    <div class="row g-0 h-100 align-content-center">
                      <div class="col-lg-3 d-flex align-items-center text-muted text-small">PACKAGE</div>
                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">CARDS</div>
                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">TOTAL</div>
                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">STATUS</div>
                      <div class="col-lg-3 d-flex align-items-center text-muted text-small">DATE</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="checkboxTable">
              <?php if (empty($paymentData)) {
                echo "No Payments available";
              ?>
                <?php
              } else {
                foreach ($paymentData as $payment) : // Example date from your database
                  $rawDateFromDatabase = $payment['date'];

                  // Convert the raw date to a DateTime object
                  $dateTime = new DateTime($rawDateFromDatabase);

                  // Format the date as you want (change 'F d, Y H:i:s' as needed)
                  $formattedDate = $dateTime->format('H:i, d F Y'); ?>
                  <div class="card mb-2" id="customerCard">
                    <div class="card-body pt-0 pb-0 sh-30 sh-lg-8">
                      <div class="row g-0 h-100 align-content-center">
                        <div class="col-6 col-lg-3 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-1 order-lg-1">
                          <div class="text-muted text-small d-lg-none">Package</div>
                          <div class="text-alternate"><?php echo $payment['pname']; ?></div>
                        </div>
                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-2 order-lg-2">
                          <div class="text-muted text-small d-lg-none">Cards</div>
                          <div class="text-alternate">
                            <span class="text-alternate"><?php echo $payment['bclimit']; ?></span>
                          </div>
                        </div>
                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-3 order-lg-3">
                          <div class="text-muted text-small d-lg-none">Total</div>
                          <div class="text-alternate">
                            <span>
                              <span class="text-alternate"><?php echo $payment['btotal']; ?> ₺</span>
                            </span>
                          </div>
                        </div>
                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-4 order-lg-4">
                          <div class="text-muted text-small d-lg-none mb-1">Status</div>
                          <div>
                            <?php if ($payment['status'] == 0) : ?>
                              <span class="badge bg-outline-danger me-1">Unpaid</span>
                            <?php else : ?>
                              <span class="badge bg-outline-success me-1">Paid</span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="col-12 col-lg-3 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-last order-lg-5">
                          <div class="text-muted text-small d-lg-none">Date</div>
                          <div class="text-alternate">
                            <?php echo $formattedDate; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              <?php endforeach;
              } ?>
            </div>
          </div>
        </div>
    </main>
    <footer>
      <?php include 'subpages/footer.php'; ?>
    </footer>


    <!-- Layout Footer End -->
    <?php include 'subpages/footer-js.php'; ?>
</body>

</html>