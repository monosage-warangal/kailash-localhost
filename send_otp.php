<?php
session_start(); // Start session if not already started
include 'config.php'; // Include database connection file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to generate OTP
function generateOTP($length = 6) {
    return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
}

if(isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Generate OTP
    $otp = generateOTP();

    // Store OTP in session
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email; // Store email in session for verification

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fellowships@monosage.com';
        $mail->Password = 'Fellowships@12';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom($email, "New Registration");
        $mail->addAddress($email);
        //Content
        $mail->isHTML(true);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("🎉 Thanks for registering!") . "?=";
        $mail->Body    = "Hello,<br><br>Your OTP for registration is: <b>$otp</b>.<br><br>Please use this OTP to complete your registration.<br><br>Thank you.";

        $mail->send();
        echo 'success';
    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }
} else {
    echo "Invalid Request"; // Handle invalid requests
}
?>
