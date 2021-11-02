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

function showSearchErrors()
{
    $from = $to = '';

    $from = getChosenSearchOption()[0];
    $to = getChosenSearchOption()[1];

    if (!empty($_POST['btnEventType'])) {
        return 'No \'Event type\' selected.';

    } elseif (!empty($_POST['btnFieldsUpdated'])) {
        return 'No \'Fields updated\' selected.';

    } elseif (!empty($_POST['btnTimestamps'])) {

        if ( $from === 'From timestamp' || $to === 'To timestamp') {
            return 'No timestamp range selected.';

        } elseif ($from > $to) {
            return '\'From\' cannot be greater than \'To\', you silly sausage!<br><br>
            Choose a valid range of timestamps.';
        }
    }
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
    $eventType = $fieldUpdated = $from = $to = '';

    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];

    if (!empty($_POST['combinedQuery'])) {

        if (!empty(getEventsByFieldsUpdatedForCombinedSearch())) {

            if (empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

                if ($from === 'From timestamp' || $to === 'To timestamp') {
                    return 'No timestamp range selected.';

                } else {
                    return 'No entries exist with chosen options.';
                }
            }

            if ($from > $to && $from !== 'From timestamp') {
                return '\'From\' cannot be greater than \'To\', you nincompoop!<br><br>
                Choose a valid range of timestamps.';
            }

        } else {
            return "No entries exist with options '{$eventType}' and '{$fieldUpdated}'.";
        }
    }
}

function showResultSummary()
{
    if (!empty(showResultSummaryWhenEventTypeSearched())) {
        return showResultSummaryWhenEventTypeSearched();

    } elseif (!empty(showResultSummaryWhenFieldsUpdatedSearched())) {
        return showResultSummaryWhenFieldsUpdatedSearched();

    } elseif (!empty(showResultSummaryWhenSearchingByRangeOfTimestamps())) {
        return showResultSummaryWhenSearchingByRangeOfTimestamps();

    } elseif (!empty(showCombinedSearchResultSummary())) {
        return showCombinedSearchResultSummary();
    }
}

function showResultSummaryWhenEventTypeSearched()
{
    if (!empty($_POST['btnEventType'])) {

        if (count(getSearchedEvents()) > 0) {
            return "".count(getSearchedEvents())." '".getChosenSearchOption()."' events found";
        }
    }
}

function showResultSummaryWhenFieldsUpdatedSearched()
{
    if (!empty($_POST['btnFieldsUpdated'])) {

        if (getChosenSearchOption() === 'null') {
            return "".count(getSearchedEvents())." events found with no fields updated";

        } elseif (count(getSearchedEvents()) > 0) {
            return "".count(getSearchedEvents())." events found with updated field '".getChosenSearchOption()."' ";
        }
    }
}

function showResultSummaryWhenSearchingByRangeOfTimestamps()
{
    $from = $to = '';

    $from = getChosenSearchOption()[0];
    $to = getChosenSearchOption()[1];

    if (!empty($_POST['btnTimestamps'])) {

        if (count(getSearchedEvents()) > 1) {
            return "".count(getSearchedEvents())." events found between {$from} and {$to}";

        } elseif (count(getSearchedEvents()) > 0) {
            return "".count(getSearchedEvents())." event found between {$from} and {$to}";
        }
    }
}

function getQtyOfEventsAccordingToCombinedSearch()
{
    $qtyOfIndividualOccuranceOfEvent = [];

    foreach (getEventsByRangeOfTimestampsForCombinedSearch() as $event) {

        if (strpos($event, getChosenSearchOption()[0]) !== false) {
            array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, getChosenSearchOption()[0]));

        } else {
            array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, getChosenSearchOption()[1]));
        }
    }

    return count($qtyOfIndividualOccuranceOfEvent);
}

function setMsgForOneOrManyEvents($qtyOfEvents, $msg)
{
    return $qtyOfEvents > 1 ? $msg : str_replace('events', 'event', $msg);
}

function showCombinedSearchResultSummary()
{
    $eventType = $fieldUpdated = $msg1 = $msg2 = $msg3 = $msg4 = $msg5 = $msg6 = $from = $to = '';
    $qtyOfEvents = 0;

    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEvents = getQtyOfEventsAccordingToCombinedSearch();

    $msg1 = "{$qtyOfEvents} {$eventType} events found between {$from} and {$to}";
    $msg2 = "{$qtyOfEvents} {$eventType} events found with no fields updated between {$from} and {$to}";
    $msg3 = "{$qtyOfEvents} {$eventType} events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg4 = "{$qtyOfEvents} events found with no fields updated between {$from} and {$to}";
    $msg5 = "{$qtyOfEvents} events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg6 = "{$qtyOfEvents} events found between {$from} and {$to}";

    if (!empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

        if ($eventType !== 'Event type') {

            if ($eventType === 'INSERTED' || $fieldUpdated === 'Fields updated') {
                return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg1);

            } elseif ($eventType === 'DELETED' && $fieldUpdated === 'null') {
                return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg2);

            } elseif ($eventType === 'UPDATED' || $eventType === 'DELETED') {
                return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg3);
            }

        } elseif ($fieldUpdated !== 'Fields updated') {

            if ($fieldUpdated === 'null') {
                return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg4);

            } else {
                return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg5);
            }

        } else {
            return setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg6);
        }
    }
}
