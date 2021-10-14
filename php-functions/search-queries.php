<?php
function showEventsByType()
{
    $eventsByType = $eventsByTypeAccordingToCombinedSearch = [];
    $typesOfEvents = ['INSERTED', 'UPDATED', 'DELETED'];
    $qtyOfEventsByType = 0;
    $qtyOfEventsByTypeSummary = $noEventTypeSelectedError = '';

    if (filter_var(!empty($_POST['eventType']), FILTER_SANITIZE_STRING)) {

        // 'getEventFile()' returns the input 'event-file.txt'.
        foreach(getEventFile() as $event) {

            // Show events by their types only.
            if (filter_var(!empty($_POST['btnEventType']), FILTER_SANITIZE_STRING) &&
                validateSearchForm() === true) {

                if (strpos($event, $_POST['eventType']) !== false) {

                    array_push($eventsByType, $event);

                    $qtyOfEventsByType = count($eventsByType);

                    if ($qtyOfEventsByType < 1) {

                        $qtyOfEventsByTypeSummary = null;

                    } else {

                        $qtyOfEventsByTypeSummary = "{$qtyOfEventsByType} {$_POST['eventType']} events found";
                    }

                } else {

                    $noEventTypeSelectedError = 'No \'Event type\' selected.';
                }

            // Perform combined searching.
            } elseif (!empty($_POST['combinedQuery'])) {

                if (in_array($_POST['eventType'], $typesOfEvents)) {

                    if (strpos($event, $_POST['eventType']) !== false) {

                        array_push($eventsByTypeAccordingToCombinedSearch, $event);
                    }

                } else {

                    array_push($eventsByTypeAccordingToCombinedSearch, $event);
                }
            }
        }
    }
    // When performing combined searching the returned array '$eventsByType'
    // is later filtered by the next function 'showEventsByFieldsUpdated()' 
    return [
        $eventsByType,
        $qtyOfEventsByTypeSummary,
        $noEventTypeSelectedError,
        $eventsByTypeAccordingToCombinedSearch
    ];
}

function showEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = $eventsByFieldsUpdatedAccordingToCombinedSearch = [];
    $fieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate', 'null'];
    $qtyOfEventsByFieldsUpdated = 0;
    $qtyOfEventsWithNoFieldsUpdatedSummary = $qtyOfEventsByFieldsUpdatedSummary = $noFieldsUpdatedSelectedError = '';

    if (filter_var(!empty($_POST['fieldsUpdated']), FILTER_SANITIZE_STRING)) {

        // Show events by fields updated only.
        if (filter_var(!empty($_POST['btnFieldsUpdated']), FILTER_SANITIZE_STRING) &&
            validateSearchForm() === true) {

            foreach(getEventFile() as $event) {

                if (strpos($event, $_POST['fieldsUpdated']) !== false) {

                    array_push($eventsByFieldsUpdated, $event);

                    $qtyOfEventsByFieldsUpdated = count($eventsByFieldsUpdated);

                    if ($qtyOfEventsByFieldsUpdated < 1) {

                        $infoAboutQtyOfEventsByFieldsUpdated = null;

                    } elseif ($_POST['fieldsUpdated'] === 'null') {

                        $qtyOfEventsWithNoFieldsUpdatedSummary =
                        "{$qtyOfEventsByFieldsUpdated} events found with no fields updated";

                    } else {

                        $qtyOfEventsByFieldsUpdatedSummary =
                        "{$qtyOfEventsByFieldsUpdated} events found with updated field '{$_POST['fieldsUpdated']}'";
                    }

                } else {

                    $noFieldsUpdatedSelectedError = 'No \'Fields updated\' selected.';
                }
            }
        }

        if (!empty($_POST['combinedQuery'])) {

            if (!empty(showEventsByType()[3])) {

                // Result of search returned earlier, and filtered further when performing combined searching.
                foreach(showEventsByType()[3] as $event) {

                    if (in_array($_POST['fieldsUpdated'], $fieldsUpdated)) {

                        if (strpos($event, $_POST['fieldsUpdated']) !== false) {

                            array_push($eventsByFieldsUpdatedAccordingToCombinedSearch, $event);

                        }

                    }  else {

                        array_push($eventsByFieldsUpdatedAccordingToCombinedSearch, $event);
                    }
                }
            }
        }
    }
    // Array '$eventsByFieldsUpdated' is filtered later by function 'showEventsByRangeOfTimestamps()'.
    return [
        $eventsByFieldsUpdated,
        $qtyOfEventsWithNoFieldsUpdatedSummary,
        $qtyOfEventsByFieldsUpdatedSummary,
        $noFieldsUpdatedSelectedError,
        $eventsByFieldsUpdatedAccordingToCombinedSearch
    ];
}

