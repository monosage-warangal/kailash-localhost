<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Check if a file was uploaded
if ($_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    // Validate file type if necessary
    $fileType = strtolower(pathinfo($_FILES['profilePic']['name'], PATHINFO_EXTENSION));
    if ($fileType != 'jpg' && $fileType != 'png') {
        echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
        exit();
    }

    // Move uploaded file to target directory
    $targetDir = 'uploads/';
    $newFileName = uniqid('profile_', true) . '.' . $fileType;
    $targetFilePath = $targetDir . $newFileName;

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFilePath)) {
        // Update profile picture path in the database
        $sql = "UPDATE users SET profile_pic = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $targetFilePath, $email);

        if ($stmt->execute()) {
            echo "Profile picture uploaded successfully.";
        } else {
            echo "Error updating profile picture: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "Upload error: " . $_FILES['profilePic']['error'];
}

$conn->close();
?>
