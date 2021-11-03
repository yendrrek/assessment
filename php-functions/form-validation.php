<?php
$_SESSION['tokenCsrf'] = $_SESSION['tokenCsrf'] ?? bin2hex(random_bytes(64));

function validateSearchForm()
{
    if (!empty($_POST['tokenCsrf'])) {

        if (hash_equals($_SESSION['tokenCsrf'], $_POST['tokenCsrf'])) {

            return true;
        }
    }
}
