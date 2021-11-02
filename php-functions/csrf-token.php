<?php
function createCsrfToken()
{
    return $_SESSION['tokenCsrf'] = $_SESSION['tokenCsrf'] ?? bin2hex(random_bytes(64));
}
