<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['member_id'])) {
  header("Location: sign-in.php");
}
$userId = $_SESSION['member_id'];
require_once 'config.php';
$getFullname = $conn->query("SELECT * FROM members WHERE member_id = '$userId'");
$fullName = $getFullname->fetch_assoc();
$memberid = $fullName['member_id'];
$getCompanyID =  $_GET['companyid'];
function getBrandsData($conn, $memberid, $getCompanyID)
{
  $userData = array();
  $query = "SELECT * FROM company WHERE memberid = '$memberid' AND statu = 0 AND company_id = '$getCompanyID'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $company_id = $row['company_id'];
      $countQuery = "SELECT COUNT(*) AS user_count FROM users WHERE company_id = '$company_id' AND statu = 0";
      $countResult = $conn->query($countQuery);
      $userCount = $countResult->fetch_assoc();
      $row['user_count'] = $userCount['user_count'];
      $userData[] = $row;
    }
  }
  return $userData;
}
$userData = getBrandsData($conn, $memberid, $getCompanyID);
function getAddressesData($conn, $getCompanyID)
{
  $addressData = array();
  $query = "SELECT * FROM address WHERE acompany_id = '$getCompanyID'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $addressData[] = $row;
    }
  }
  return $addressData;
}
$addressData = getAddressesData($conn, $getCompanyID);
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
          <div class="row g-0">
            <div class="col-auto mb-3 mb-md-0 me-auto">
              <div class="w-auto sw-md-30">
                <a href="#" class="muted-link pb-1 d-inline-block breadcrumb-back">
                  <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                  <span class="text-small align-middle">Products</span>
                </a>
                <h1 class="mb-0 pb-0 display-4" id="title">Product Detail</h1>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-8">
            <?php foreach ($userData as $user) : ?>
              <div class="mb-5">
                <h2 class="small-title">Branding</h2>
                <div class="card">
                  <div class="card-body">
                    <form id="company-edit-form" enctype="multipart/form-data">
                      <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="cname" class="form-control" value="<?php echo $user['cname']; ?>" />
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Company Slug</label>
                        <input type="text" id="slugInput" name="cslug" class="form-control" value="<?php echo $user['cslug']; ?>" />
                      </div>
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <label class="form-label">Text Color</label>
                          <input type="color" name="textcolor" class="form-control form-control-color" id="exampleColorInput" value="<?php echo $user['textcolor']; ?>" title="Choose your color">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="exampleColorInput" class="form-label">Background
                            Color</label>
                          <input type="color" name="bgcolor" class="form-control form-control-color" id="exampleColorInput" value="<?php echo $user['bgcolor']; ?>" title="Choose your color">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="exampleColorInput" class="form-label">Button
                            Color</label>
                          <input type="color" name="buttoncolor" class="form-control form-control-color" id="exampleColorInput" value="<?php echo $user['buttoncolor']; ?>" title="Choose your color">
                        </div>
                      </div>
                      <div class="row">
                        <div class="mb-3">
                          <label for="FuTaskDoc" class="btn btn-info">Choose Logo
                            <!--<i class="fa-solid fa-cloud-arrow-up"></i>-->
                          </label>
                          <input type="file" id="FuTaskDoc" name="fileToUpload" hidden="hidden">
                        </div>
                      </div>
                      <input type="hidden" name="hidelogo" value=<?php echo $user['logo']; ?>>
                      <input type="hidden" name="company_id" value=<?php echo $user['company_id']; ?>>
                      <button id="update-button" type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto">
                        <i data-acorn-icon="save"></i>
                        <span>Save</span>
                      </button>
                    </form>
                    <div id="error-message"></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="col-xl-4">
            <div class="row g-0">
              <div class="col-12 mb-5">
                <div id="checkboxTable">
                  <div class="mb-4 mb-lg-3 bg-transparent no-shadow d-none d-lg-block">
                    <div class="d-flex justify-content-between">
                      <h2 class="small-title">Addresses</h2>
                      <button id="showCreateAddressModal" type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto" data-toggle="modal" data-target="#createAddressModal">
                        <i data-acorn-icon="plus"></i>
                        <span>Add Address</span>
                      </button>
                    </div>
                  </div>
                  <?php foreach ($addressData as $address) : ?>
                    <div class="card mb-2">
                      <div class="row g-0 h-100 sh-lg-9 position-relative">
                        <div class="col py-4 py-lg-0">
                          <div class="ps-5 pe-4 h-100">
                            <div class="row g-0 h-100 align-content-center">
                              <a class="col-11 col-lg-4 d-flex flex-column mb-lg-0 mb-3 pe-3 d-flex order-1 h-lg-100 justify-content-center">
                                <?php echo $address['aname']; ?>
                              </a>
                              <div class="col-1 col-lg-3 d-flex flex-column mb-2 mb-lg-0 align-items-end order-2 order-lg-last justify-content-lg-center">
                                <div class="btn-group ms-1 check-all-container-checkbox-click">
                                  <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto showEditAddressModal" data-address-id="<?php echo $address['address_id']; ?>" data-toggle="modal" data-target="#editAddressModal">
                                    <i data-acorn-icon="edit"></i>
                                    <span>Edit Address</span>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="createAddressModal" tabindex="-1" role="dialog" aria-labelledby="createAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createAddressModalLabel">Add Address</h5>
              <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeCreateAddressModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="create-address-form" enctype="multipart/form-data">
                <input type="hidden" name="acompany_id" value="<?php echo $getCompanyID; ?>">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="form-label">Address Name</label>
                    <input name="aname" type="text" class="form-control" placeholder="Home" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="form-label">Address Text</label>
                    <input name="address" type="text" class="form-control" placeholder="Salamis Road, Famagusta" />
                  </div>
                </div>
                <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelCreateAddress">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAddress">Save</button>
              </form>
              <div class="error-message mt-3" id="error-form"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog" aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
              <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeEditAddressModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="edit-address-form" enctype="multipart/form-data">
                <input type="hidden" name="address_id" id="edit-address-id">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="form-label">Address Name</label>
                    <input name="aname" type="text" class="form-control" value="" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label class="form-label">Address Text</label>
                    <input name="address" type="text" class="form-control" value="" />
                  </div>
                </div>
                <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelEditAddress">Cancel</button>
                <button type="button" class="btn btn-primary" id="editAddress">Save</button>
              </form>
              <div class="error-message mt-3" id="error-form"></div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <div id="successMessage" class="success-message">
      <span id="successMessageText" style="padding-right:20px;">Success message goes here</span>
      <button id="closeSuccessMessage" class="close-button">X</button>
    </div>
    <footer>
      <?php include 'subpages/footer.php';  ?>
    </footer>
  </div>
  <?php include 'subpages/footer-js.php';  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const slugInput = document.getElementById('slugInput');
      slugInput.addEventListener('input', function() {
        const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        this.value = sanitizedValue;
      });
    });
    $(document).ready(function() {
      $('#update-button').click(function() {
        var formData = new FormData($('#company-edit-form')[0]);
        var fileInput = $('#company-edit-form input[name="fileToUpload"]')[0];
        formData.append('fileToUpload', fileInput.files[0]);
        $.ajax({
          type: 'POST',
          url: '/updatecompany.php',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.trim() === "Company data updated successfully.") {
              window.location.reload();
            } else {
              $('#error-message').text(response).addClass(
                'mt-3 alert alert-danger');
            }
          },
          error: function() {
            $('#error-message').text(
              "An error occurred while processing the request.").addClass(
              'alert alert-danger');
          }
        });
      });
      $('#showCreateAddressModal').click(function() {
        $('#createAddressModal').modal('show');
      });
      $('#saveAddress').click(function() {
        var formData = new FormData($('#create-address-form')[0]);
        $.ajax({
          type: 'POST',
          url: '/addadress.php',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.trim() === "Address created successfully") {
              showSuccessMessage("Address created successfully");
              $('#createAddressModal').modal('hide');
              $("#create-address-form")[0].reset();
              setTimeout(function() {
                location.reload();
              }, 2000);
            } else {
              $('#error-form').text(response).addClass(
                'alert alert-danger');
            }
          },
          error: function() {
            $('#error-form').text(
              "An error occurred while creating the address.").addClass(
              'alert alert-danger');
          }
        });
      });
      $('.showEditAddressModal').click(function() {
        $('#editAddressModal').modal('show');
        var addressId = $(this).data('address-id');
        $('#edit-address-id').val(addressId);
        $.ajax({
          type: 'POST',
          url: '/getaddress.php',
          data: {
            address_id: addressId
          },
          dataType: 'json', // Parse the response as JSON
          success: function(response) {
            if (response.error) {
              console.error(response.error);
            } else {
              $('#edit-address-form input[name="aname"]').val(response.aname);
              $('#edit-address-form input[name="address"]').val(response.address);
            }
          },
          error: function(xhr, status, error) {
            console.log(xhr.responseText);
            console.log(status);
            console.log(error);
          }

        });
      });

      $('#editAddress').click(function() {
        var formData = new FormData($('#edit-address-form')[0]);
        $.ajax({
          type: 'POST',
          url: '/editaddress.php',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.trim() === "Address updated successfully") {
              showSuccessMessage("Address updated successfully");
              $('#editAddressModal').modal('hide');
              $("#edit-address-form")[0].reset();
              setTimeout(function() {
                location.reload();
              }, 2000);
            } else {
              $('#error-form').text(response).addClass(
                'alert alert-danger');
            }
          },
          error: function() {
            $('#error-form').text(
              "An error occurred while creating the address.").addClass(
              'alert alert-danger');
          }
        });
      });
    });
    document.getElementById('closeCreateAddressModal').addEventListener('click', function() {
      $('#createAddressModal').modal('hide');
    });
    document.getElementById('cancelCreateAddress').addEventListener('click', function() {
      $('#createAddressModal').modal('hide');
    });
    document.getElementById('closeEditAddressModal').addEventListener('click', function() {
      $('#editAddressModal').modal('hide');
    });
    document.getElementById('cancelEditAddress').addEventListener('click', function() {
      $('#editAddressModal').modal('hide');
    });

    function showSuccessMessage(message) {
      var successMessage = document.getElementById('successMessage');
      var successMessageText = document.getElementById('successMessageText');
      successMessageText.textContent = message;
      successMessage.style.display = 'block';
      setTimeout(function() {
        successMessage.style.animation = '';
        successMessage.style.display = 'none';
      }, 3000);
    }
    document.getElementById('closeSuccessMessage').addEventListener('click', function() {
      var successMessage = document.getElementById('successMessage');
      successMessage.style.animation = '';
      successMessage.style.display = 'none';
    });
  </script>
</body>

</html>