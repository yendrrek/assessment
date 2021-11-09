<?php
// This function provides values for 'From timestamp' and 'To timestamp'.
// For optimal user experience typing is eliminated, range of timestamps given
// represents the actual entries from the input event file, so the user will always
// get a result, and will not be looking for timestamps which don't exist.
function getAllTimestampsInAscendingOrder()
{
    $timestampsInAscendingOrder = [];
    $eventFile = getEventFile();

    foreach($eventFile as $event) {

        // Timestamp is extracted from an event and formatted.
        $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

        array_push($timestampsInAscendingOrder, $timestamp);

        sort($timestampsInAscendingOrder);
    }

    return $timestampsInAscendingOrder;
}
