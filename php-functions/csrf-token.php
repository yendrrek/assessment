<?php
// Generate random token on each HTTP request for each
// form to prevent Cross-Site Request Forgery.
function createCsrfToken()
{
    if (!isset($_SESSION['tokenCsrf'])) {
        $_SESSION['tokenCsrf'] = bin2hex(random_bytes(64));
    }

    $tokenCsrf = $_SESSION['tokenCsrf'];

    return $tokenCsrf;
}
