<?php
function showTotalNumberOfEventsByType()
{
    $eventsByType = [];
    $qtyOfEventsByTypeFound = 0;
    $infoAboutQtyOfEventsByTypeFound = '';

    foreach(getEventFile() as $event) {

        if (filter_var(!empty($_POST['eventType']), FILTER_SANITIZE_STRING)) {

            if (strpos($event, $_POST['eventType']) !== false) {

                array_push($eventsByType, $event);
            }
        }
    }

    $qtyOfEventsByTypeFound = count($eventsByType);

    $infoAboutQtyOfEventsByTypeFound = "{$qtyOfEventsByTypeFound} {$_POST['eventType']} events found";

    if ($qtyOfEventsByTypeFound < 1) {

        $infoAboutQtyOfEventsByTypeFound = null;

    } else {

        echo $infoAboutQtyOfEventsByTypeFound;
    }
}

showTotalNumberOfEventsByType();