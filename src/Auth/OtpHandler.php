<?php
namespace Souhaibbenfarhat\Auth0app\Auth;

use Souhaibbenfarhat\Auth0app\Config\Database;
use PDO;
use Exception;

class OtpHandler
{
    private $conn;

    public function __construct()
    {
        // Initialize the database connection
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function generateOtp()
    {
        return bin2hex(random_bytes(4)); // Generate a random OTP (8 characters)
    }

    public function saveOtp($email, $otp)
    {
        // Save OTP to the auth table in your database
        $query = 'UPDATE auth SET otp = :otp, otp_created_at = NOW(), otp_used = 0 WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':otp', $otp);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to save OTP.');
        }
    }

    public function validateOtp($email, $otp)
    {
        // Validate the OTP from the auth table
        $query = 'SELECT * FROM auth WHERE email = :email AND otp = :otp AND otp_used = 0';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':otp', $otp);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mark OTP as used
            $updateQuery = 'UPDATE auth SET otp_used = 1 WHERE email = :email';
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();

            return true;
        } else {
            return false;
        }
    }
}
