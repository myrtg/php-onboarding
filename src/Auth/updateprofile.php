<?php
namespace Souhaibbenfarhat\Auth0app\Auth;

use Souhaibbenfarhat\Auth0app\Config\Database;
use PDO;
use Exception;

require '../../vendor/autoload.php'; // Adjust path as needed

// Create a new instance of Database and connect
$db = new Database();
$conn = $db->connect(); // Use the connect method from Database class

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if required fields are present
    if (empty($data['email']) || empty($data['firstName']) || empty($data['lastName'])) {
        echo json_encode(['message' => 'Required fields are missing']);
        http_response_code(400); // Bad request
        exit();
    }

    $email = $data['email'];
    $first_name = $data['firstName'];
    $last_name = $data['lastName'];
    $position = !empty($data['position']) ? $data['position'] : null;  // Allow position to be empty

    try {
        // Build query dynamically if position is not provided
        $query = 'UPDATE employees SET first_name = :first_name, last_name = :last_name, profile_completed = 1';

        if ($position !== null) {
            $query .= ', position = :position';
        }

        $query .= ' WHERE email = :email';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);

        if ($position !== null) {
            $stmt->bindParam(':position', $position);
        }

        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Profile updated successfully']);
            http_response_code(200); // Success
        } else {
            echo json_encode(['message' => 'Failed to update profile']);
            http_response_code(500); // Internal server error
        }
    } catch (Exception $e) {
        echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
        http_response_code(500); // Internal server error
    }
} else {
    echo json_encode(['message' => 'Invalid request method']);
    http_response_code(405); // Method not allowed
}
