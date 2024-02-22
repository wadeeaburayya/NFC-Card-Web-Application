<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit;
}
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cname = $_POST['cname'];
    $bgcolor = $_POST['bgcolor'];
    $textcolor = $_POST['textcolor'];
    $buttoncolor = $_POST['buttoncolor'];
    $cslug = $_POST['cslug'];
    $company_id = $_POST['company_id'];
    
    if (!preg_match('/^[a-zA-Z0-9\s.,]+$/', $cname)) {
        echo "Company name can only contain alphanumeric characters and spaces.";
        exit;
    }
    if (!preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $bgcolor) || !preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $textcolor) || !preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $buttoncolor)) {
        echo "Invalid color format. Please use hex color code like #ffffff.";
        exit;
    }

    $checkSlugQuery = "SELECT cslug FROM company WHERE cslug = ? AND NOT company_id = '$company_id'";
    $checkStmt = $conn->prepare($checkSlugQuery);
    $checkStmt->bind_param("s", $cslug);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo "Slug already exists.";
        exit;
    } else {
        if (!empty($_FILES['fileToUpload']['name'])) {
            $uploadDirectory = 'uploads/';
            $uploadedFile = $_FILES['fileToUpload']['tmp_name'];

            $originalFileName = $_FILES['fileToUpload']['name'];
            $originalFileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

            // Allow PNG, JPG, and JPEG formats
            if (in_array($originalFileExtension, ['png', 'jpg', 'jpeg'])) {
                if ($originalFileExtension === 'png') {
                    $source = imagecreatefrompng($uploadedFile);
                } elseif (in_array($originalFileExtension, ['jpg', 'jpeg'])) {
                    $source = imagecreatefromjpeg($uploadedFile);
                }

                if (!$source) {
                    echo "Unable to create image from file.";
                    exit;
                }

                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $originalFileExtension;

                $newWidth = 150;
                $newHeight = 150;
                $sourceWidth = imagesx($source);
                $sourceHeight = imagesy($source);

                $aspectRatio = $sourceWidth / $sourceHeight;
                $newAspectRatio = $newWidth / $newHeight;

                if ($aspectRatio > $newAspectRatio) {
                    $targetWidth = $newWidth;
                    $targetHeight = $newWidth / $aspectRatio;
                } else {
                    $targetWidth = $newHeight * $aspectRatio;
                    $targetHeight = $newHeight;
                }

                // Create a new image with the desired dimensions
                $thumb = imagecreatetruecolor($newWidth, $newHeight);

                // Resize the image while preserving the aspect ratio
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

                // Save the resized image
                imagepng($thumb, $uploadDirectory . $newFileName);

                // Clean up resources
                imagedestroy($thumb);
                imagedestroy($source);
                $updateQuery = "UPDATE company SET cname = ?, logo = ?, bgcolor = ?, buttoncolor = ?, textcolor = ?, cslug = ? WHERE company_id = ?";
                $stmt = $conn->prepare($updateQuery);

                if ($stmt === false) {
                    echo "Error: Unable to prepare statement";
                } else {
                    $stmt->bind_param("ssssssi", $cname, $newFileName, $bgcolor, $buttoncolor,  $textcolor, $cslug, $company_id);
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            echo "Company data updated successfully.";
                        } else {
                            echo "No2 changes made to the company";
                        }
                    } else {
                        echo "Company update failed";
                    }
                    $stmt->close();
                }
            } else {
                echo "Unsupported file format. Please upload a PNG, JPG, or JPEG image.";
                exit;
            }
        } else {
            $updateQuery = "UPDATE company SET cname = ?, bgcolor = ?, buttoncolor = ?, textcolor = ?, cslug = ? WHERE company_id = ?";
            $stmt = $conn->prepare($updateQuery);

            if ($stmt === false) {
                echo "Unable to prepare statement";
            } else {
                $stmt->bind_param("sssssi", $cname, $bgcolor, $buttoncolor, $textcolor, $cslug, $company_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo "Company data updated successfully.";
                    } else {
                        echo "No changes made to the company";
                    }
                } else {
                    echo "Company update failed";
                }
                $stmt->close();
            }
        }


        $checkStmt->close();
    }
} else {
    echo "Invalid request method";
}
$conn->close();
