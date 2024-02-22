<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: sign-in.php");
    exit;
}
require_once 'config.php';
require_once 'vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : 1;
    $title = $_POST['title'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $linkedinacc = $_POST['linkedinacc'];
    $instagram = $_POST['instagram'];
    $x = $_POST['x'];
    $facebook = $_POST['facebook'];
    $website = $_POST['website'];
    $member_id = $_POST['member_id'];
    if (!preg_match('/^[\p{L}0-9\s.]+$/u', $full_name)) {
        echo "Name can only contain letters, numbers, spaces, and dots.";
        exit;
    }
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        echo "Invalid website URL.";
        exit;
    }
    // Check if the slug already exists
    $checkSlugQuery = "SELECT link FROM users WHERE link = ?";
    $checkStmt = $conn->prepare($checkSlugQuery);
    $checkStmt->bind_param("s", $slug);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo "Slug already exists. Please choose a different one.";
    } else {
        $new_cardid = null;
        $path = 'qrcodes/';
        $qrFileName = 'qrcode_' . bin2hex(random_bytes(8)) . '.png'; // Generate a unique QR code filename
        $link = "?cardid=$new_cardid";
        $qrCode = (new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'imageBase64' => false,
        ])))->render($link);
        file_put_contents($path . $qrFileName, $qrCode);
        $newFileName = null;
        if (!empty($_FILES['fileToUpload']['name'])) {
            $uploadDirectory = 'uploads/';
            $uploadedFile = $_FILES['fileToUpload']['tmp_name'];
            $originalFileName = $_FILES['fileToUpload']['name'];
            $originalFileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            // Allow both JPEG and PNG formats
            if ($originalFileExtension === 'jpg' || $originalFileExtension === 'jpeg' || $originalFileExtension === 'png') {
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $originalFileExtension;
                // Load the original image
                if ($originalFileExtension === 'jpg' || $originalFileExtension === 'jpeg') {
                    $source = imagecreatefromjpeg($uploadedFile);
                } elseif ($originalFileExtension === 'png') {
                    $source = imagecreatefrompng($uploadedFile);
                }
                // Calculate the new dimensions while maintaining the aspect ratio
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
                if ($originalFileExtension === 'jpg' || $originalFileExtension === 'jpeg') {
                    imagejpeg($thumb, $uploadDirectory . $newFileName);
                } elseif ($originalFileExtension === 'png') {
                    imagepng($thumb, $uploadDirectory . $newFileName);
                }
                // Clean up resources
                imagedestroy($thumb);
                imagedestroy($source);
            } else {
                echo "Unsupported file format. Please upload a JPEG or PNG image.";
                exit;
            }
        } else {
            $newFileName = "defaultpp.png";
        }
        $insertQuery = "INSERT INTO users (member_id, name, company_id, title, phonenumber, address, email, link, linkedinacc, instagram, facebook, x, website, photo, qrcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        if ($stmt) {
            $stmt->bind_param("isissssssssssss", $member_id, $full_name, $company_id, $title, $phonenumber, $address, $email, $slug, $linkedinacc, $instagram, $facebook, $x, $website, $newFileName, $qrFileName); // Use the resized image file name
            if ($stmt->execute()) {
                // Fetch the newly inserted cardid
                $result = $conn->query("SELECT cardid FROM users WHERE link = '$slug'");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $new_cardid = $row['cardid'];
                    $link = "?cardid=$new_cardid";
                    // Update the QR code with the correct link
                    $qrCode = (new QRCode(new QROptions([
                        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                        'eccLevel' => QRCode::ECC_L,
                        'imageBase64' => false,
                    ])))->render($link);
                    file_put_contents($path . $qrFileName, $qrCode);
                    // Update the database with the correct qrcode filename
                    $updateQuery = "UPDATE users SET qrcode = ? WHERE cardid = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    if ($updateStmt) {
                        $updateStmt->bind_param("si", $qrFileName, $new_cardid);
                        if ($updateStmt->execute()) {
                            echo "Card created successfully";
                        } else {
                            echo "Failed to update QR code image: " . $updateStmt->error;
                        }
                        $updateStmt->close();
                    } else {
                        echo "Unable to prepare update statement";
                    }
                } else {
                    echo "Could not retrieve the new card's ID";
                }
            } else {
                echo "Card creation failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Unable to prepare statement";
        }
        // Close the check statement
        $checkStmt->close();
    }
} else {
    echo "Invalid request method";
}
$conn->close();