function showEventsByRangeOfTimestamps()
{
    $eventsByRangeOfTimestamps = $eventsByRangeOfTimestampsAccordingToCombinedSearch = [];
    $from = $to = $noTimestampRangeSelectedError = $invalidRangeOfTimestampsError = $oneEventByRangeOfTimestampsSummary = $qtyOfEventsByRangeOfTimestampsSummary = '';
    $qtyOfEventsByRangeOfTimestamps = 0;

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

                    $noTimestampRangeSelectedError = 'No timestamp range selected.';

                } elseif ($timestamp >= $from && $timestamp <= $to) {

                    array_push($eventsByRangeOfTimestamps, $event);

                    $qtyOfEventsByRangeOfTimestamps = count($eventsByRangeOfTimestamps);

                } elseif ($from > $to) {

                    $invalidRangeOfTimestampsError = '\'From\' cannot be greater than \'To\', you silly sausage!';

                }
            }

            if ($qtyOfEventsByRangeOfTimestamps < 1 || $from === 'From timestamp' || $to === 'To timestamp') {

                $infoAboutQtyOfEventsByRangeOfTimestamps = null;

            } elseif ($qtyOfEventsByRangeOfTimestamps < 2) {

                $oneEventByRangeOfTimestampsSummary =
                    "{$qtyOfEventsByRangeOfTimestamps} event found between {$from} and {$to}";

            } else {

                $qtyOfEventsByRangeOfTimestampsSummary =
                    "{$qtyOfEventsByRangeOfTimestamps} events found between {$from} and {$to}";
            }

        } elseif (!empty($_POST['combinedQuery'])) {

            if (!empty(showEventsByFieldsUpdated()[4])) {

                foreach(showEventsByFieldsUpdated()[4] as $event) {

                    $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                    if ($from === 'From timestamp' || $to === 'To timestamp') {

                        $noTimestampRangeSelectedError = 'No timestamp range selected.';

                    } elseif ($timestamp >= $from && $timestamp <= $to) {

                        array_push($eventsByRangeOfTimestampsAccordingToCombinedSearch, $event);

                    } elseif ($from > $to) {

                        $invalidRangeOfTimestampsError =
                        '\'From\' cannot be greater than \'To\', you silly sausage!';
                    }
                }
            }
        }

        return [
            $eventsByRangeOfTimestamps,
            $oneEventByRangeOfTimestampsSummary,
            $qtyOfEventsByRangeOfTimestampsSummary,
            $noTimestampRangeSelectedError,
            $invalidRangeOfTimestampsError,
            $eventsByRangeOfTimestampsAccordingToCombinedSearch,
            $noEntriesAccordingToCombinedSearch
        ];
    }
}

function showCombinedResult()
{
    $noOptionsSelectedForCombinedSearchError = '';

    if (filter_var(!empty($_POST['combinedQuery']), FILTER_SANITIZE_STRING) &&
        validateSearchForm() === true) {

        if (!empty(showEventsByRangeOfTimestamps()[5])) {

            return showEventsByRangeOfTimestamps()[5];

        } elseif (!empty(showEventsByRangeOfTimestamps()[3])) {

            return $noTimestampRangeSelectedError;

        } elseif (!empty(showEventsByRangeOfTimestamps()[4])) {

            return $invalidRangeOfTimestampsError;

        } elseif (!empty(showEventsByRangeOfTimestamps()[6])) {

            return $noEntriesAccordingToCombinedSearch;

        } else {

            $noEntriesAccordingToCombinedSearch = 'No entries exist with chosen options.';

            return $noEntriesAccordingToCombinedSearch;
        }
    }
}
