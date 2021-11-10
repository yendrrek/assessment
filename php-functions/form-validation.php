<?php
$_SESSION['tokenCsrf'] = $_SESSION['tokenCsrf'] ?? bin2hex(random_bytes(64));

function validateSearchForm()
{
    if (!empty($_POST['tokenCsrf'])) {

        $csrfTokenSentFromForm = filter_var($_POST['tokenCsrf'], FILTER_SANITIZE_STRING);

        return hash_equals($_SESSION['tokenCsrf'], $csrfTokenSentFromForm) ?? false;
    }
}
