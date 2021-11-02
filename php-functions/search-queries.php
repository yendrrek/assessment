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
    // Variable '$option' can be either a string or array;

    $noOptionIsEmpty = false;

    $noOptionIsEmpty = (
        !empty($_POST['eventType']) &&
        !empty($_POST['fieldsUpdated']) &&
        !empty($_POST['fromTimestamp']) &&
        !empty($_POST['toTimestamp'])
    );

    if (!empty($_POST['btnEventType']) && !empty($_POST['eventType'])) {
        $option = '';
        $option = $_POST['eventType'];

    } elseif (!empty($_POST['btnFieldsUpdated']) && !empty($_POST['fieldsUpdated'])) {
        $option = '';
        $option = $_POST['fieldsUpdated'];

    } elseif (!empty($_POST['btnTimestamps']) &&
        !empty($_POST['fromTimestamp']) && !empty($_POST['toTimestamp'])) {
        $option = [];
        $option = [
            $_POST['fromTimestamp'],
            $_POST['toTimestamp']
        ];

    } elseif (!empty($_POST['combinedQuery']) && $noOptionIsEmpty === true) {
        $option = [];
        $option = [
            $_POST['eventType'],
            $_POST['fieldsUpdated'],
            $_POST['fromTimestamp'],
            $_POST['toTimestamp']
        ];
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

            if (is_array(getChosenSearchOption()) && count(getChosenSearchOption()) === 2) {
                $from = getChosenSearchOption()[0];
                $to = getChosenSearchOption()[1];
            }

            // Timestamps are extracted from events and formatted.
            $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

            if ((!is_array(getChosenSearchOption()) &&
                strpos($event, getChosenSearchOption()) !== false) ||
                (($timestamp >= $from && $timestamp <= $to) &&
                ($from !== 'From timestamp' && $to !== 'To timestamp'))) {
                array_push($events, $event);
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

function showSearchErrors()
{
    $searchError = $from = $to = '';

    $from = getChosenSearchOption()[0];
    $to = getChosenSearchOption()[1];

    if (!empty($_POST['btnEventType'])) {
        $searchError = 'No \'Event type\' selected.';

    } elseif (!empty($_POST['btnFieldsUpdated'])) {
        $searchError = 'No \'Fields updated\' selected.';

    } elseif (!empty($_POST['btnTimestamps'])) {

        if ( $from === 'From timestamp' || $to === 'To timestamp') {
            $searchError = 'No timestamp range selected.';

        } elseif ($from > $to) {
            $searchError = '\'From\' cannot be greater than \'To\', you silly sausage!<br><br>
            Choose a valid range of timestamps.';
        }
    }

    return $searchError;
}

function getEventsByTypeForCombinedSearch()
{
    $eventsByTypeForCombinedSearch = [];

    if (validateSearchForm() === true) {

        foreach (getEventFile() as $event) {

            if (getChosenSearchOption()[0] !== 'Event type') {

                if (strpos($event, getChosenSearchOption()[0]) !== false) {
                    array_push($eventsByTypeForCombinedSearch, $event);
                }

            } else {
                array_push($eventsByTypeForCombinedSearch, $event);
            }
        }
    }

    return $eventsByTypeForCombinedSearch;
}

function getEventsByFieldsUpdatedForCombinedSearch()
{
    $eventsByFieldsUpdatedForCombinedSearch = [];

    if (validateSearchForm() === true) {

        foreach (getEventsByTypeForCombinedSearch() as $event) {

            if (getChosenSearchOption()[1] !== 'Fields updated') {

                if (strpos($event, getChosenSearchOption()[1]) !== false) {
                    array_push($eventsByFieldsUpdatedForCombinedSearch, $event);
                }

            } else {
                array_push($eventsByFieldsUpdatedForCombinedSearch, $event);
            }
        }
    }

    return $eventsByFieldsUpdatedForCombinedSearch;
}

function getEventsByRangeOfTimestampsForCombinedSearch()
{
    $timestamp = $from = $to = '';
    $eventsByRangeOfTimestampsForCombinedSearch = [];

    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];

    if (validateSearchForm() === true) {

        foreach (getEventsByFieldsUpdatedForCombinedSearch() as $event) {

            if ($from !== 'From timestamp' && $to !== 'To timestamp') {
                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ($timestamp >= $from && $timestamp <= $to) {
                    array_push($eventsByRangeOfTimestampsForCombinedSearch, $event);
                }
            }
        }
    }

    return $eventsByRangeOfTimestampsForCombinedSearch;
}

function showCombinedSearchErrors()
{
    $combinedSearchError = $from = $to = $eventType = $fieldUpdated = '';

    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];

    if (!empty($_POST['combinedQuery'])) {

        if (!empty(getEventsByFieldsUpdatedForCombinedSearch())) {

            if (empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

                if ($from === 'From timestamp' || $to === 'To timestamp') {
                    $combinedSearchError = 'No timestamp range selected.';

                } else {
                    $combinedSearchError = 'No entries exist with chosen options.';
                }
            }

            if ($from > $to && $from !== 'From timestamp') {
                $combinedSearchError = '\'From\' cannot be greater than \'To\', you nincompoop!<br><br>
                Choose a valid range of timestamps.';
            }

        } else {
            $combinedSearchError = "No entries exist with options '{$eventType}' and '{$fieldUpdated}'.";
        }
    }

    return $combinedSearchError;
}

