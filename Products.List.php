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

$itemsPerPage = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : 10; // Default to 10 items per page

if ($itemsPerPage === 'all') {

  $itemsPerPage = countTotalRows($conn, $memberid); // Set to the total number of rows

} else {

  $itemsPerPage = intval($itemsPerPage);

}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Default to the first page

$userData = getBrandsData($conn, $memberid, $itemsPerPage, $page);

$totalPages = 0; // Initialize total pages

// Calculate the total number of pages

$totalRows = countTotalRows($conn, $memberid);

if ($itemsPerPage > 0) {

  $totalPages = ceil($totalRows / $itemsPerPage);

}

function countTotalRows($conn, $memberid)

{

  $query = "SELECT COUNT(*) AS total FROM company WHERE memberid = '$memberid' AND statu = 0";

  $result = $conn->query($query);

  $row = $result->fetch_assoc();

  return $row['total'];

}

// Function to fetch user data from the database

function getBrandsData($conn, $memberid, $itemsPerPage, $page)

{

  $userData = array();

  $offset = ($page - 1) * $itemsPerPage;

  $query = "SELECT * FROM company WHERE memberid = '$memberid' ORDER BY company_id DESC LIMIT $itemsPerPage OFFSET $offset";

  $result = $conn->query($query);

  if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

      $company_id = $row['company_id'];

      // Count how many users have the same company_id

      $countQuery = "SELECT COUNT(*) AS user_count FROM users WHERE company_id = '$company_id' AND statu = 0";

      $countResult = $conn->query($countQuery);

      $userCount = $countResult->fetch_assoc();

      $row['user_count'] = $userCount['user_count'];

      $userData[] = $row;

    }

  }

  return $userData;

}

$userData = getBrandsData($conn, $memberid, $itemsPerPage, $page);

$brandLimit = $conn->query("SELECT SUM(bblimit) AS totalLimit FROM billing WHERE bmid = '$memberid'");

$rowBrandLimit = $brandLimit->fetch_assoc();

$left = $rowBrandLimit['totalLimit'];

?>

<!DOCTYPE html>

<html lang="en" data-footer="true">



