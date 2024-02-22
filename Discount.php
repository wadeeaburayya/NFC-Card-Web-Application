<?php
session_start();

if (!isset($_SESSION['member_id'])) {
  header("Location: sign-in.php");
}
$userId = $_SESSION['member_id'];
require_once 'config.php';

$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();

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
        <!-- Title and Top Buttons End -->

        <div class="row">
          <div class="col-12 col-lg order-1 order-lg-0">


            <h2 class="small-title">Payment</h2>
            <div class="card mb-5">
              <div class="card-body">
                <form>
                  <div class="row g-3">
                    <div class="col-sm-auto mb-3">
                      <label class="form-label">Card Number</label>
                      <input class="form-control w-100 sw-sm-40" />
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-sm-auto mb-3">
                      <label class="form-label">Name on the Card</label>
                      <input class="form-control w-100 sw-sm-40" />
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-auto mb-3">
                      <label class="form-label">CCV</label>
                      <input class="form-control sw-8" />
                    </div>
                    <div class="col-auto mb-0">
                      <div class="time-picker-container">
                        <label class="form-label">Expiration Date</label>
                        <input class="form-control time-picker" id="timePickerStandard" data-hours24="1,2,3,4,5,6,7,8,9,10,11,12" data-minutes="21,22,23,24,25,26,27,28,29,30" />
                      </div>
                    </div>
                  </div>
                </form>
                <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start">
                  <i data-acorn-icon="save"></i>
                  <span>Save</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- Layout Footer Start -->
        <footer>
          <?php include 'subpages/footer.php'; ?>
        </footer>
        <!-- Layout Footer End -->
        <?php include 'subpages/footer-js.php'; ?>
</body>

</html>