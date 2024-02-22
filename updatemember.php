<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission and update member details
    $memberid = $_POST['member_id'];
    $newName = $_POST['namesurname'];
    $newEmail = $_POST['member_email'];

    // Check if any required field is empty
    if (empty($memberid) || empty($newName) || empty($newEmail)) {
        echo "Please fill in all required fields.";
        exit();
    }

    // Check if a file was uploaded
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        // Process the uploaded file (you might want to add additional checks and validations)
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
                $source = @imagecreatefrompng($uploadedFile);
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

            // Update the member's profile picture in the database
            $updateProfilePic = $conn->prepare("UPDATE members SET pp = ? WHERE member_id = ?");
            $updateProfilePic->bind_param("si", $newFileName, $memberid);
            $updateProfilePic->execute();
            $updateProfilePic->close();
        } else {
            echo "Error: Unsupported file format. Please upload a JPEG or PNG image.";
            exit;
        }
    }

    // Update member's name and email
    $updateMemberDetails = $conn->prepare("UPDATE members SET namesurname = ?, member_email = ? WHERE member_id = ?");
    $updateMemberDetails->bind_param("ssi", $newName, $newEmail, $memberid);
    $updateMemberDetails->execute();
    $updateMemberDetails->close();

    echo "Card data updated successfully.";
    exit();
}
