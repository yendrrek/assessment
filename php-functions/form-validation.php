<?php
// Token sent from a form is matched against the same token stored in $_SESSION.
// If they do not match, form is not submitted.  
function validateSearchForm()
{
    if (filter_var(!empty($_POST['tokenCsrf']), FILTER_SANITIZE_STRING)) {

        if (hash_equals($_SESSION['tokenCsrf'], $_POST['tokenCsrf'])) {

            return true;
        }
    }
}
