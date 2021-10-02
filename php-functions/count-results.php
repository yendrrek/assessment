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

function showTotalNumberOfEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = [];
    $qtyOfEventsByFieldsUpdatedFound = 0;
    $infoAboutQtyOfEventsByFieldsUpdatedFound = $infoAboutQtyOfEventsFoundWithNoFieldsUpdated = '';

    foreach(getEventFile() as $event) {

        if (filter_var(!empty($_POST['fieldsUpdated']), FILTER_SANITIZE_STRING)) {

            if (strpos($event, $_POST['fieldsUpdated']) !== false) {

                array_push($eventsByFieldsUpdated, $event);
            }
        }
    }

    $qtyOfEventsByFieldsUpdatedFound = count($eventsByFieldsUpdated);

    $infoAboutQtyOfEventsByFieldsUpdatedFound =
      "{$qtyOfEventsByFieldsUpdatedFound} events found with updated field '{$_POST['fieldsUpdated']}'";

    $infoAboutQtyOfEventsFoundWithNoFieldsUpdated =
      "{$qtyOfEventsByFieldsUpdatedFound} events found with no fields updated";

    if ($qtyOfEventsByFieldsUpdatedFound < 1) {

        $infoAboutQtyOfEventsByFieldsUpdatedFound = null;

    } elseif ($_POST['fieldsUpdated'] === 'null') {

        echo $infoAboutQtyOfEventsFoundWithNoFieldsUpdated;

    } else {

        echo $infoAboutQtyOfEventsByFieldsUpdatedFound;
    }
}

showTotalNumberOfEventsByFieldsUpdated();