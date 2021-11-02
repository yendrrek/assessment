<?php
function validateSearchForm()
{
    if (!empty($_POST['tokenCsrf'])) {

        if (hash_equals($_SESSION['tokenCsrf'], $_POST['tokenCsrf'])) {

            return true;
        }
    }
}