<head>

  <?php

  include 'subpages/header.php';

  ?>

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

                  <span class="text-small align-middle">Home</span>

                </a>

                <h1 class="mb-0 pb-0 display-4" id="title">Compnaies List</h1>

              </div>

            </div>

            <div class="col-3 d-flex align-items-end justify-content-end">

            </div>

            <div class="col-12 col-md-5 d-flex align-items-end justify-content-end">

              <button id="showCreateCompanyModal" type="button" class="btn btn-outline-primary btn-icon btn-icon-start ms-0 ms-sm-1 w-100 w-md-auto" data-toggle="modal" data-target="#createCompanyModal">

                <i data-acorn-icon="plus"></i>

                <span>Create</span>

              </button>

            </div>

          </div>

        </div>

        <div class="row mb-2">

          <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">

            <div class="d-inline-block float-mdassets/arch-input-container w-100 shadow bg-foreground">

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

          <span class="alert alert-info">Maximum Brands: <?php echo $left; ?>

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

          <div class="row g-0">

            <div class="col-12 mb-5">

              <div id="checkboxTable">

                <div class="mb-4 mb-lg-3 bg-transparent no-shadow d-none d-lg-block">

                  <div class="row g-0">

                    <div class="col-auto sw-11 d-none d-lg-flex"></div>

                    <div class="col">

                      <div class="ps-5 pe-4 h-100">

                        <div class="row g-0 h-100 align-content-center">

                          <div class="col-lg-4 d-flex flex-column mb-lg-0 pe-3 d-flex">

                            <div class="text-muted text-small cursor-pointer sort">

                              COMPANY

                            </div>

                          </div>

                          <div class="col-lg-2 d-flex flex-column pe-1 justify-content-center">

                            <div class="text-muted text-small cursor-pointer sort" data-sort="email">

                              MEMBERS

                            </div>

                          </div>

                          <!--<div class="col-lg-3 d-flex flex-column pe-1 justify-content-center">

                            <div class="text-muted text-small cursor-pointer sort" data-sort="phone">

                              PLAN

                            </div>

                          </div>-->

                          <div class="col-lg-2 d-flex flex-column pe-1 justify-content-center">

                            <div class="text-muted text-small cursor-pointer sort" data-sort="group">

                              STATUS

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

                <?php foreach ($userData as $user) : ?>

                  <div class="card mb-2">

                    <div class="row g-0 h-100 sh-lg-9 position-relative">

                      <a class="col-auto position-relative">

                        <img src="uploads/<?php echo $user['logo']; ?>" alt="product" class="card-img card-img-horizontal sw-9" />

                      </a>

                      <div class="col py-4 py-lg-0">

                        <div class="ps-5 pe-4 h-100">

                          <div class="row g-0 h-100 align-content-center">

                            <a class="col-11 col-lg-4 d-flex flex-column mb-lg-0 mb-3 pe-3 d-flex order-1 h-lg-100 justify-content-center">

                              <?php echo $user['cname']; ?>

                            </a>

                            <div class="col-12 col-lg-2 d-flex flex-column pe-1 mb-2 mb-lg-0 justify-content-center order-3">

                              <div class="lh-1 text-alternate"><?php echo $user['user_count']; ?>

                              </div>

                            </div>

                            <!--<div class="col-12 col-lg-3 d-flex flex-column pe-1 mb-2 mb-lg-0 justify-content-center order-4">

                              <div class="lh-1 text-alternate">Gold Plan</div>

                            </div>-->

                            <div class="col-12 col-lg-2 d-flex flex-column pe-1 mb-2 mb-lg-0 align-items-start justify-content-center order-5">

                              <?php if ($user['statu'] == 0) : ?>

                                <span class="badge bg-outline-success me-1">Active</span>

                              <?php else : ?>

                                <span class="badge bg-outline-danger me-1">Inactive</span>

                              <?php endif; ?>

                            </div>

                            <div class="col-1 d-flex flex-column mb-2 mb-lg-0 align-items-end order-2 order-lg-last justify-content-lg-center">

                              <div class="btn-group ms-1 check-all-container-checkbox-click">

                                <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-offset="0,3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options </button>

                                <div class="dropdown-menu dropdown-menu-end">

                                  <a href="Products.Detail.php?companyid=<?php echo $user['company_id']; ?>" class="dropdown-item" id="editChecked" type="button">Edit</a>

                                  <?php if ($user['statu'] == 0) : ?>

                                    <button class="dropdown-item delete-company" data-cusers_id="<?php echo $user['user_count']; ?>" data-company_id="<?php echo $user['company_id']; ?>" type="button">Deactivate</button>

                                  <?php else : ?>

                                    <button class="dropdown-item activate-company" data-company_id="<?php echo $user['company_id']; ?>" type="button">Activate</button>

                                  <?php endif; ?>

                                </div>

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

            <?php if ($totalPages > 1) : ?>

              <!-- Pagination Start -->

              <div class="d-flex justify-content-center">

                <nav>

                  <ul class="pagination">

                    <?php

                    // Calculate the total number of pages

                    $totalRows = countTotalRows($conn, $memberid);

                    $totalPages = ceil($totalRows / $itemsPerPage);

                    // Previous page

                    $prevPage = intval($page) - 1;

                    echo '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">

                            <a class="page-link shadow" href="?itemsPerPage=' . $itemsPerPage . '&page=' . $prevPage . '" tabindex="-1" aria-disabled="true">

                                <i data-acorn-icon="chevron-left"></i>

                            </a>

                        </li>';

                    // Page links

                    for ($i = 1; $i <= $totalPages; $i++) {

                      echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">

                              <a class="page-link shadow" href="?itemsPerPage=' . $itemsPerPage . '&page=' . $i . '">' . $i . '</a>

                          </li>';

                    }

                    // Next page

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

        </div>

    </main>

    <div class="modal fade" id="activateCompanyModal" tabindex="-1" role="dialog" aria-labelledby="activateCompanyModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="activateCompanyModalLabel">Activate Company</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeActivateCompanyModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            Are you sure you want to activate this company?

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelActivateCompany">Cancel</button>

            <button type="button" class="btn btn-success" id="confirmActivate">Activate</button>

          </div>

        </div>

      </div>

    </div>

    <div class="modal fade" id="deleteCompanyModal" tabindex="-1" role="dialog" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="deleteCompanyModalLabel">Deactivate Company</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeDeleteCompanyModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelDeleteCompany">Cancel</button>

            <button type="button" class="btn btn-danger" id="confirmDelete">Deactivate</button>

          </div>

        </div>

      </div>

    </div>

    <!-- Create Card Modal -->

    <div class="modal fade" id="createCompanyModal" tabindex="-1" role="dialog" aria-labelledby="createCompanyModalLabel" aria-hidden="true">

      <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="createCompanyModalLabel">Create Company</h5>

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close" id="closeCreateCompanyModal">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            <form id="create-company-form" enctype="multipart/form-data">

              <input type="hidden" name="memberid" value="<?php echo $memberid; ?>">

              <div class="row mb-3">

                <div class="col-md-12">

                  <label class="form-label">Company Name</label>

                  <input name="cname" type="text" class="form-control" placeholder="iCard Cool" required />

                </div>

                <div class="error-message" id="cname-error"></div>

              </div>

              <div class="row mb-3">

                <div class="col-md-4">

                  <label class="form-label">Text Color</label>

                  <div class="input-with-color-picker">

                    <input name="textcolor" type="text" class="form-control" id="textColorInput" placeholder="#000000" required />

                    <input type="color" class="color-picker" id="textColorPicker" value="#000000" />

                  </div>

                  <div class="error-message" id="textcolor-error"></div>

                </div>

                <div class="col-md-4">

                  <label class="form-label">Background Color</label>

                  <div class="input-with-color-picker">

                    <input name="bgcolor" type="text" class="form-control" id="backgroundColorInput" placeholder="#000000" required />

                    <input type="color" class="color-picker" id="backgroundColorPicker" value="#ffffff" />

                  </div>

                  <div class="error-message" id="bgcolor-error"></div>

                </div>

                <div class="col-md-4">

                  <label class="form-label">Button Color</label>

                  <div class="input-with-color-picker">

                    <input name="buttoncolor" type="text" class="form-control" id="buttonColorInput" placeholder="#000000" required />

                    <input type="color" class="color-picker" id="buttonColorPicker" value="#000000" />

                  </div>

                  <div class="error-message" id="buttoncolor-error"></div>

                </div>

              </div>

              <div class="row mb-3">

                <div class="col-md-12">

                  <label class="form-label">slug</label>

                  <div class="input-with-color-picker">

                    <input name="cslug" type="text" class="form-control" id="slugInput" required />

                  </div>

                  <div class="error-message" id="cslug-error"></div>

                </div>

              </div>

              <div class="modal-footer">

                <label for="FuTaskDoc" class="btn btn-info">Choose Logo

                  <!--<i class="fa-solid fa-cloud-arrow-up"></i>-->

                </label>

                <input type="file" id="FuTaskDoc" name="fileToUpload" hidden="hidden">

                <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelCreateCompany">Cancel</button>

                <button type="button" class="btn btn-primary" id="saveCompany">Save</button>

              </div>

            </form>

            <div class="error-message" id="error-form"></div>

          </div>

        </div>

      </div>

    </div>

    <div id="successMessage" class="success-message">

      <span id="successMessageText" style="padding-right:20px;">Success message goes here</span>

      <button id="closeSuccessMessage" class="close-button">X</button>

    </div>

    <script>

      // Function to update text input from color picker

      function updateTextInputFromColorPicker(pickerId, inputId) {

        const picker = document.getElementById(pickerId);

        const input = document.getElementById(inputId);

        picker.addEventListener('input', () => {

          input.value = picker.value;

        });

      }

      // Function to update color picker from text input

      function updateColorPickerFromTextInput(inputId, pickerId) {

        const input = document.getElementById(inputId);

        const picker = document.getElementById(pickerId);

        input.addEventListener('input', () => {

          picker.value = input.value;

        });

      }

      // Update text input from color picker

      updateTextInputFromColorPicker('textColorPicker', 'textColorInput');

      updateTextInputFromColorPicker('backgroundColorPicker', 'backgroundColorInput');

      updateTextInputFromColorPicker('buttonColorPicker', 'buttonColorInput');

      // Update color picker from text input

      updateColorPickerFromTextInput('textColorInput', 'textColorPicker');

      updateColorPickerFromTextInput('backgroundColorInput', 'backgroundColorPicker');

      updateColorPickerFromTextInput('buttonColorInput', 'buttonColorPicker');

      

      // Function to set default colors if input values are empty

      function setDefaultColorsIfEmpty() {

        const textColorInput = document.getElementById('textColorInput');

        const backgroundColorInput = document.getElementById('backgroundColorInput');

        const buttonColorInput = document.getElementById('buttonColorInput');

        if (textColorInput.value.trim() === '') {

          textColorInput.value = '#000000'; // Set default text color

        }

        if (backgroundColorInput.value.trim() === '') {

          backgroundColorInput.value = '#ffffff'; // Set default background color

        }

        if (buttonColorInput.value.trim() === '') {

          buttonColorInput.value = '#ffffff'; // Set default button color

        }

      }

      // Call the function to set default colors when the page loads

      setDefaultColorsIfEmpty();

    </script>

    <footer>

      <?php include 'subpages/footer.php'; ?>

    </footer>

    <!-- Layout Footer End -->

  </div>

  <?php include 'subpages/footer-js.php'; ?>

  <script>

    // ...

    document.addEventListener('DOMContentLoaded', function() {

      const slugInput = document.getElementById('slugInput');

      // Attach an input event listener to the Slug input field

      slugInput.addEventListener('input', function() {

        // Convert to lowercase and replace any disallowed characters with an empty string

        const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');

        this.value = sanitizedValue;

      });

    });

    $(document).ready(function() {

      // ...

      // Add a click event for the delete button

      $('.activate-company').click(function() {

        var companyId = $(this).data('company_id');

        $('#activateCompanyModal').modal('show');

        // Store the cardId in the modal for later use

        $('#activateCompanyModal').data('company_id', companyId);

      });

      // Handle the confirmation of card deletion

      $('#confirmActivate').click(function() {

        // Get the cardId from the modal

        var companyId = $('#activateCompanyModal').data('company_id');

        // Perform the card deletion here

        // You can use an AJAX request to the server to update the card status to 1

        // Example:

        $.ajax({

          type: 'POST',

          url: '/activatecompany.php',

          data: {

            companyId: companyId

          },

          success: function(response) {

            // Handle the response, e.g., show a success message or redirect

            if (response.trim() === "Company activated successfully") {

              showSuccessMessage("Company activated successfully");

              $('#activateCompanyModal').modal('hide');

              setTimeout(function() {

                location.reload();

              }, 1000);

            } else {

              // Show an error message

              alert("Error: " + response);

            }

          },

          error: function() {

            // Handle errors

            alert("An error occurred while deleting the company.");

          }

        });

      });

      // Add a click event for the delete button

      $('.delete-company').click(function() {

        var companyId = $(this).data('company_id');

        var cusersId = $(this).data('cusers_id');



        // Check if cusersId is greater than 0

        if (cusersId > 0) {

          // Display the custom message in the modal body

          $('#deleteCompanyModal .modal-body').text('This company has ' + cusersId + ' user(s). Are you sure you want to deactivate it? User(s) will have default theme.');

        } else {

          // Display the default message in the modal body

          $('#deleteCompanyModal .modal-body').text('Are you sure you want to deactivate this company?');

        }



        $('#deleteCompanyModal').modal('show');

        $('#deleteCompanyModal').data('company_id', companyId);

        $('#deleteCompanyModal').data('cusers_id', cusersId);

      });

      // Handle the confirmation of card deletion

      $('#confirmDelete').click(function() {

        // Get the cardId from the modal

        var companyId = $('#deleteCompanyModal').data('company_id');

        var cusersId = $('#deleteCompanyModal').data('cusers_id');



        $.ajax({

          type: 'POST',

          url: '/deletecompany.php',

          data: {

            companyId: companyId,

            cusersId: cusersId

          },

          success: function(response) {

            if (response.trim() === "Company deactivated successfully") {

              showSuccessMessage("Company deactivated successfully");

              $('#deleteCompanyModal').modal('hide');

              setTimeout(function() {

                location.reload();

              }, 1000);

            } else {

              alert("Error: " + response);

            }

          },

          error: function() {

            // Handle errors

            alert("An error occurred while deleting the company.");

          }

        });

      });



      function validateForm() {

        var isValid = true;

        var cname = $('input[name="cname"]').val();

        var textcolor = $('input[name="textcolor"]').val();

        var bgcolor = $('input[name="bgcolor"]').val();

        var buttoncolor = $('input[name="buttoncolor"]').val();

        var cslug = $('input[name="cslug"]').val();

        if (cname.trim() === "") {

          $('#cname-error').text('Company is required').css('color', 'red');

          isValid = false;

        } else {

          $('#cname-error').text('');

        }

        if (textcolor.trim() === "") {

          $('#textcolor-error').text('Text Color is required').css('color', 'red');

          isValid = false;

        } else {

          $('#textcolor-error').text('');

        }

        if (bgcolor.trim() === "") {

          $('#bgcolor-error').text('Background Color is required').css('color', 'red');

          isValid = false;

        } else {

          $('#bgcolor-error').text('');

        }

        if (buttoncolor.trim() === "") {

          $('#buttoncolor-error').text('Button Color is required').css('color', 'red');

          isValid = false;

        } else {

          $('#buttoncolor-error').text('');

        }

        if (cslug.trim() === "") {

          $('#cslug-error').text('Company Slug is required').css('color', 'red');

          isValid = false;

        } else {

          $('#cslug-error').text('');

        }

        return isValid;

      }

      // Show the create card modal when the "Create" button is clicked

      $('#showCreateCompanyModal').click(function() {

        $('#createCompanyModal').modal('show');

      });

      // Handle saving the card when the "Save" button is clicked

      $('#saveCompany').click(function() {

        if (validateForm()) {

          var formData = new FormData($('#create-company-form')[0]);

          $.ajax({

            type: 'POST',

            url: '/createcompany.php', // Replace with the actual URL to handle card creation

            data: formData,

            processData: false, // Don't process the data

            contentType: false, // Don't set content type (it will be set automatically)

            success: function(response) {

              // Handle the response, e.g., show a success message or redirect

              if (response.trim() === "Company created successfully") {

                showSuccessMessage("Company created successfully");

                $('#createCompanyModal').modal('hide');

                $("#create-company-form")[0].reset();

                window.location.reload();

              } else {

                // Show an error message

                $('#error-form').text(response).addClass(

                  'alert alert-danger'); // Update the error message

              }

            },

            error: function() {

              // Handle errors

              $('#error-form').text(

                "An error occurred while creating the company.").addClass(

                'alert alert-danger'); // Update the error message

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

    });

    // JavaScript to close the Delete Company modal

    document.getElementById('closeActivateCompanyModal').addEventListener('click', function() {

      $('#activateCompanyModal').modal('hide');

    });

    document.getElementById('cancelActivateCompany').addEventListener('click', function() {

      $('#activateCompanyModal').modal('hide');

    });

    // JavaScript to close the Delete Company modal

    document.getElementById('closeDeleteCompanyModal').addEventListener('click', function() {

      $('#deleteCompanyModal').modal('hide');

    });

    document.getElementById('cancelDeleteCompany').addEventListener('click', function() {

      $('#deleteCompanyModal').modal('hide');

    });

    // JavaScript to close the Create Company modal

    document.getElementById('closeCreateCompanyModal').addEventListener('click', function() {

      $('#createCompanyModal').modal('hide');

    });

    document.getElementById('cancelCreateCompany').addEventListener('click', function() {

      $('#createCompanyModal').modal('hide');

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