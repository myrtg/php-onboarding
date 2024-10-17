<?php
require '../../vendor/autoload.php'; // Adjust path as needed

use Souhaibbenfarhat\Auth0app\Config\Database;

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

// Get the incoming data
$data = json_decode(file_get_contents('php://input'), true);
$password = $data['password'] ?? '';

// Validate password
if (empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Password is required']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Initialize the database connection
$db = new Database();
$conn = $db->connect();

// Assuming you have the user's ID stored in the session or passed along with the request
session_start();
$employeeId = $_SESSION['employee_id'] ?? null;

if (!$employeeId) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'User not found']);
    exit;
}

// Update the user's password in the database
$stmt = $conn->prepare("UPDATE auth SET password = :password, password_set = 1 WHERE employee_id = :employee_id");
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':employee_id', $employeeId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Password updated successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Failed to update password']);
}
