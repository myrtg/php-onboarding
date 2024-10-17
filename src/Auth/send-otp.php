<?php
require '../../vendor/autoload.php';

use Souhaibbenfarhat\Auth0app\Auth\OtpHandler;
use Souhaibbenfarhat\Auth0app\Mail\Mailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];

    // Check if email is provided
    if (empty($email)) {
        echo json_encode(['message' => 'Email is required']);
        http_response_code(400);
        exit;
    }

    // Initialize OtpHandler and Mailer
    $otpHandler = new OtpHandler();
    $mailer = new Mailer();

    // Generate OTP and save it to the auth table
    $otp = $otpHandler->generateOtp();
    if (!$otpHandler->saveOtp($email, $otp)) {
        echo json_encode(['message' => 'Failed to save OTP']);
        http_response_code(500);
        exit;
    }

    // Create the email body
    $verifyUrl = "http://localhost:3000/verify-otp?email=" . urlencode($email);
    $subject = "Your OTP Code";
    $body = "Here is your OTP code: $otp. \n\nClick the link to verify your OTP: $verifyUrl";

    // Send the OTP email
    if ($mailer->sendMail($email, $subject, $body)) {
        echo json_encode(['message' => 'OTP sent successfully']);
    } else {
        echo json_encode(['message' => 'Failed to send OTP']);
        http_response_code(500);
    }
}
