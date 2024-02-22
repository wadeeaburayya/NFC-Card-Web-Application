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

$itemsPerPage = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : 10; // Default to 10 items per page

if ($itemsPerPage === 'all') {

  $itemsPerPage = countTotalRows($conn, $memberid); // Set to the total number of rows

} else {

  $itemsPerPage = intval($itemsPerPage);

}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Default to the first page

$userData = getUsersData($conn, $memberid, $itemsPerPage, $page);

$totalPages = 0; // Initialize total pages

// Calculate the total number of pages

$totalRows = countTotalRows($conn, $memberid);

if ($itemsPerPage > 0) {

  $totalPages = ceil($totalRows / $itemsPerPage);

}

function getUsersData($conn, $memberid, $itemsPerPage, $page)

{

  $userData = array();

  $offset = ($page - 1) * $itemsPerPage;

  $query = "SELECT * FROM users WHERE member_id = '$memberid' ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset";

  $result = $conn->query($query);

  if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

      $userData[] = $row;

    }

  }

  return $userData;

}

function countTotalRows($conn, $memberid)

{

  $query = "SELECT COUNT(*) AS total FROM users WHERE member_id = '$memberid' AND statu = 0";

  $result = $conn->query($query);

  $row = $result->fetch_assoc();

  return $row['total'];

}

function getCompanies($conn, $memberid)

{

  $companies = array();

  $query = "SELECT company_id, cname FROM company WHERE statu = 0 AND memberid = '$memberid'";

  $result = $conn->query($query);

  if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

      $companies[] = $row;

    }

  }

  return $companies;

}

$cardLimit = $conn->query("SELECT SUM(bclimit) AS totalLimit FROM billing WHERE bmid = '$memberid'");

$rowCardLimit = $cardLimit->fetch_assoc();

