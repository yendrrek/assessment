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

function getChosenSearchOption()
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

function getSearchedEvents()
{
    $timestamp = $from = $to = '';

    $events = [];

    // 'validateSearchForm()' and 'getEventFile()' below are included in separate files.
    if (validateSearchForm() === true) {

        foreach (getEventFile() as $event) {

            if (!empty(getChosenSearchOption())) {

                if (is_array(getChosenSearchOption())) {

                    $from = getChosenSearchOption()[0];
                    $to = getChosenSearchOption()[1];
                }

                // Timestamps are extracted from events and formatted.
                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ((!is_array(getChosenSearchOption()) && strpos($event, getChosenSearchOption()) !== false) ||
                    (($timestamp >= $from && $timestamp <= $to) &&
                    ($from !== 'From timestamp' && $to !== 'To timestamp'))) {

                    array_push($events, $event);
                }
            }
        }

        return $events;
    }
}

function getQtyOfFoundEvents()
{
    $qtyOfFoundEvents = 0;

    $qtyOfFoundEvents = count(getSearchedEvents());

    return $qtyOfFoundEvents;
}

function displayResultSummary()
{
    $searchResultSummary = '';

    if (!empty($_POST['btnEventType'])) {

        if (getQtyOfFoundEvents() > 0) {

            $searchResultSummary = "".getQtyOfFoundEvents()." '".getChosenSearchOption()."' events found<br><br>";
        }

    } elseif (!empty($_POST['btnFieldsUpdated'])) {

        if (getQtyOfFoundEvents() > 0) {

            if (getChosenSearchOption() === 'null') {

                $searchResultSummary = "".getQtyOfFoundEvents()." events found with no fields updated<br><br>";

            } else {

                $searchResultSummary =
                    "".getQtyOfFoundEvents()." events found with updated field '".getChosenSearchOption()."' <br><br>";
            }
        }

    } elseif (!empty($_POST['btnTimestamps'])) {

        if (getQtyOfFoundEvents() < 1 ||
            $_POST['fromTimestamp'] === 'From timestamp' || $_POST['toTimestamp'] === 'To timestamp') {

            $searchResultSummary = null;

        } elseif (getQtyOfFoundEvents() < 2) {

            $searchResultSummary =
                "".getQtyOfFoundEvents()." event found between {$_POST['fromTimestamp']} and {$_POST['toTimestamp']}
                <br><br>";

        } else {

            $searchResultSummary =
            "".getQtyOfFoundEvents()." events found between {$_POST['fromTimestamp']} and {$_POST['toTimestamp']}
            <br><br>";
        }
    }

    return $searchResultSummary;
}

function displaySearchErrors()
{
    $searchError = '';

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