function showResultSummary()
{
    $searchResultSummary = '';

    if (!empty(showResultSummaryWhenEventTypeSearched())) {
        $searchResultSummary = showResultSummaryWhenEventTypeSearched();

    } elseif (!empty(showResultSummaryWhenFieldsUpdatedSearched())) {
        $searchResultSummary = showResultSummaryWhenFieldsUpdatedSearched();

    } elseif (!empty(showResultSummaryWhenSearchingByRangeOfTimestamps())) {
        $searchResultSummary = showResultSummaryWhenSearchingByRangeOfTimestamps();

    } elseif (!empty(showCombinedSearchResultSummary())) {
        $searchResultSummary = showCombinedSearchResultSummary();
    }

    return $searchResultSummary;
}

function showResultSummaryWhenEventTypeSearched()
{
    $searchResultSummary = '';

    if (!empty($_POST['btnEventType'])) {

        if (getQtyOfFoundEvents() > 0) {
            $searchResultSummary = "".getQtyOfFoundEvents()." '".getChosenSearchOption()."' events found";
        }
    }

    return $searchResultSummary;
}

function showResultSummaryWhenFieldsUpdatedSearched()
{
    $searchResultSummary = '';

    if (!empty($_POST['btnFieldsUpdated'])) {

        if (getQtyOfFoundEvents() > 0) {

            if (getChosenSearchOption() === 'null') {
                $searchResultSummary = "".getQtyOfFoundEvents()." events found with no fields updated";

            } else {
                $searchResultSummary =
                "".getQtyOfFoundEvents()." events found with updated field '".getChosenSearchOption()."' ";
            }
        }
    }

    return $searchResultSummary;
}

function showResultSummaryWhenSearchingByRangeOfTimestamps()
{
    $from = $to = $searchResultSummary = '';

    $from = getChosenSearchOption()[0];
    $to = getChosenSearchOption()[1];

    if (!empty($_POST['btnTimestamps'])) {

        if (getQtyOfFoundEvents() < 1 ||
            $from === 'From timestamp' || $to === 'To timestamp') {
            $searchResultSummary = null;

        } elseif (getQtyOfFoundEvents() < 2) {
            $searchResultSummary = "".getQtyOfFoundEvents()." event found between {$from} and {$to}";

        } else {
            $searchResultSummary = "".getQtyOfFoundEvents()." events found between {$from} and {$to}";
        }
    }

    return $searchResultSummary;
}

function getQtyOfEventsAccordingToCombinedSearch()
{
    $qtyOfEvents = 0;
    $qtyOfEvents = $qtyOfIndividualOccuranceOfEvent = [];

    foreach (getEventsByRangeOfTimestampsForCombinedSearch() as $event) {

        if (strpos($event, getChosenSearchOption()[0]) !== false) {
            array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, getChosenSearchOption()[0]));

        } else {
            array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, getChosenSearchOption()[1]));
        }
    }

    $qtyOfEvents = count($qtyOfIndividualOccuranceOfEvent);

    return $qtyOfEvents;
}

function setMsgForOneOrManyEvents($qtyOfEvents, $msg)
{
    return $qtyOfEvents > 1 ? $msg : str_replace('events', 'event', $msg);
}

function showCombinedSearchResultSummary()
{
    $eventType = $fieldUpdated = $qtyOfEventsMsg = $msg1 = $msg2 = $msg3 = $msg4 = $msg5 = $msg6 = $from = $to = '';
    $qtyOfEvents = 0;

    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEvents = getQtyOfEventsAccordingToCombinedSearch();

    $msg1 = "{$qtyOfEvents} ".$eventType." events found between {$from} and {$to}";
    $msg2 = "{$qtyOfEvents} ".$eventType." events found with no fields updated between {$from} and {$to}";
    $msg3 = "{$qtyOfEvents} ".$eventType." events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg4 = "{$qtyOfEvents} events found with no fields updated between {$from} and {$to}";
    $msg5 = "{$qtyOfEvents} events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg6 = "{$qtyOfEvents} events found between {$from} and {$to}";

    if (!empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

        if ($eventType !== 'Event type') {

            if ($fieldUpdated === 'Fields updated' || $eventType === 'INSERTED') {
                $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg1);

            } elseif ($eventType === 'DELETED' && $fieldUpdated === 'null') {
                $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg2);

            } elseif ($eventType === 'UPDATED' || $eventType === 'DELETED') {
                $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg3);
            }

        } elseif ($fieldUpdated !== 'Fields updated') {

            if ($fieldUpdated === 'null') {
                $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg4);

            } else {
                $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg5);
            }

        } else {
            $qtyOfEventsMsg = setMsgForOneOrManyEvents($qtyOfEvents, $msg6);
        }

        return $qtyOfEventsMsg;

    } else {

        return false;
    }
}
