<?php
// Token sent from a form is matched against the same token stored in $_SESSION.
// If they do not match, form is not submitted.  
function validateSearchForm()
{
    if (!empty($_POST['tokenCsrf'])) {

        if (hash_equals($_SESSION['tokenCsrf'], $_POST['tokenCsrf'])) {

            return true;
        }
    }
}
