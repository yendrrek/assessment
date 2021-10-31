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
    $timestamp = $from = $to = '';
    $events = [];

    // 'validateSearchForm()' and 'getEventFile()' below are included in separate files.
    if (validateSearchForm() === true) {

        foreach (getEventFile() as $event) {

            if (!empty(getChosenSearchOption())) {

                if (is_array(getChosenSearchOption()) && count(getChosenSearchOption()) === 2) {
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

function showSearchErrors()
{
    $searchError = '';

    if ($_POST['btnEventType'] === 'btnEventType') {
        $searchError = 'No \'Event type\' selected.';

    } elseif ($_POST['btnFieldsUpdated'] === 'btnFieldsUpdated') {
        $searchError = 'No \'Fields updated\' selected.';

    } elseif ($_POST['btnTimestamps'] === 'btnTimestamps') {

        if ($_POST['fromTimestamp'] === 'From timestamp' || $_POST['toTimestamp'] === 'To timestamp') {
            $searchError = 'No timestamp range selected.';

        } elseif ($_POST['fromTimestamp'] > $_POST['toTimestamp']) {
            $searchError = '\'From\' cannot be greater than \'To\', you silly sausage!<br><br>
            Choose a valid range of timestamps.';
        }
    }

    return $searchError;
}

function getEventsByTypeForCombinedSearch()
{
    $eventType = '';
    $eventsByTypeForCombinedSearch = [];

    if (validateSearchForm() === true) {

        foreach (getEventFile() as $event) {

            if (!empty(getChosenSearchOption()[0])) {

                if (getChosenSearchOption()[0] !== 'Event type') {
                    $eventType = getChosenSearchOption()[0];

                    if (strpos($event, $eventType) !== false) {
                        array_push($eventsByTypeForCombinedSearch, $event);
                    }

                } else {
                    array_push($eventsByTypeForCombinedSearch, $event);
                }
            }
        }
    }

    return $eventsByTypeForCombinedSearch;
}

function getEventsByFieldsUpdatedForCombinedSearch()
{
    $fieldUpdated = '';
    $eventsByFieldsUpdatedForCombinedSearch = [];

    if (validateSearchForm() === true) {

        foreach (getEventsByTypeForCombinedSearch() as $event) {

            if (!empty(getChosenSearchOption()[1])) {

                if (getChosenSearchOption()[1] !== 'Fields updated') {
                    $fieldUpdated = getChosenSearchOption()[1];

                    if (strpos($event, $fieldUpdated) !== false) {
                        array_push($eventsByFieldsUpdatedForCombinedSearch, $event);
                    }

                } else {
                    array_push($eventsByFieldsUpdatedForCombinedSearch, $event);
                }
            }
        }
    }

    return $eventsByFieldsUpdatedForCombinedSearch;
}

function getEventsByRangeOfTimestampsForCombinedSearch()
{
    $timestamp = $from = $to = $noEntriesAccordingToCombinedSearch = '';
    $eventsByRangeOfTimestampsForCombinedSearch = [];

    if (validateSearchForm() === true) {

        foreach (getEventsByFieldsUpdatedForCombinedSearch() as $event) {

            if (!empty(getChosenSearchOption()[2]) && !empty(getChosenSearchOption()[3])) {

                if (getChosenSearchOption()[2] !== 'From timestamp' &&
                    getChosenSearchOption()[3] !== 'To timestamp') {
                    $from = getChosenSearchOption()[2];
                    $to = getChosenSearchOption()[3];
                    $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                    if ($timestamp >= $from && $timestamp <= $to) {
                        array_push($eventsByRangeOfTimestampsForCombinedSearch, $event);
                    }
                }
            }
        }
    }

    return $eventsByRangeOfTimestampsForCombinedSearch;
}

function showCombinedSearchErrors()
{
    $combinedSearchError = $from = $to = '';

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
            $combinedSearchError =
            "No entries exist with options '{$_POST['eventType']}' and '{$_POST['fieldsUpdated']}'.";
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

    } elseif (!empty(showResultSummaryForCombinedSearching())) {
        $searchResultSummary = [];
        $searchResultSummary = showResultSummaryForCombinedSearching();
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
    if (!empty($_POST['btnTimestamps'])) {

        if (getQtyOfFoundEvents() < 1 ||
            $_POST['fromTimestamp'] === 'From timestamp' || $_POST['toTimestamp'] === 'To timestamp') {
            $searchResultSummary = null;

        } elseif (getQtyOfFoundEvents() < 2) {
            $searchResultSummary =
            "".getQtyOfFoundEvents()." event found between {$_POST['fromTimestamp']} and
            {$_POST['toTimestamp']}";

        } else {
            $searchResultSummary =
            "".getQtyOfFoundEvents()." events found between {$_POST['fromTimestamp']} and
            {$_POST['toTimestamp']}";
        }
    }

    return $searchResultSummary;
}

function showResultSummaryForCombinedSearching()
{
    $searchResultSummary = [];
    $qtyOfEventsInsertedMsg = $qtyOfEventsUpdatedMsg = $qtyOfEventsDeletedMsg = '';

    if (!empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

        if (!empty(showCombinedSearchResultSummaryForInsertedEventType())) {
            $qtyOfEventsInsertedMsg = showCombinedSearchResultSummaryForInsertedEventType();
        }

        if (!empty(showCombinedSearchResultSummaryForUpdatedEventType())) {
            $qtyOfEventsUpdatedMsg = showCombinedSearchResultSummaryForUpdatedEventType();
        }

        if (!empty(showCombinedSearchResultSummaryForDeletedEventType())) {
            $qtyOfEventsDeletedMsg = showCombinedSearchResultSummaryForDeletedEventType();
        }

        $searchResultSummary = [
            $qtyOfEventsInsertedMsg,
            $qtyOfEventsUpdatedMsg,
            $qtyOfEventsDeletedMsg
        ];

        return $searchResultSummary;
    }
}

function getQtyOfEventsAccordingToCombinedSearch()
{
    $qtyOfEvents = $qtyOfIndividualOccuranceOfInsertedEvent = $qtyOfIndividualOccuranceOfUpdatedEvent =
    $qtyOfIndividualOccuranceOfDeletedEvent =[];
    $qtyOfEventsInserted = $qtyOfEventsUpdated = $qtyOfEventsDeleted = 0;

    foreach (getEventsByRangeOfTimestampsForCombinedSearch() as $event) {

        if (strpos($event, 'INSERTED') !== false) {
            array_push($qtyOfIndividualOccuranceOfInsertedEvent, substr_count($event, 'INSERTED'));

        } elseif (strpos($event, 'UPDATED') !== false) {
            array_push($qtyOfIndividualOccuranceOfUpdatedEvent, substr_count($event, 'UPDATED'));

        } elseif (strpos($event, 'DELETED') !== false) {
            array_push($qtyOfIndividualOccuranceOfDeletedEvent, substr_count($event, 'DELETED'));
        }
    }

    $qtyOfEventsInserted = count($qtyOfIndividualOccuranceOfInsertedEvent);
    $qtyOfEventsUpdated = count($qtyOfIndividualOccuranceOfUpdatedEvent);
    $qtyOfEventsDeleted = count($qtyOfIndividualOccuranceOfDeletedEvent);

    $qtyOfEvents = [
        $qtyOfEventsInserted,
        $qtyOfEventsUpdated,
        $qtyOfEventsDeleted
    ];

    return $qtyOfEvents;
}

function showCombinedSearchResultSummaryForInsertedEventType()
{
    $qtyOfEventsInsertedMsg = $from = $to = '';
    $qtyOfEventsInserted = 0;

    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEventsInserted = getQtyOfEventsAccordingToCombinedSearch()[0];

    if ($qtyOfEventsInserted > 1) {
        $qtyOfEventsInsertedMsg = "{$qtyOfEventsInserted} INSERTED events found between {$from} and {$to}";

    } elseif ($qtyOfEventsInserted > 0) {
        $qtyOfEventsInsertedMsg = "{$qtyOfEventsInserted} INSERTED event found between {$from} and {$to}";
    }

    return $qtyOfEventsInsertedMsg;
}

function showCombinedSearchResultSummaryForUpdatedEventType()
{
    $fieldUpdated = $qtyOfEventsUpdatedMsg = $from = $to = '';
    $qtyOfEventsUpdated = 0;

    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEventsUpdated = getQtyOfEventsAccordingToCombinedSearch()[1];

    if ($qtyOfEventsUpdated > 1) {

        if ($fieldUpdated === 'Fields updated') {
            $qtyOfEventsUpdatedMsg = "{$qtyOfEventsUpdated} UPDATED events found between {$from} and {$to}";

        } else {
            $qtyOfEventsUpdatedMsg =
            "{$qtyOfEventsUpdated} UPDATED events found with updated field '{$fieldUpdated}' between
            {$from} and {$to}";
        }

    } elseif ($qtyOfEventsUpdated > 0) {

        if ($fieldUpdated === 'Fields updated') {
            $qtyOfEventsUpdatedMsg = "{$qtyOfEventsUpdated} UPDATED event found between {$from} and {$to}";

        } else {
            $qtyOfEventsUpdatedMsg =
            "{$qtyOfEventsUpdated} UPDATED event found with updated field '{$fieldUpdated}' between
            {$from} and {$to}";
        }
    }

    return $qtyOfEventsUpdatedMsg;
}

function showCombinedSearchResultSummaryForDeletedEventType()
{
    $fieldUpdated = $qtyOfEventsDeletedMsg = $from = $to = '';
    $qtyOfEventsDeleted = 0;

    $fieldUpdated = getChosenSearchOption()[1];
    $from = getChosenSearchOption()[2];
    $to = getChosenSearchOption()[3];
    $qtyOfEventsDeleted = getQtyOfEventsAccordingToCombinedSearch()[2];

    if ($qtyOfEventsDeleted > 1) {

        if ($fieldUpdated === 'Fields updated') {
            $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED events found between {$from} and {$to}";

        } else {

            if ($fieldUpdated === 'null') {
                $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED events found between {$from} and {$to}";

            } else {
                $qtyOfEventsDeletedMsg =
                "{$qtyOfEventsDeleted} DELETED events found with updated field '{$fieldUpdated}' between
                {$from} and {$to}";
            }
        }

    } elseif ($qtyOfEventsDeleted > 0) {

        if ($fieldUpdated === 'Fields updated') {
            $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED event found between {$from} and {$to}";

        } else {

            if ($fieldUpdated === 'null') {
                $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED event found between {$from} and {$to}";

            } else {
                $qtyOfEventsDeletedMsg =
                "{$qtyOfEventsDeleted} DELETED event found with updated field '{$fieldUpdated}' between
                {$from} and {$to}";
            }
        }
    }

    return $qtyOfEventsDeletedMsg;
}
