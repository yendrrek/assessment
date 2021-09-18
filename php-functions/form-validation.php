<?php
function validateSearchForm()
{
    if (!empty($_POST['tokenCsrf'])) {

        if (hash_equals($_SESSION['tokenCsrf'], $_POST['tokenCsrf'])) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                return true;
            }
        }
    }
}
