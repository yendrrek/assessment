<?php

// This function feeds with options 'From' and 'To' timestamp drop-down menu.
// For optimal user experience typing is eliminated, and range of timestamps given 
// represents the acutal entries from the events file, so the user will always
// get a result, and will not be looking for timestamps which don't even exist.

function getAllTimestampsInAscendingOrder()
{
    $timestampsInAscendingOrder = [];

    foreach(getEventFile() as $event) {

        // Timestamps are extracted from the events and formated.
        $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

        array_push($timestampsInAscendingOrder, $timestamp);

        sort($timestampsInAscendingOrder);
    }

    return $timestampsInAscendingOrder;
}
