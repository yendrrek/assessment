<?php
// Make the input 'event-file.txt' available for processing.
function getEventFile() 
{
    $eventFile = [];

    if (!file_exists('../assessment/event-file/event-file.txt')) {
        die('<script>alert("Unfortunately, the file containing searchable data is missing, and no query is possible at the moment.")</script>');

    } else {

        $eventFile = file('../assessment/event-file/event-file.txt');

        return $eventFile;
    }
}
