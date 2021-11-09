<?php
function sanitiseUserInput($userInput)
{
    is_array($userInput) ?
    $sanitisedUserInput = filter_var_array($userInput, FILTER_SANITIZE_STRING) :
    $sanitisedUserInput = filter_var($userInput, FILTER_SANITIZE_STRING);

    return $sanitisedUserInput;
}

function getChosenSearchOption()
{
    $noOptionIsEmpty = false;
    $noOptionIsEmpty = (
        !empty($_POST['eventType']) &&
        !empty($_POST['fieldsUpdated']) &&
        !empty($_POST['fromTimestamp']) &&
        !empty($_POST['toTimestamp'])
    );

    if (!empty($_POST['btnEventType']) && !empty($_POST['eventType'])) {
        $option = $_POST['eventType'];

    } elseif (!empty($_POST['btnFieldsUpdated']) && !empty($_POST['fieldsUpdated'])) {
        $option = $_POST['fieldsUpdated'];

    } elseif (!empty($_POST['btnTimestamps']) &&
        !empty($_POST['fromTimestamp']) && !empty($_POST['toTimestamp'])) {
        $option = [
            $_POST['fromTimestamp'],
            $_POST['toTimestamp']
        ];

    } elseif (!empty($_POST['combinedQuery']) && $noOptionIsEmpty === true) {
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
    $events = [];
    $searchFormIsValidated = validateSearchForm();
    $eventFile = getEventFile();
    $chosenSearchOption = getChosenSearchOption();

    if ($searchFormIsValidated === true) {

        foreach ($eventFile as $event) {

            if (!empty($chosenSearchOption)) {

                if (is_array($chosenSearchOption) && count($chosenSearchOption) === 2) {
                    $from = $chosenSearchOption[0];
                    $to = $chosenSearchOption[1];
                }

                // Timestamps are extracted from events and formatted.
                $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                if ((!is_array($chosenSearchOption) &&
                    strpos($event, $chosenSearchOption) !== false) ||
                    (($timestamp >= $from && $timestamp <= $to) &&
                    ($from !== 'From timestamp' && $to !== 'To timestamp'))) {
                    array_push($events, $event);
                }
            }
        }

        return $events;
    }
}

function showSearchErrors()
{
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
    $searchFormIsValidated = validateSearchForm();
    $eventFile = getEventFile();
    $eventType = getChosenSearchOption()[0];

    if ($searchFormIsValidated === true) {

        foreach ($eventFile as $event) {

            if (!empty($eventType) && $eventType !== 'Event type') {

                if (strpos($event, $eventType) !== false) {
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
    $searchFormIsValidated = validateSearchForm();
    $eventsByTypeForCombinedSearch = getEventsByTypeForCombinedSearch();
    $fieldUpdated = getChosenSearchOption()[1];

    if ($searchFormIsValidated === true) {

        foreach ($eventsByTypeForCombinedSearch as $event) {

            if ($fieldUpdated !== 'Fields updated') {

                if (!empty($fieldUpdated) && strpos($event, $fieldUpdated) !== false) {
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
    $eventsByRangeOfTimestampsForCombinedSearch = [];
    $searchFormIsValidated = validateSearchForm();
    $eventsByFieldsUpdatedForCombinedSearch = getEventsByFieldsUpdatedForCombinedSearch();
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];

    if ($searchFormIsValidated === true) {

        foreach ($eventsByFieldsUpdatedForCombinedSearch as $event) {

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
    $eventsByFieldsUpdatedForCombinedSearch = getEventsByFieldsUpdatedForCombinedSearch();
    $eventsByRangeOfTimestampsForCombinedSearch = getEventsByRangeOfTimestampsForCombinedSearch();
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];

    if (!empty($_POST['combinedQuery'])) {

        if (!empty($eventsByFieldsUpdatedForCombinedSearch)) {

            if (empty($eventsByRangeOfTimestampsForCombinedSearch)) {

                if ($from === 'From timestamp' || $to === 'To timestamp') {
                    return 'No timestamp range selected.';

                } elseif ($from > $to && $from !== 'From timestamp') {
                    return '\'From\' cannot be greater than \'To\', you nincompoop!<br><br>
                    Choose a valid range of timestamps.';

                } else {
                    return 'No entries exist with chosen options.';
                }
            }

        } else {
            return "No entries exist with options '{$eventType}' and '{$fieldUpdated}'.";
        }
    }
}

function showResultSummary()
{
    $resultSummaryWhenEventTypeSearched = showResultSummaryWhenEventTypeSearched();
    $resultSummaryWhenFieldsUpdatedSearched = showResultSummaryWhenFieldsUpdatedSearched();
    $resultSummaryWhenSearchingByRangeOfTimestamps = showResultSummaryWhenSearchingByRangeOfTimestamps();
    $combinedSearchResultSummary = showCombinedSearchResultSummary();

    if (!empty($resultSummaryWhenEventTypeSearched)) {
        return $resultSummaryWhenEventTypeSearched;

    } elseif (!empty($resultSummaryWhenFieldsUpdatedSearched)) {
        return $resultSummaryWhenFieldsUpdatedSearched;

    } elseif (!empty($resultSummaryWhenSearchingByRangeOfTimestamps)) {
        return $resultSummaryWhenSearchingByRangeOfTimestamps;

    } elseif (!empty($combinedSearchResultSummary)) {
        return $combinedSearchResultSummary;
    }
}

function showResultSummaryWhenEventTypeSearched()
{
    $searchedEvents = getSearchedEvents();
    $chosenSearchOption = getChosenSearchOption();

    if (!empty($_POST['btnEventType'])) {

        if (count($searchedEvents) > 0) {
            return "".count($searchedEvents)." '{$chosenSearchOption}' events found";
        }
    }
}

function showResultSummaryWhenFieldsUpdatedSearched()
{
    $searchedEvents = getSearchedEvents();
    $chosenSearchOption = getChosenSearchOption();

    if (!empty($_POST['btnFieldsUpdated'])) {

        if ($chosenSearchOption === 'null') {
            return "".count($searchedEvents)." events found with no fields updated";

        } elseif (count($searchedEvents) > 0) {
            return "".count($searchedEvents)." events found with updated field '{$chosenSearchOption}' ";
        }
    }
}

function showResultSummaryWhenSearchingByRangeOfTimestamps()
{
    $searchedEvents = getSearchedEvents();
    $from = getChosenSearchOption()[0];
    $to = getChosenSearchOption()[1];

    if (!empty($_POST['btnTimestamps'])) {

        if (count($searchedEvents) > 1) {
            return "".count($searchedEvents)." events found between {$from} and {$to}";

        } elseif (count($searchedEvents) > 0) {
            return "".count($searchedEvents)." event found between {$from} and {$to}";
        }
    }
}

function getQtyOfEventsAccordingToCombinedSearch()
{
    $eventsByRangeOfTimestampsForCombinedSearch = getEventsByRangeOfTimestampsForCombinedSearch();
    $qtyOfIndividualOccuranceOfEvent = [];
    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];

    foreach ($eventsByRangeOfTimestampsForCombinedSearch as $event) {

        strpos($event, $eventType) !== false ?
        array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, $eventType)) :
        array_push($qtyOfIndividualOccuranceOfEvent, substr_count($event, $fieldUpdated));
    }

    return count($qtyOfIndividualOccuranceOfEvent);
}

function setResultSummaryForOneOrManyEvents($qtyOfEvents, $msg)
{
    return $qtyOfEvents > 1 ? $msg : str_replace('events', 'event', $msg);
}

function showCombinedSearchResultSummary()
{
    $eventType = getChosenSearchOption()[0];
    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEvents = getQtyOfEventsAccordingToCombinedSearch();
    $eventsByRangeOfTimestampsForCombinedSearch = getEventsByRangeOfTimestampsForCombinedSearch();

    $msg1 = "{$qtyOfEvents} {$eventType} events found between {$from} and {$to}";
    $msg2 = "{$qtyOfEvents} {$eventType} events found with no fields updated between {$from} and {$to}";
    $msg3 = "{$qtyOfEvents} {$eventType} events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg4 = "{$qtyOfEvents} events found with no fields updated between {$from} and {$to}";
    $msg5 = "{$qtyOfEvents} events found with updated field '{$fieldUpdated}' between {$from} and {$to}";
    $msg6 = "{$qtyOfEvents} events found between {$from} and {$to}";

    if (!empty($eventsByRangeOfTimestampsForCombinedSearch)) {

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
