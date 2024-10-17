<?php
require '../../vendor/autoload.php'; // Adjust path to autoload as necessary

use Souhaibbenfarhat\Auth0app\Auth\OtpHandler; // Use correct namespace

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input from the request
    $data = json_decode(file_get_contents("php://input"), true);

    $email = $data['email'] ?? null;  // Ensure email is provided
    $otp = $data['otp'] ?? null;      // Ensure OTP is provided

    // Validate if both email and OTP are provided
    if (empty($email) || empty($otp)) {
        echo json_encode(['message' => 'Email and OTP are required']);
        http_response_code(400); // Bad request status
        exit;
    }

    // Create an instance of OtpHandler
    $otpHandler = new OtpHandler();

    // Validate the OTP using the OtpHandler class
    $isValidOtp = $otpHandler->validateOtp($email, $otp);

    // If OTP is valid
    if ($isValidOtp) {
        echo json_encode(['message' => 'OTP verified successfully']);
        http_response_code(200); // Success status
    } else {
        echo json_encode(['message' => 'Invalid OTP']);
        http_response_code(400); // Bad request status
    }
}
