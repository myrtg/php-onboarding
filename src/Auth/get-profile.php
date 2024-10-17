<?php
require '../../vendor/autoload.php';

use Souhaibbenfarhat\Auth0app\Config\Database;

header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input from the request body
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if the email is provided
    if (!isset($input['email'])) {
        echo json_encode(['error' => 'Email is required']);
        http_response_code(400);
        exit();
    }

    $email = $input['email'];

    try {
        // Initialize the database connection
        $db = new Database();
        $conn = $db->connect();

        // Fetch user data from the employees table
        $query = "
            SELECT e.first_name, e.last_name, e.username, e.gender, e.email, e.service, e.position, e.site, 
                   e.employment_status, e.account_status, e.invitation_status, e.creation_date, e.role, e.ou, e.profile_completed
            FROM employees e
            WHERE e.email = :email";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Return user profile data as JSON
            echo json_encode($user);
            http_response_code(200);
        } else {
            // User not found
            echo json_encode(['error' => 'User not found']);
            http_response_code(404);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    // Invalid request method
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
}
