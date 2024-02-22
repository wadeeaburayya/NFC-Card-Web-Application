<?php
session_start();
if (!isset($_SESSION['member_id'])) {
  header("Location: sign-in.php");
}
$userId = $_SESSION['member_id'];
require_once 'config.php';
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$cardid = $_GET['cardid'];
function getUsersData($conn, $cardid)
{
  $userData = array();
  $query = "SELECT * FROM users WHERE cardid = '$cardid'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $userData[] = $row;
    }
  }
  return $userData;
}
$userData = getUsersData($conn, $cardid);
$memberid = $fullName['member_id'];
function getBrandsData($conn, $memberid)
{
  $brandsData = array();
  $query = "SELECT * FROM company WHERE memberid = '$memberid'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $brandsData[] = $row;
    }
  }
  return $brandsData;
}
$brandsData = getBrandsData($conn, $memberid);
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
                  <span class="text-small align-middle">Cards</span>
                </a>
                <h1 class="mb-0 pb-0 display-4" id="title">Card Detail</h1>
              </div>
            </div>
            <!-- Title End -->
            <!-- Top Buttons Start -->
            <div class="col-12 col-md-5 d-flex align-items-end justify-content-end">
            </div>
            <!-- Top Buttons End -->
          </div>
        </div>
        <!-- Title and Top Buttons End -->
        <div class="row gx-4 gy-5">
          <!-- Customer Start -->
          <?php foreach ($userData as $user) : ?>
            <div class="col-12 col-xl-4 col-xxl-3">
              <h2 class="small-title">Info</h2>
              <div class="card">
                <div class="card-body mb-n5">
                  <div class="d-flex align-items-center flex-column">
                    <div class="mb-3 d-flex align-items-center flex-column">
                      <div class="sw-14 sh-14 mb-3   d-inline-block bg-primary d-flex justify-content-center align-items-center rounded-xl">
                        <img src="uploads/<?php echo $user['photo']; ?>" style="border: 2px solid white; border-radius:50%;" width="105" height="105" alt="image">
                      </div>
                      <div class="h5 mb-1"><?php echo $user['name']; ?></div>
                    </div>
                  </div>
                  <div class="d-flex justify-content-center">
                    <div class="d-flex flex-row justify-content-between w-100 w-sm-50 w-xl-100 mb-5">
                      <!--<button type="button" class="btn btn-primary w-100 me-2">Edit</button>-->
                      <a target="_blank" href="?cardid=<?php echo $user['cardid']; ?>" type="button" class="btn btn-outline-primary w-100 me-2">Preview</a>
                    </div>
                  </div>
                  <div class="mb-5">
                    <?php
                    if ($user['phonenumber'] == '') {
                    } else {
                    ?>
                      <div class="row g-0 align-items-center mb-2">
                        <div class="col-auto">
                          <div class="border border-primary sw-5 sh-5 rounded-xl d-flex justify-content-center align-items-center">
                            <i data-acorn-icon="phone" class="text-primary"></i>
                          </div>
                        </div>
                        <div class="col ps-3">
                          <div class="row g-0">
                            <div class="col-auto">
                              <div class="sh-5 d-flex align-items-center">
                                <?php echo $user['phonenumber']; ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                    <?php
                    if ($user['email'] == '') {
                    } else {
                    ?>
                      <div class="row g-0 align-items-center mb-2">
                        <div class="col-auto">
                          <div class="border border-primary sw-5 sh-5 rounded-xl d-flex justify-content-center align-items-center">
                            <i data-acorn-icon="email" class="text-primary"></i>
                          </div>
                        </div>
                        <div class="col ps-3">
                          <div class="row g-0">
                            <div class="col-auto">
                              <div class="sh-5 d-flex align-items-center">
                                <?php echo $user['email']; ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                    <?php
                    if ($user['title'] == '') {
                    } else {
                    ?>
                      <div class="row g-0 align-items-center mb-2">
                        <div class="col-auto">
                          <div class="border border-primary sw-5 sh-5 rounded-xl d-flex justify-content-center align-items-center">
                            <i data-acorn-icon="boxes" class="text-primary"></i>
                          </div>
                        </div>
                        <div class="col ps-3">
                          <div class="row g-0">
                            <div class="col-auto">
                              <div class="sh-5 d-flex align-items-center">
                                <?php echo $user['title']; ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                  <?php
                  if ($user['address'] == '') {
                  } else {
                  ?>
                    <div class="mb-5">
                      <div>
                        <p class="text-small text-muted mb-2">Address</p>
                        <div class="row g-0 mb-2">
                          <!--<div class="col-auto">
                          <div class="sw-3 me-1">
                            <i data-acorn-icon="arrow-double-right" class="text-primary" data-acorn-size="17"></i>
                          </div>
                        </div>
                        <?php
                        $userCompanyID = $user['company_id'];
                        $getCompanyInfo = $conn->query("SELECT cname FROM company WHERE company_id = '$userCompanyID'");
                        $rowCompany = $getCompanyInfo->fetch_assoc();
                        $userAddressID = $user['address'];
                        $getAddressInfo = $conn->query("SELECT address FROM address WHERE address_id = '$userAddressID'");
                        $rowAddress = $getAddressInfo->fetch_assoc();
                        ?>
                        <div class="col text-alternate align-middle"><?php // echo $rowCompany['cname']; 
                                                                      ?></div>-->
                        </div>
                        <div class="row g-0 mb-2">
                          <div class="col-auto">
                            <div class="sw-3 me-1">
                              <i data-acorn-icon="pin" class="text-primary" data-acorn-size="17"></i>
                            </div>
                          </div>
                          <div class="col text-alternate"><?php
                                                          echo $rowAddress['address']; ?></div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <!-- Customer End -->
            <div class="col-12 col-xl-8 col-xxl-9">
              <div class="mb-5">
                <h2 class="small-title">Edit Information</h2>
                <div class="card">
                  <div class="card-body">
                    <form id="user-edit-form" enctype="multipart/form-data">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" type="text" class="form-control" value="<?php echo $user['name']; ?>" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" type="text" class="form-control" value="<?php echo $user['title']; ?>" />
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="text" class="form-control emailInput" value="<?php echo $user['email']; ?>" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input name="phonenumber" type="text" class="form-control phoneInput" value="<?php echo $user['phonenumber']; ?>" />
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select class="form-select" id="companySelect" name="company_id">
                              <!-- Options will be populated dynamically using PHP -->
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Address</label>
                            <select id="addressSelect" class="form-select" name="address">
                              <!-- Options will be populated dynamically using jQuery -->
                            </select>
                          </div>
                        </div>
                      </div>
                      <h2 class="small-title">Social Media</h2>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Linkedin</label>
                            <input name="linkedinacc" type="text" class="form-control socialInput" value="<?php echo $user['linkedinacc']; ?>" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <input name="facebook" type="text" class="form-control socialInput" value="<?php echo $user['facebook']; ?>" />
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <input name="instagram" type="text" class="form-control socialInput" value="<?php echo $user['instagram']; ?>" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">X</label>
                            <input name="x" type="text" class="form-control socialInput" value="<?php echo $user['x']; ?>" />
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input name="website" type="text" class="form-control" value="<?php echo $user['website']; ?>" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input name="link" type="text" id="slugInput" class="form-control" value="<?php echo $user['link']; ?>" />
                          </div>
                        </div>
                      </div>
                      <div class="d-flex">
                        <div class="mb-3">
                          <label for="FuTaskDoc" class="btn btn-info">Choose Picture</label>
                          <input type="file" id="FuTaskDoc" name="fileToUpload" hidden="hidden">
                        </div>
                        <div class="mb-3">
                          <button id="update-button" type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto">
                            <i data-acorn-icon="save"></i>
                            <span>Save</span>
                          </button>
                        </div>
                      </div>
                      <input type="hidden" name="id" value=<?php echo $user['id']; ?>>
                      <input type="hidden" name="member_id" value=<?php echo $memberid; ?>>
                      <div id="error-message"></div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
    <footer>
      <?php include 'subpages/footer.php'; ?>
    </footer>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- bootstrap-js-Link -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const slugInput = document.getElementById('slugInput');
      slugInput.addEventListener('input', function() {
        const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        this.value = sanitizedValue;
      });
      const socialInputs = document.getElementsByClassName('socialInput');
      for (const socialInput of socialInputs) {
        socialInput.addEventListener('input', function() {
          const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
          this.value = sanitizedValue;
        });
      }
      const phoneInputs = document.getElementsByClassName('phoneInput');
      for (const phoneInput of phoneInputs) {
        phoneInput.addEventListener('input', function() {
          const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9- +()]/g, '');
          this.value = sanitizedValue;
        });
      }
      const emailInputs = document.getElementsByClassName('emailInput');
      for (const emailInput of emailInputs) {
        emailInput.addEventListener('input', function() {
          const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9-@.]/g, '');
          this.value = sanitizedValue;
        });
      }
    });
    $(document).ready(function() {
      $('#update-button').click(function() {
        var formData = new FormData($('#user-edit-form')[0]);
        var fileInput = $('#user-edit-form input[name="fileToUpload"]')[0];
        formData.append('fileToUpload', fileInput.files[0]);
        $.ajax({
          type: 'POST',
          url: '/updatecard.php',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.trim() === "Card data updated successfully.") {
              window.location.reload();
            } else {
              $('#error-message').text(response).addClass('alert alert-danger');
            }
          },
          error: function() {
            $('#error-message').text(
              "An error occurred while processing the request.").addClass(
              'alert alert-danger');
          }
        });
      });
      $.ajax({
        type: "GET",
        url: "/getCompaniesAndAddresses.php",
        dataType: "json",
        success: function(data) {
          if (data.companies.length > 0) {
            var options = "<option value='0'>Select a company</option>";
            for (var i = 0; i < data.companies.length; i++) {
              options += "<option value='0" + data.companies[i].company_id + "'>" + data
                .companies[i].cname + "</option>";
            }
            $("#companySelect").html(options);
            $(".selectRow").show();
            // Handle company select change
            $("#companySelect").change(function() {
              var companyId = $(this).val();
              if (companyId === "") {
                // Clear options and hide address select when "Select Company" is chosen
                $("#addressSelect").html(
                  "<option value='0'>Select an address</option>");
              } else {
                // Fetch addresses for the selected company
                $.ajax({
                  type: "GET",
                  url: "/getCompaniesAndAddresses.php?companyId=" +
                    companyId,
                  dataType: "json",
                  success: function(data) {
                    var options =
                      "<option value='0'>Select an address</option>";
                    for (var i = 0; i < data.addresses
                      .length; i++) {
                      options += "<option value='" + data
                        .addresses[i].address_id + "'>" +
                        data.addresses[i].address +
                        "</option>";
                    }
                    $("#addressSelect").html(options);
                    // Show address select after fetching addresses
                    $(".selectRow").show();
                  }
                });
              }
            });
          } else {
            $(".selectRow").hide();
          }
        }
      });
      // Add a default option to the address select
      var defaultAddressOption = "<option value=''>Select an address</option>";
      $("#addressSelect").html(defaultAddressOption);
    });
  </script>
  <?php include 'subpages/footer-js.php'; ?>
</body>
</html>