<?php
require '../vendor/autoload.php';

use Auth0\SDK\Auth0;

$auth0 = new Auth0([
    'domain' => getenv('AUTH0_DOMAIN'),
    'client_id' => getenv('AUTH0_CLIENT_ID'),
    'client_secret' => getenv('AUTH0_CLIENT_SECRET'),
    'redirect_uri' => 'http://localhost/auth0app/welcome.php',
    'scope' => 'openid profile email',
]);

$auth0User = $auth0->getUser();

if (!$auth0User) {
    echo "Authentication failed.";
    exit;
}

// If authentication is successful, retrieve the user information
echo "Welcome, " . $auth0User['name'] . "!";
echo "<br>Your email: " . $auth0User['email'];

// Complete registration or redirect to dashboard
