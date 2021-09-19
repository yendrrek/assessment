<?php
// On each HTTP request a token is randomly generated 
// and used in each form to prevent Cross-Site Request Forgery attacks. 
function createCsrfToken()
{
    if (!isset($_SESSION['tokenCsrf'])) {

        $_SESSION['tokenCsrf'] = bin2hex(random_bytes(64));
    }

    $tokenCsrf = $_SESSION['tokenCsrf'];

    return $tokenCsrf;
}