$left = $rowCardLimit['totalLimit'];



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

                  <span class="text-small align-middle">Home</span>

                </a>

                <h1 class="mb-0 pb-0 display-4" id="title">iCard List</h1>

              </div>

            </div>

            <!-- Title End -->

            <!-- Top Buttons Start -->

            <div class="col-3 d-flex align-items-end justify-content-end">

              <!-- Check Button End -->

            </div>

            <!-- Top Buttons End -->

            <div class="col-12 col-md-5 d-flex align-items-end justify-content-end">

              <button id="showCreateCardModal" type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto" data-toggle="modal" data-target="#createCardModal">

                <i data-acorn-icon="plus"></i>

                <span>Create</span>

              </button>

            </div>

          </div>

        </div>

        <div class="row mb-2">

          <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">

            <div class="d-inline-block float-md-start me-1 mb-1 search-input-container w-100 shadow bg-foreground">

              <input id="searchInput" class="form-control" placeholder="Search" />

              <span class="search-magnifier-icon">

                <i data-acorn-icon="search"></i>

              </span>

              <span class="search-delete-icon d-none">

                <i data-acorn-icon="close"></i>

              </span>

            </div>

          </div>

          <div class="col-sm-12 col-md-7 col-lg-9 col-xxl-10 text-end mb-1">

          <span class="alert alert-info">Maximum Cards: <?php echo $left; ?>

            </span>

            <div class="d-inline-block">

              <div class="dropdown-as-select d-inline-block" data-childSelector="span">

                <button class="btn p-0 shadow" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-offset="0,3">

                  <span class="btn btn-foreground-alternate dropdown-toggle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-delay="0" title="Item Count">

                    <?php

                    $itemsPerPage = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : 10;

                    echo $itemsPerPage . " Items";

                    ?>

                  </span>

                </button>

                <div class="dropdown-menu shadow dropdown-menu-end">

                  <a class="dropdown-item" href="?itemsPerPage=5">5 Items</a>

                  <a class="dropdown-item" href="?itemsPerPage=10">10 Items</a>

                  <a class="dropdown-item" href="?itemsPerPage=20">20 Items</a>

                  <a class="dropdown-item" href="?itemsPerPage=all">Total</a>

                </div>

              </div>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="col-12 mb-5">

            <div class="card mb-2 bg-transparent no-shadow d-none d-lg-block">

              <div class="row g-0 sh-3">

                <div class="col">

                  <div class="card-body pt-0 pb-0 h-100">

                    <div class="row g-0 h-100 align-content-center">

                      <div class="col-lg-1 d-flex align-items-center mb-2 mb-lg-0 text-muted text-small">ID</div>

                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">ICARDS</div>

                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">TITLE</div>

                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">PHONE</div>

                      <div class="col-lg-2 d-flex align-items-center text-muted text-small">STATUS</div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

            <div id="checkboxTable">

              <?php if (empty($userData)) {

                echo "No cards available";

              ?>

                <?php

              } else {

                foreach ($userData as $user) : ?>

                  <div class="card mb-2" id="customerCard">

                    <div class="card-body pt-0 pb-0 sh-30 sh-lg-8">

                      <div class="row g-0 h-100 align-content-center">

                        <div class="col-11 col-lg-1 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-1 order-lg-1 h-lg-100 position-relative">

                          <div class="text-muted text-small d-lg-none">Id</div>

                          <?php echo $user['cardid']; ?>

                        </div>

                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-3 order-lg-2">

                          <div class="text-muted text-small d-lg-none">Name</div>

                          <div class="text-alternate"><?php echo $user['name']; ?></div>

                        </div>

                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-4 order-lg-4">

                          <div class="text-muted text-small d-lg-none">Title</div>

                          <div class="text-alternate">

                            <span>

                              <span class="text-alternate"><?php echo $user['title']; ?></span>

                            </span>

                          </div>

                        </div>

                        <div class="col-6 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-5 order-lg-4">

                          <div class="text-muted text-small d-lg-none">Phone</div>

                          <div class="text-alternate">

                            <span class="text-truncate h-100 d-flex align-items-center"><i class="text-primary me-2" data-acorn-icon="phone" data-acorn-size="17"></i>

                              <?php echo $user['phonenumber']; ?>

                            </span>

                          </div>

                        </div>

                        <div class="col-12 col-lg-2 d-flex flex-column justify-content-center mb-2 mb-lg-0 order-last order-lg-5">

                          <div class="text-muted text-small d-lg-none mb-1">Status</div>

                          <div>

                            <?php if ($user['statu'] == 0) : ?>

                              <span class="badge bg-outline-success me-1">Active</span>

                            <?php else : ?>

                              <span class="badge bg-outline-danger me-1">Inactive</span>

                            <?php endif; ?>

                          </div>

                        </div>

                        <div class="col-12 col-lg-2 d-flex flex-column justify-content-center align-items-lg-end mb-2 mb-lg-0 order-2 text-end order-lg-last">

                          <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-offset="0,3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options </button>

                          <div class="dropdown-menu dropdown-menu-end">

                            <a target="_blank" href="?cardid=<?php echo $user['cardid']; ?>" class="dropdown-item" id="editChecked" type="button">Preview</a>

                            <a href="Customers.Detail.php?cardid=<?php echo $user['cardid']; ?>" class="dropdown-item" id="editChecked" type="button">Edit</a>

                            <?php if ($user['statu'] == 0) { ?>

                              <button class="dropdown-item deactivate-card" data-cardid="<?php echo $user['cardid']; ?>" type="button">Deactivate</button>

                            <?php } elseif ($user['statu'] == 1) { ?>

                              <button class="dropdown-item activate-card" data-cardid="<?php echo $user['cardid']; ?>" type="button">Activate</button>

                            <?php } ?>

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

        <?php if ($totalPages > 1) : ?>

          <!-- Pagination Start -->

          <div class="d-flex justify-content-center">

            <nav>

              <ul class="pagination">

                <?php

                $totalRows = countTotalRows($conn, $memberid);

                $totalPages = ceil($totalRows / $itemsPerPage);

                $prevPage = intval($page) - 1;

                echo '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">

        <a class="page-link shadow" href="?itemsPerPage=' . $itemsPerPage . '&page=' . $prevPage . '" tabindex="-1" aria-disabled="true">

            <i data-acorn-icon="chevron-left"></i>

        </a>

    </li>';

                for ($i = 1; $i <= $totalPages; $i++) {

                  echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">

            <a class="page-link shadow" href="?itemsPerPage=' . $itemsPerPage . '&page=' . $i . '">' . $i . '</a>

        </li>';

                }

                $nextPage = intval($page) + 1;

                echo '<li class="page-item ' . ($page == $totalPages ? 'disabled' : '') . '">

        <a class="page-link shadow" href="?itemsPerPage=' . $itemsPerPage . '&page=' . $nextPage . '">

            <i data-acorn-icon="chevron-right"></i>

        </a>

    </li>';

                ?>

              </ul>

            </nav>

          </div>

          <!-- Pagination End -->

        <?php endif; ?>

      </div>

    </main>

    <div class="modal fade" id="activateCardModal" tabindex="-1" role="dialog" aria-labelledby="activateCardModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="activateCardModalLabel">Activate Card</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeActivateCardModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            Are you sure you want to activate this card?

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelActivateCard">Cancel</button>

            <button type="button" class="btn btn-success" id="confirmActivate">Activate</button>

          </div>

        </div>

      </div>

    </div>

    <div class="modal fade" id="deactivateCardModal" tabindex="-1" role="dialog" aria-labelledby="deactivateCardModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="deactivateCardModalLabel">Delete Card</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeDeactivateCardModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            Are you sure you want to delete this card?

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelDeactivateCard">Cancel</button>

            <button type="button" class="btn btn-danger" id="confirmDeactivate">Deactivate</button>

          </div>

        </div>

      </div>

    </div>

    <!-- Create Card Modal -->

    <div class="modal fade" id="createCardModal" tabindex="-1" role="dialog" aria-labelledby="createCardModalLabel" aria-hidden="true">

      <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="createCardModalLabel">Create Card</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeCreateCardModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            <form id="create-card-form" action="createcard.php" method="POST" enctype="multipart/form-data">

              <input type="hidden" name="member_id" value="<?php echo $memberid; ?>">

              <div class="row mb-3">

                <div class="col-md-6">

                  <label class="form-label">Full Name</label>

                  <input name="full_name" type="text" class="form-control" placeholder="John Doe" />

                  <div class="error-message" id="full-name-error"></div>

                </div>

                <div class="col-md-6">

                  <label class="form-label">Email</label>

                  <input name="email" type="email" class="form-control" placeholder="email@domain.com" />

                  <div class="error-message" id="email-error"></div>

                </div>

              </div>

              <div class="row mb-3">

                <div class="col-md-6">

                  <label class="form-label">Title</label>

                  <input name="title" type="text" class="form-control" />

                </div>

                <div class="col-md-6">

                  <label class="form-label">Phone Number</label>

                  <input name="phonenumber" type="text" class="form-control" placeholder="+90xxxxxxxxx" />

                  <div class="error-message" id="phone-error"></div>

                </div>

              </div>

              <div class="row mb-3 selectRow">

                <div class="col-md-6">

                  <label class="form-label" for="companySelect">Select Company:</label>

                  <select class="form-select" id="companySelect" name="company_id">

                    <!-- Options will be populated dynamically using PHP -->

                  </select>

                </div>

                <div class="col-md-6">

                  <label class="form-label" for="addressSelect">Select Address:</label>

                  <select id="addressSelect" class="form-select" name="address">

                    <!-- Options will be populated dynamically using jQuery -->

                  </select>

                </div>

              </div>

              <div class="row mb-3">

                <div class="col-md-6">

                  <label class="form-label">Slug</label>

                  <input name="slug" type="text" class="form-control" id="slugInput" />

                  <div class="error-message" id="slug-error"></div>

                </div>

                <div class="col-md-6">

                  <label class="form-label">LinkedIn Account</label>

                  <input name="linkedinacc" type="text" class="form-control" />

                </div>

              </div>

              <div class="row mb-3">

                <div class="col-md-6">

                  <label class="form-label">Instagram</label>

                  <input name="instagram" type="text" class="form-control socialInput" />

                </div>

                <div class="col-md-6">

                  <label class="form-label">X</label>

                  <input name="x" type="text" class="form-control socialInput" />

                </div>

              </div>

              <div class="row mb-3">

                <div class="col-md-6">

                  <label class="form-label">Facebook</label>

                  <input name="facebook" type="text" class="form-control socialInput" />

                </div>

                <div class="col-md-6">

                  <label class="form-label">Website</label>

                  <input name="website" type="text" class="form-control" />

                </div>

              </div>

              <div class="modal-footer">

                <label for="FuTaskDoc" class="btn btn-info">Choose Profile Picture <i class="fa-solid fa-cloud-arrow-up"></i></label>

                <input type="file" name="fileToUpload" id="FuTaskDoc" hidden="hidden">

                <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelCreateCard">Cancel</button>

                <button type="button" class="btn btn-primary" id="saveCard">Save</button>

              </div>

            </form>

            <div id="error-message"></div> <!-- Add this element for error messages -->

          </div>

        </div>

      </div>

    </div>

    <div id="successMessage" class="success-message">

      <span id="successMessageText" style="padding-right:20px;">Success message goes here</span>

      <button id="closeSuccessMessage" class="close-button">X</button>

    </div>

    <footer>

      <?php include 'subpages/footer.php'; ?>

    </footer>

  </div>

  <?php include 'subpages/footer-js.php'; ?>

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

    });

    $(document).ready(function() {

      $('.activate-card').click(function() {

        var cardId = $(this).data('cardid');

        $('#activateCardModal').modal('show');

        $('#activateCardModal').data('cardid', cardId);

      });

      $('#confirmActivate').click(function() {

        var cardId = $('#activateCardModal').data('cardid');

        $.ajax({

          type: 'POST',

          url: '/activatecard.php',

          data: {

            cardId: cardId

          },

          success: function(response) {

            if (response.trim() === "Card activated successfully") {

              $('#activateCardModal').modal('hide');

              showSuccessMessage("Card activated successfully");

              setTimeout(function() {

                location.reload();

              }, 1000);

            } else {

              alert("Error: " + response);

            }

          },

          error: function() {

            alert("An error occurred while activating the card.");

          }

        });

      });

      $('.deactivate-card').click(function() {

        var cardId = $(this).data('cardid');

        $('#deactivateCardModal').modal('show');

        $('#deactivateCardModal').data('cardid', cardId);

      });

      $('#confirmDeactivate').click(function() {

        var cardId = $('#deactivateCardModal').data('cardid');

        $.ajax({

          type: 'POST',

          url: '/deactivatecard.php',

          data: {

            cardId: cardId

          },

          success: function(response) {

            if (response.trim() === "Card deactivated successfully") {

              $('#deactivateCardModal').modal('hide');

              showSuccessMessage("Card deactivated successfully");

              setTimeout(function() {

                location.reload();

              }, 1000);

            } else {

              alert("Error: " + response);

            }

          },

          error: function() {

            alert("An error occurred while deactivating the card.");

          }

        });

      });



      function validateForm() {

        var isValid = true;

        var fullName = $('input[name="full_name"]').val();

        var phonenumber = $('input[name="phonenumber"]').val();

        var email = $('input[name="email"]').val();

        var slug = $('input[name="slug"]').val();

        if (fullName.trim() === "") {

          $('#full-name-error').text('Full Name is required').css('color', 'red');

          isValid = false;

        } else {

          $('#full-name-error').text('');

        }

        if (phonenumber.trim() === "") {

          $('#phone-error').text('Phone Number is required').css('color', 'red');

          isValid = false;

        } else if (!isValidPhoneNumber(phonenumber)) {

          $('#phone-error').text('Invalid phone number').css('color', 'red');

          isValid = false;

        } else {

          $('#phone-error').text('');

        }

        if (email.trim() === "") {

          $('#email-error').text('E-mail is required').css('color', 'red');

          isValid = false;

        } else if (!isValidEmail(email)) {

          $('#email-error').text('Invalid email address').css('color', 'red');

          isValid = false;

        } else {

          $('#email-error').text('');

        }

        if (slug.trim() === "") {

          $('#slug-error').text('Slug is required').css('color', 'red');

          isValid = false;

        } else {

          $('#slug-error').text('');

        }

        return isValid;

      }

      // Function to validate an email address using a regular expression

      function isValidEmail(email) {

        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        return emailPattern.test(email);

      }

      // Function to validate a phone number, allowing +, spaces, numbers, and parentheses

      function isValidPhoneNumber(phoneNumber) {

        var phonePattern = /^[0-9\s\+\(\)]+$/;

        return phonePattern.test(phoneNumber);

      }

      $('#showCreateCardModal').click(function() {

        $('#createCardModal').modal('show');

      });

      $('#saveCard').click(function() {

        if (validateForm()) {

          var formData = new FormData($('#create-card-form')[0]);

          $.ajax({

            type: 'POST',

            url: '/createcard.php',

            data: formData,

            processData: false, // Don't process the data

            contentType: false, // Don't set content type (it will be set automatically)

            success: function(response) {

              if (response.trim() === "Card created successfully") {

                showSuccessMessage("Card created successfully");

                $('#createCardModal').modal('hide');

                $("#create-card-form")[0].reset();

                setTimeout(function() {

                  location.reload();

                }, 1000);

              } else {

                $('#error-message').text(response).addClass('alert alert-danger'); // Update the error message

              }

            },

            error: function() {

              $('#error-message').text("An error occurred while creating the card.").addClass('alert alert-danger');; // Update the error message

            }

          });

        }

      });

      $('#searchInput').on('input', function() {

        var searchTerm = $(this).val().toLowerCase();

        $('#checkboxTable .card').each(function() {

          var cardText = $(this).text().toLowerCase();

          if (cardText.includes(searchTerm)) {

            $(this).show();

          } else {

            $(this).hide();

          }

        });

      });

      // Fetch companies and populate the company select

      $.ajax({

        type: "GET",

        url: "/getCompaniesAndAddresses.php",

        dataType: "json",

        success: function(data) {

          if (data.companies.length > 0) {

            var options = "<option value=''>Select a company</option>";

            for (var i = 0; i < data.companies.length; i++) {

              options += "<option value='" + data.companies[i].company_id + "'>" + data.companies[i].cname + "</option>";

            }

            $("#companySelect").html(options);

            $(".selectRow").show();

            // Handle company select change

            $("#companySelect").change(function() {

              var companyId = $(this).val();

              if (companyId === "") {

                // Clear options and hide address select when "Select Company" is chosen

                $("#addressSelect").html("<option value=''>Select an address</option>");

              } else {

                // Fetch addresses for the selected company

                $.ajax({

                  type: "GET",

                  url: "/getCompaniesAndAddresses.php?companyId=" + companyId,

                  dataType: "json",

                  success: function(data) {

                    var options = "<option value=''>Select an address</option>";

                    for (var i = 0; i < data.addresses.length; i++) {

                      options += "<option value='" + data.addresses[i].address_id + "'>" + data.addresses[i].address + "</option>";

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

    document.getElementById('closeActivateCardModal').addEventListener('click', function() {

      $('#activateCardModal').modal('hide');

    });

    document.getElementById('cancelActivateCard').addEventListener('click', function() {

      $('#activateCardModal').modal('hide');

    });

    document.getElementById('closeDeactivateCardModal').addEventListener('click', function() {

      $('#deactivateCardModal').modal('hide');

    });

    document.getElementById('cancelDeactivateCard').addEventListener('click', function() {

      $('#deactivateCardModal').modal('hide');

    });

    document.getElementById('closeCreateCardModal').addEventListener('click', function() {

      $('#createCardModal').modal('hide');

    });

    document.getElementById('cancelCreateCard').addEventListener('click', function() {

      $('#createCardModal').modal('hide');

    });

    // Function to show the success message

    function showSuccessMessage(message) {

      var successMessage = document.getElementById('successMessage');

      var successMessageText = document.getElementById('successMessageText');

      // Set the message text

      successMessageText.textContent = message;

      // Display the success message

      successMessage.style.display = 'block';

      // Hide the success message after 5 seconds

      setTimeout(function() {

        successMessage.style.animation = '';

        successMessage.style.display = 'none';

      }, 5000);

    }

    // Function to close the success message immediately

    document.getElementById('closeSuccessMessage').addEventListener('click', function() {

      var successMessage = document.getElementById('successMessage');

      successMessage.style.animation = '';

      successMessage.style.display = 'none';

    });

  </script>

</body>



</html>