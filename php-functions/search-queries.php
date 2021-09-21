<?php
function showEventsByType()
{
    $eventsByType = $arrayWithEvents = [];
    $typesOfEvents = [
        'INSERTED',
        'UPDATED',
        'DELETED'
    ];

    // 'getEventFile()' returns the input 'event-file.txt'. 
    foreach(getEventFile() as $event) {

        // Show events by their types only.
        if (filter_var(!empty($_POST['btnEventType']), FILTER_SANITIZE_STRING) &&
            filter_var(!empty($_POST['eventType']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

            if ($_POST['eventType'] === 'Event type') {

                echo 'No \'Event type\' selected.';

                break;

            } elseif (strpos($event, $_POST['eventType']) !== false) {
                
                // Print result on the screen.
                echo $event . '<br><br>';
            }
        
        // Perform combined searching.    
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
    
    // When performing combined searching the returned array with types of events
    // is later filtered by the next function 'showEventsByFieldsUpdated()' 
    return $eventsByType;
}

function showEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = [];
    $fieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate', 'null'];

    foreach(getEventFile() as $event) {

        // Show events by fields updated only.
        if (filter_var(!empty($_POST['btnFieldsUpdated']), FILTER_SANITIZE_STRING) &&
            filter_var(!empty($_POST['fieldsUpdated']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

            if ($_POST['fieldsUpdated'] === 'Fields updated') {

                echo 'No \'Fields updated\' selected.';

                break;

            } elseif (strpos($event, $_POST['fieldsUpdated']) !== false) {

                echo $event . '<br><br>';
            }
        }
    }

    // Result of search returned earlier, and filtered further when performing combined searching.  
    foreach(showEventsByType() as $event) {

        if (!empty($_POST['combinedQuery']) &&
            !empty($_POST['fieldsUpdated'])) {

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
    // Array filtered later by the next function 'showEventsByRangeOfTimestamps()'.
    return $eventsByFieldsUpdated;
}

showEventsByFieldsUpdated();

function showEventsByRangeOfTimestamps()
{
    $eventsByRangeOfTimestamps = [];
    $invalidRangeOfTimestampsMsg = '\'From\' cannot be greater than \'To\', you silly sausage!';

    if (filter_var(!empty($_POST['fromTimestamp']), FILTER_SANITIZE_STRING) &&
        filter_var(!empty($_POST['toTimestamp']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

        $from = $_POST['fromTimestamp'];
        $to = $_POST['toTimestamp'];

        // Search events by a range of timestamps only.
        if (filter_var(!empty($_POST['btnTimestamps']), FILTER_SANITIZE_STRING)) {

            foreach(getEventFile() as $event) {

                // Timestamps are extracted from the events and formatted.
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
    if (filter_var(!empty($_POST['combinedQuery']), FILTER_SANITIZE_STRING) &&
        validateSearchForm() === true) {

        showEventsByType();

        showEventsByFieldsUpdated();

        if (!empty(showEventsByRangeOfTimestamps())) {

            if (is_string(showEventsByRangeOfTimestamps())) {

                if (strpos(showEventsByRangeOfTimestamps(), 'sausage') !== false) {
                    
                    // Show error if invalid range of timestamps has been chosen.
                    echo showEventsByRangeOfTimestamps();

                } elseif (strpos(showEventsByRangeOfTimestamps(), 'timestamp') !== false) {
                    
                    // Show error if no range of timestamps has been chosen.
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
