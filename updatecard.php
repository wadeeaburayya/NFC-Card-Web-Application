<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit;
}
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $member_id = $_SESSION['member_id'];
    $name = $_POST['name'];
    $title = $_POST['title'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $linkedinacc = $_POST['linkedinacc'];
    $website = $_POST['website'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $x = $_POST['x'];
    $link = $_POST['link'];
    $company_id = $_POST['company_id'];

    if (!preg_match('/^[\p{L}0-9\s.]+$/u', $name)) {
        echo "Name can only contain letters, numbers, spaces, and dots.";
        exit;
    }
    
    
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        echo "Invalid website URL.";
        exit;
    }

    $checkSlugQuery = "SELECT link FROM users WHERE link = ? AND NOT id = '$id'";
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

                $newWidth = 800;
                $newHeight = 800;
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
                if ($company_id == 0 || $address == "") {
                    $updateQuery = "UPDATE users SET name = ?, title = ?, phonenumber = ?, link = ?, member_id = ?, photo = ?, email = ?, website = ?, linkedinacc = ?, facebook = ?, instagram = ?, x = ? WHERE id = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("ssssisssssssi", $name, $title, $phonenumber, $link, $member_id, $newFileName, $email, $website, $linkedinacc, $facebook, $instagram, $x, $id);
                } else {
                    $updateQuery = "UPDATE users SET name = ?, title = ?, phonenumber = ?, address = ?, link = ?, member_id = ?, photo = ?, email = ?, website = ?, linkedinacc = ?, facebook = ?, instagram = ?, x = ?, company_id = ? WHERE id = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("ssssssissssssii", $name, $title, $phonenumber, $address, $newFileName, $link, $member_id, $email, $website, $linkedinacc, $facebook, $instagram, $x, $company_id, $id);
                }


                if ($stmt === false) {
                    echo "Unable to prepare statement";
                } else {
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            echo "Card data updated successfully.";
                        } else {
                            echo "No changes made to the card";
                        }
                    } else {
                        echo "Card update failed";
                    }
                    $stmt->close();
                }
            } else {
                echo "Unsupported file format. Please upload a PNG, JPG, or JPEG image.";
                exit;
            }
        } else {
            if ($company_id == 0 || $address == "") {
                $updateQuery = "UPDATE users SET name = ?, title = ?, phonenumber = ?, link = ?, member_id = ?, email = ?, website = ?, linkedinacc = ?, facebook = ?, instagram = ?, x = ? WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssssissssssi", $name, $title, $phonenumber, $link, $member_id, $email, $website, $linkedinacc, $facebook, $instagram, $x, $id);
            } else {
                $updateQuery = "UPDATE users SET name = ?, title = ?, phonenumber = ?, address = ?, link = ?, member_id = ?, email = ?, website = ?, linkedinacc = ?, facebook = ?, instagram = ?, x = ?, company_id = ? WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sssssissssssii", $name, $title, $phonenumber, $address, $link, $member_id, $email, $website, $linkedinacc, $facebook, $instagram, $x, $company_id, $id);
            }

            if ($stmt === false) {
                echo "Unable to prepare statement";
            } else {
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo "Card data updated successfully.";
                    } else {
                        echo "No changes made to the card";
                    }
                } else {
                    echo "Card update failed";
                }
                $stmt->close();
            }
        }


        $checkStmt->close();
    }
}
