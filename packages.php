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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id'])) {
    // Get the selected package ID
    $selectedPackageId = $_POST['package_id'];

    // Add the package to the user's basket (you may want to store this information in a database or session)
    $_SESSION['basket'] = $selectedPackageId;

    // Redirect to the basket page
    header("Location: basket.php");
    exit(); // make sure to exit after header redirect
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
                <!-- Title and Top Buttons Start -->
                <div class="page-title-container">
                    <div class="row g-0">
                        <!-- Title Start -->
                        <div class="col-auto mb-3 mb-md-0 me-auto">
                            <div class="w-auto sw-md-30">
                                <a href="#" class="muted-link pb-1 d-inline-block breadcrumb-back">
                                    <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                                    <span class="text-small align-middle">Packages</span>
                                </a>
                                <h1 class="mb-0 pb-0 display-4" id="title">Type of Packages</h1>
                            </div>
                        </div>
                        <!-- Title End -->
                        <!-- Top Buttons Start -->
                        <div class="w-100 d-md-none"></div>

                        <!-- Top Buttons End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->
                <div class="row mb-n5">
                    <?php
                    // Fetch packages from the database
                    $packagesResult = $conn->query("SELECT * FROM package");
                    while ($package = $packagesResult->fetch_assoc()) {
                    ?>
                        <div class="col-xl-4">
                            <div class="mb-5">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center flex-column">
                                            <div class="mb-3 d-flex align-items-center flex-column">
                                                <div class="rounded-xl">
                                                    <img src="<?php echo $package['pimage_url']; ?>" width="105" height="105" alt="image">
                                                </div>
                                                <div class="h2 mb-1"><?php echo $package['pname']; ?></div>
                                                <h5><?php echo $package['pdescription']; ?></h5>
                                                <h6 style="font-size: 48px; color: var(--primary); margin: 25px 0; display: block; font-weight: 600;">
                                                    ₺<?php echo $package['pprice']; ?></h6>
                                                <ul style="list-style-type: none;" class="mb-4">
                                                    <?php
                                                    // Display additional package details from the database
                                                    $details = explode(",", $package['pdetails']);
                                                    foreach ($details as $detail) {
                                                        echo "<li><p>$detail</p></li>";
                                                    }
                                                    ?>
                                                </ul>
                                                <form action="packages.php" method="post">
                                                    <input type="hidden" name="package_id" value="<?php echo $package['packageid']; ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-icon btn-icon-start">
                                                        <i data-acorn-icon="basket"></i>
                                                        <span>Buy Now</span>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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