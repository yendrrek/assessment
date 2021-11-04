<?php
// Make the input 'event-file.txt' available for searching.
function getEventFile() 
{
    if (!file_exists('../assessment/event-file/event-file.txt')) {
        die('<script>alert("Unfortunately, the file containing searchable data is missing, and no query is possible at the moment.")</script>');

    } else {

        return file('../assessment/event-file/event-file.txt');
    }
}
