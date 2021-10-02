<?php
function showTotalNumberOfFoundEventsByType()
{
    $eventsByType = [];
    $qtyOfEventsByType = 0;
    $infoAboutqtyOfEventsByType = '';

    foreach(getEventFile() as $event) {

        if (filter_var(!empty($_POST['eventType']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

            if (strpos($event, $_POST['eventType']) !== false) {

                array_push($eventsByType, $event);
            }
        }
    }

    $qtyOfEventsByType = count($eventsByType);

    $infoAboutqtyOfEventsByType = "{$qtyOfEventsByType} {$_POST['eventType']} events found";

    if ($qtyOfEventsByType < 1) {

        $infoAboutqtyOfEventsByType = null;

    } else {

        echo $infoAboutqtyOfEventsByType;
    }
}

showTotalNumberOfFoundEventsByType();

function showTotalNumberOfFoundEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = [];
    $qtyOfEventsByFieldsUpdated = 0;
    $infoAboutqtyOfEventsByFieldsUpdated = $infoAboutQtyOfEventsWithNoFieldsUpdated = '';

    foreach(getEventFile() as $event) {

        if (filter_var(!empty($_POST['fieldsUpdated']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

            if (strpos($event, $_POST['fieldsUpdated']) !== false) {

                array_push($eventsByFieldsUpdated, $event);
            }
        }
    }

    $qtyOfEventsByFieldsUpdated = count($eventsByFieldsUpdated);

    $infoAboutqtyOfEventsByFieldsUpdated =
      "{$qtyOfEventsByFieldsUpdated} events found with updated field '{$_POST['fieldsUpdated']}'";

    $infoAboutQtyOfEventsWithNoFieldsUpdated =
      "{$qtyOfEventsByFieldsUpdated} events found with no fields updated";

    if ($qtyOfEventsByFieldsUpdated < 1) {

        $infoAboutqtyOfEventsByFieldsUpdated = null;

    } elseif ($_POST['fieldsUpdated'] === 'null') {

        echo $infoAboutQtyOfEventsWithNoFieldsUpdated;

    } else {

        echo $infoAboutqtyOfEventsByFieldsUpdated;
    }
}

showTotalNumberOfFoundEventsByFieldsUpdated();

/*function showTotalNumberOfFoundEventsBetweenTimestamps()
{

    $eventsByRangeOfTimestamps = [];
    $qtyOfEventsByRangeOfTimestamps = 0;
    $infoAboutqtyOfEventsByRangeOfTimestamps = '';

    foreach(getEventFile() as $event) {

        if (filter_var(!empty($_POST['fromTimestamp']), FILTER_SANITIZE_STRING) &&
            filter_var(!empty($_POST['toTimestamp']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {


        }

    }
}*/