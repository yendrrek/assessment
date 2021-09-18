<?php
function showEventsByType()
{
    $eventsByType = [];
    $typesOfEvents = ['INSERTED', 'UPDATED', 'DELETED'];

    foreach(getEventFile() as $event) {

        if (!empty($_POST['btnEventType']) && !empty($_POST['eventType']) && validateSearchForm() === true) {

            if ($_POST['eventType'] === 'Event type') {

                echo 'No \'Event type\' selected.';

                break;

            } elseif (strpos($event, $_POST['eventType']) !== false) {

                echo $event . '<br><br>';
            }

        } elseif (!empty($_POST['combinedQuery']) && !empty($_POST['eventType'])) {

            if (in_array($_POST['eventType'], $typesOfEvents)) {

                foreach ($typesOfEvents as $types) {

                    if (strpos($event, $_POST['eventType']) !== false) {

                        array_push($eventsByType, $event);

                        break; 
                    }
                }

            } else {

                array_push($eventsByType, $event);
            }
        }
    }

    return $eventsByType;
}

function showEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = [];
    $fieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate'];

    foreach(getEventFile() as $event) {

        if (!empty($_POST['btnFieldsUpdated']) && !empty($_POST['fieldsUpdated']) && validateSearchForm() === true) {

            if ($_POST['fieldsUpdated'] === 'Fields updated') {

                echo 'No \'Fields updated\' selected.';

                break;

            } elseif (strpos($event, $_POST['fieldsUpdated']) !== false) {

                echo $event . '<br><br>';
            }
        }
    }

    foreach(showEventsByType() as $event) {

        if (!empty($_POST['combinedQuery']) && !empty($_POST['fieldsUpdated'])) {

            if (in_array($_POST['fieldsUpdated'], $fieldsUpdated)) {

                foreach ($fieldsUpdated as $fields) {

                    if (strpos($event, $_POST['fieldsUpdated']) !== false) {

                        array_push($eventsByFieldsUpdated, $event);

                        break;
                    }
                }

            } else {

                array_push($eventsByFieldsUpdated, $event);

            }
        }
    }

    return $eventsByFieldsUpdated;
}

showEventsByFieldsUpdated();

function showEventsByRangeOfTimestamps()
{
    $eventsByRangeOfTimestamps = [];
    $invalidRangeOfTimestampsMsg = '\'From\' cannot be greater than \'To\', you silly sausage!';

    if (!empty($_POST['fromTimestamp']) && !empty($_POST['toTimestamp']) && validateSearchForm() === true) {

        $from = $_POST['fromTimestamp'];
        $to = $_POST['toTimestamp'];

        if (!empty($_POST['btnTimestamps'])) {

            foreach(getEventFile() as $event) {

                // Timestamps are extracted from the events and formated.
                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ($from === 'From timestamp' || $to === 'To timestamp') {

                    echo 'No timestamp range selected.';

                    break;

                } else {

                    if ($timestamp >= $from && $timestamp <= $to) {

                        echo $event . '<br><br>';

                    } elseif ($timestamp < $from && $timestamp > $to) {

                        echo $invalidRangeOfTimestampsMsg;

                        break;
                    }
                }
            }

        } elseif (!empty($_POST['combinedQuery'])) {

            foreach(showEventsByFieldsUpdated() as $event) {

                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ($from === 'From timestamp' || $to === 'To timestamp') {

                   $timestampRangeError = 'No timestamp range selected.';

                   return $timestampRangeError;
                   
                } else {

                    if ($timestamp >= $from && $timestamp <= $to) {

                        array_push($eventsByRangeOfTimestamps, $event);

                    } elseif ($timestamp < $from && $timestamp > $to) {

                        return $invalidRangeOfTimestampsMsg;
                    }
                }
            }

            return $eventsByRangeOfTimestamps;
        }
    }
}

showEventsByRangeOfTimestamps();

function showCombinedResult()
{
    if (!empty($_POST['combinedQuery']) && validateSearchForm() === true) {

        showEventsByType();

        showEventsByFieldsUpdated();

        if (!empty(showEventsByRangeOfTimestamps())) {

            if (is_string(showEventsByRangeOfTimestamps())) {

                if (strpos(showEventsByRangeOfTimestamps(), 'sausage') !== false) {

                    echo showEventsByRangeOfTimestamps();

                } elseif (strpos(showEventsByRangeOfTimestamps(), 'timestamp') !== false) {

                    echo showEventsByRangeOfTimestamps();
                }

            } else {

                foreach(showEventsByRangeOfTimestamps() as $event) {

                    echo $event . '<br><br>';
                }
            }

        } else {

            echo 'No entries exist with chosen options.';
        }
    }
}

showCombinedResult();
