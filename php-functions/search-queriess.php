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

function getSearchOption()
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
    $searchResultSummary = $searchError = $timestamp = $from = $to = '';

    $qtyOfEvents = 0;

    $events = [];

    //'getEventFile()' and 'validateSearchForm()' below are included in a separate files.
    foreach (getEventFile() as $event) {

        if (validateSearchForm() === true) {

            // Can be a string or array if storing values with 'From timestamp' and 'To timestamp'.
            $searchOption = getSearchOption();

            if (is_array($searchOption)) {

                $from = $searchOption[0];
                $to = $searchOption[1];
            }

            // Timestamps are extracted from events and formatted.
            $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

            if ((!is_array($searchOption) && strpos($event, $searchOption) !== false) ||
                (($timestamp >= $from && $timestamp <= $to) &&
                ($from !== 'From timestamp' && $to !== 'To timestamp'))) {

                array_push($events, $event);

                $qtyOfEvents = count($events);

                if ($qtyOfEvents > 0) {

                    if (!empty($_POST['btnEventType'])) {

                        $searchResultSummary = "{$qtyOfEvents} '{$searchOption}' events found<br><br>";

                    } elseif (!empty($_POST['btnFieldsUpdated'])) {

                        if ($searchOption === 'null') {

                            $searchResultSummary = "{$qtyOfEvents} events found with no fields updated<br><br>";

                        } else {

                            $searchResultSummary = "{$qtyOfEvents} events found with updated field '{$searchOption}'<br><br>";
                        }

                    } elseif (!empty($_POST['btnTimestamps'])) {

                        if ($qtyOfEvents < 1 || $from === 'From timestamp' || $to === 'To timestamp') {

                            $searchResultSummary = null;

                        } elseif ($qtyOfEvents < 2) {

                            $searchResultSummary = "{$qtyOfEvents} event found between {$from} and {$to}<br><br>";

                        } else {

                            $searchResultSummary = "{$qtyOfEvents} events found between {$from} and {$to}<br><br>";
                        }
                    }
                }

            } else {

                displaySearchErrors($searchError);
            }
        }
    }

    return [
        $events,
        $searchResultSummary,
    ];
}

function displaySearchErrors($searchError)
{
    if ($_POST['btnEventType'] === 'btnEventType') {

        $searchError = 'No \'Event type\' selected.';

    } elseif ($_POST['btnFieldsUpdated'] === 'btnFieldsUpdated') {

        $searchError = 'No \'Fields updated\' selected.';

    } elseif ($_POST['fromTimestamp'] === 'From timestamp' || $_POST['toTimestamp'] === 'To timestamp') {

        $searchError = 'No timestamp range selected.';

    } elseif ($_POST['fromTimestamp'] > $_POST['toTimestamp']) {

        $searchError = '\'From\' cannot be greater than \'To\', you silly sausage!';
    }

    return $searchError;
}
