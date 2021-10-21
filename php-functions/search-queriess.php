<?php
function sanitiseUserInput($userInput)
{
    if (is_array($userInput)) {

        $sanitisedUserInput = [];

        $sanitisedUserInput = filter_var_array($userInput, FILTER_SANITIZE_STRING);

    } else {

        $sanitisedUserInput = '';

        $sanitisedUserInput = filter_var($userInput, FILTER_SANITIZE_STRING);
    }

    return $sanitisedUserInput;
}

function getSingleSearchOption()
{
    $option = '';

    if (!empty($_POST['btnEventType']) && !empty($_POST['eventType'])) {

        $option = $_POST['eventType'];

    } elseif (!empty($_POST['btnFieldsUpdated']) && !empty($_POST['fieldsUpdated'])) {

        $option = $_POST['fieldsUpdated'];

    } elseif (!empty($_POST['btnTimestamps']) && !empty($_POST['fromTimestamp']) && !empty($_POST['toTimestamp'])) {

        $option = [];

        $option = [$_POST['fromTimestamp'], $_POST['toTimestamp']];
    }

    return sanitiseUserInput($option);
}

function getSearchResults()
{
    $qtyOfEventsSummary = $noEventTypeSelectedError = $timestamp = $from = $to = '';

    $qtyOfEvents = 0;

    $events = [];

    if (is_array($singleSearchOption)) {

        $singleSearchOption = [];

    } else {

        $singleSearchOption = '';
    }

    $singleSearchOption = getSingleSearchOption();

    foreach (getEventFile() as $event) {

        if (validateSearchForm() === true) {

            if (is_string($singleSearchOption)) {

                if (strpos($event, $singleSearchOption) !== false) {

                    array_push($events, $event);

                    $qtyOfEvents = count($events);

                    if ($qtyOfEvents > 0) {

                        if (!empty($_POST['btnEventType'])) {

                            $qtyOfEventsSummary = "{$qtyOfEvents} '{$singleSearchOption}' events found<br><br>";

                        } elseif (!empty($_POST['btnFieldsUpdated'])) {

                            if ($singleSearchOption === 'null') {

                                $qtyOfEventsSummary = "{$qtyOfEvents} events found with no fields updated<br><br>";

                            } else {

                                $qtyOfEventsSummary = "{$qtyOfEvents} events found with updated field '{$singleSearchOption}'<br><br>";
                            }
                        }
                    }

                } elseif ($_POST['btnEventType'] === 'btnEventType') {

                    $noEventTypeSelectedError = 'No \'Event type\' selected.';

                } elseif ($_POST['btnFieldsUpdated'] === 'btnFieldsUpdated') {

                    $noEventTypeSelectedError = 'No \'Fields updated\' selected.';
                }

            } else {

                $from = $singleSearchOption[0];
                $to = $singleSearchOption[1];

                // Timestamps are extracted from the events and formatted.
                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ($from === 'From timestamp' || $to === 'To timestamp') {

                    $noEventTypeSelectedError = 'No timestamp range selected.';

                } elseif ($timestamp >= $from && $timestamp <= $to) {

                    array_push($events, $event);

                    $qtyOfEvents = count($events);

                } elseif ($from > $to) {

                    $noEventTypeSelectedError = '\'From\' cannot be greater than \'To\', you silly sausage!';
                }

                if ($qtyOfEvents < 1 || $from === 'From timestamp' || $to === 'To timestamp') {

                    $qtyOfEventsSummary = null;

                } elseif ($qtyOfEvents < 2) {

                    $qtyOfEventsSummary = "{$qtyOfEvents} event found between {$from} and {$to}<br><br>";

                } else {

                    $qtyOfEventsSummary = "{$qtyOfEvents} events found between {$from} and {$to}<br><br>";
                }

            }
        }
    }

    return [
        $events,
        $qtyOfEventsSummary,
        $noEventTypeSelectedError
    ];
}

