<?php
session_start();
if (!isset($_SESSION['member_id'])) {
  header("Location: sign-in.php");
}
$userId = $_SESSION['member_id'];
require_once 'config.php';
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$memberid = $fullName['member_id'];
// Function to fetch user data from the database
function getUsersData($conn, $memberid)
{
  $userData = array();
  $query = "SELECT * FROM users WHERE member_id = '$memberid' ORDER BY id DESC";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $userData[] = $row;
    }
  }
  return $userData;
}
$userData = getUsersData($conn, $memberid);
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
            <div class="col-12 col-md-7">
              <a class="muted-link pb-2 d-inline-block hidden" href="#">
                <span class="align-middle lh-1 text-small">&nbsp;</span>
              </a>
              <h1 class="mb-0 pb-0 display-4" id="title">Welcome, <?php echo $fullName['namesurname']; ?></h1>
            </div>
          </div>
        </div>
        <div class="row">
          <?php if (empty($userData)) {
          } else { ?>
            <div class="col-xl-6 mb-5">
              <h2 class="small-title">Cards</h2>
              <div class="mb-n2 scroll-out">
                <div class="scroll-by-count" data-count="6">
                  <?php foreach ($userData as $user) : ?>
                    <div class="card mb-2 sh-15 sh-md-6">
                      <div class="card-body pt-0 pb-0 h-100">
                        <div class="row g-0 h-100 align-content-center">
                          <div class="col-10 col-md-4 d-flex align-items-center mb-3 mb-md-0 h-md-100">
                            <a href="Customers.Detail.php?cardid=<?php echo $user['cardid']; ?>" class="body-link stretched-link"><?php echo $user['name']; ?></a>
                          </div>
                          <div class="col-2 col-md-3 d-flex align-items-center textz-muted mb-1 mb-md-0 justify-content-end justify-content-md-start">
                            <?php if ($user['statu'] == 0) : ?>
                              <span class="badge bg-outline-primary me-1">Active</span>
                            <?php else : ?>
                              <span class="badge bg-outline-danger me-1">Inactive</span>
                            <?php endif; ?>
                          </div>
                          <div class="col-12 col-md-2 d-flex align-items-center mb-1 mb-md-0 text-alternate">
                          </div>
                          <div class="col-12 col-md-3 d-flex align-items-center justify-content-md-end mb-1 mb-md-0 text-alternate"><?php echo $user['title']; ?></div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach;
                  ?>
                </div>
              </div>
            </div>
          <?php } ?>
          <!-- Recent Customers.Detail.html End -->
          <!-- Performance Start -->
          <div class="col-xl-6 mb-5">
            <div class="d-flex">
              <div class="dropdown-as-select me-3" data-setActive="false" data-childSelector="span">
                <a class="pe-0 pt-0 align-top lh-1 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                  <span class="small-title"></span>
                </a>
                <div class="dropdown-menu font-standard">
                  <div class="nav flex-column" role="tablist">
                    <a class="active dropdown-item text-medium" href="#" aria-selected="true" role="tab">Today's</a>
                    <a class="dropdown-item text-medium" href="#" aria-selected="false" role="tab">Weekly</a>
                    <a class="dropdown-item text-medium" href="#" aria-selected="false" role="tab">Monthly</a>
                    <a class="dropdown-item text-medium" href="#" aria-selected="false" role="tab">Yearly</a>
                  </div>
                </div>
              </div>
              <h2 class="small-title">Performance</h2>
            </div>
            <div class="card sh-45 h-xl-100-card">
              <div class="card-body h-100">
                <div class="h-100">
                  <canvas id="horizontalTooltipChart"></canvas>
                  <div class="custom-tooltip position-absolute bg-foreground rounded-md border border-separator pe-none p-3 d-flex z-index-1 align-items-center opacity-0 basic-transform-transition">
                    <div class="icon-container border d-flex align-middle align-items-center justify-content-center align-self-center rounded-xl sh-5 sw-5 rounded-xl me-3">
                      <span class="icon"></span>
                    </div>
                    <div>
                      <span class="text d-flex align-middle text-alternate align-items-center text-small">Bread</span>
                      <span class="value d-flex align-middle text-body align-items-center cta-4">300</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Performance End -->
        </div>
        <div class="row">
          <div class="col-12 col-xxl">
            <div class="row">
              <!-- Tips Start -->
              <div class="col-12 col-xxl-auto mb-5">
                <div class="card h-100-card sw-xxl-40">
                  <div class="card-body row g-0">
                    <div class="col-12 d-flex flex-column justify-content-between align-items-start">
                      <div>
                        <div class="cta-3">Have any question?</div>
                        <div class="mb-3 cta-3 text-primary">Add new products!</div>
                        <div class="text-muted mb-4">
                          If you have any question about your product, service, payment or company
                          <br />
                          Email:info@icard.cool
                        </div>
                      </div>
                      <a target="_blank" href="https://wa.me/+905488300515" class="btn btn-icon btn-icon-start btn-outline-primary stretched-link mb-3">
                        <i data-acorn-icon="phone"></i>
                        <span>Whatsapp</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Tips End -->
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
  <?php include 'subpages/footer-js.php'; ?>
</body>
</html>