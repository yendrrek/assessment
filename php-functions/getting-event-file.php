<?php
// Make the input 'event-file.txt' available for searching.
function getEventFile() 
{
    file_exists('../assessment/event-file/event-file.txt') ?
    $eventFile = file('../assessment/event-file/event-file.txt') :
    die("<script>alert('File containing searchable data is missing.')</script>");

    return $eventFile;
}

