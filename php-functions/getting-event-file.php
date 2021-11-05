<?php
// Make the input 'event-file.txt' available for searching.
function getEventFile() 
{
    $pathToFile = '../assessment/event-file/event-file.txt';
    $errorMsg = 'Unfortunately, the file containing searchable data is missing, no query is possible at the moment.';

    if (!file_exists($pathToFile)) {
        die("<script>alert({$errorMsg})</script>");

    } else {

        return file($pathToFile);
    }
}
