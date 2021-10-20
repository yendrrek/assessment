<?php
function showEventsByType()
{
    $eventsByType = $eventsByTypeAccordingToCombinedSearch = $typesOfEvents = [];

    $typesOfEvents = ['INSERTED', 'UPDATED', 'DELETED'];

    $qtyOfEventsByType = 0;

    $qtyOfEventsByTypeSummary = $noEventTypeSelectedError = $eventByType = '';

    if (!empty($_POST['eventType'])) {

        $eventByType = filter_var($_POST['eventType'], FILTER_SANITIZE_STRING);

        // 'getEventFile()' returns the input 'event-file.txt'.
        foreach(getEventFile() as $event) {

            // Show events by their types only.
            if (!empty($_POST['btnEventType']) && validateSearchForm() === true) {

                if (strpos($event, $eventByType) !== false) {

                    array_push($eventsByType, $event);

                    $qtyOfEventsByType = count($eventsByType);

                    if ($qtyOfEventsByType > 0) {

                        $qtyOfEventsByTypeSummary = "{$qtyOfEventsByType} {$eventByType} events found<br><br>";
                    }

                } else {

                    $noEventTypeSelectedError = 'No \'Event type\' selected.';
                }

            // Perform combined searching.
            } elseif (!empty($_POST['combinedQuery'])) {

                if (in_array($eventByType, $typesOfEvents)) {

                    if (strpos($event, $eventByType) !== false) {

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
        $eventsByType,                          /* [0] */
        $qtyOfEventsByTypeSummary,              /* [1] */
        $noEventTypeSelectedError,              /* [2] */
        $eventsByTypeAccordingToCombinedSearch  /* [3] */
    ];
}

function showEventsByFieldsUpdated()
{
    $eventsByFieldsUpdated = $eventsByFieldsUpdatedAccordingToCombinedSearch = $fieldsUpdated =
    $eventsByTypeAccordingToCombinedSearch = [];

    $fieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate', 'null'];

    $qtyOfEventsByFieldsUpdated = 0;

    $qtyOfEventsWithNoFieldsUpdatedSummary = $qtyOfEventsByFieldsUpdatedSummary = $noFieldsUpdatedSelectedError =
    $fieldUpdated = '';

    if (!empty($_POST['fieldsUpdated'])) {

        $fieldUpdated = filter_var($_POST['fieldsUpdated'], FILTER_SANITIZE_STRING);

        $eventsByTypeAccordingToCombinedSearch = showEventsByType()[3];

        // Show events by fields updated only.
        if (!empty($_POST['btnFieldsUpdated']) && validateSearchForm() === true) {

            foreach(getEventFile() as $event) {

                if (strpos($event, $fieldUpdated) !== false) {

                    array_push($eventsByFieldsUpdated, $event);

                    $qtyOfEventsByFieldsUpdated = count($eventsByFieldsUpdated);

                    if ($fieldUpdated === 'null') {

                        $qtyOfEventsWithNoFieldsUpdatedSummary =
                        "{$qtyOfEventsByFieldsUpdated} events found with no fields updated<br><br>";

                    } else {

                        $qtyOfEventsByFieldsUpdatedSummary =
                        "{$qtyOfEventsByFieldsUpdated} events found with updated field '{$fieldUpdated}'<br><br>";
                    }

                } else {

                    $noFieldsUpdatedSelectedError = 'No \'Fields updated\' selected.';
                }
            }
        }

        if (!empty($_POST['combinedQuery'])) {

            if (!empty($eventsByTypeAccordingToCombinedSearch)) {

                // Result of search returned earlier, and filtered further when performing combined searching.
                foreach($eventsByTypeAccordingToCombinedSearch as $event) {

                    if (in_array($fieldUpdated, $fieldsUpdated)) {

                        if (strpos($event, $fieldUpdated) !== false) {

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
        $eventsByFieldsUpdated,                          /* [0] */
        $qtyOfEventsWithNoFieldsUpdatedSummary,          /* [1] */
        $qtyOfEventsByFieldsUpdatedSummary,              /* [2] */
        $noFieldsUpdatedSelectedError,                   /* [3] */
        $eventsByFieldsUpdatedAccordingToCombinedSearch, /* [4] */
        $fieldUpdated                                    /* [5] */
    ];
}

function showEventsByRangeOfTimestamps()
{
    $eventsByRangeOfTimestamps = $eventsByRangeOfTimestampsAccordingToCombinedSearch =
    $eventsByFieldsUpdatedAccordingToCombinedSearch = [];

    $from = $to = $noTimestampRangeSelectedError = $invalidRangeOfTimestampsError =
    $oneEventByRangeOfTimestampsSummary = $qtyOfEventsByRangeOfTimestampsSummary = $noEntriesAccordingToCombinedSearch =
    $fieldUpdated = '';

    $qtyOfEventsByRangeOfTimestamps = 0;

    if (!empty($_POST['fromTimestamp']) && !empty($_POST['toTimestamp']) && validateSearchForm() === true) {

        $from = filter_var($_POST['fromTimestamp'], FILTER_SANITIZE_STRING);

        $to = filter_var($_POST['toTimestamp'], FILTER_SANITIZE_STRING);

        $fieldUpdated = filter_var($_POST['fieldsUpdated'], FILTER_SANITIZE_STRING);

        $eventsByFieldsUpdatedAccordingToCombinedSearch = showEventsByFieldsUpdated()[4];

        // Search events by a range of timestamps only.
        if (!empty($_POST['btnTimestamps'])) {

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
                    "{$qtyOfEventsByRangeOfTimestamps} event found between {$from} and {$to}<br><br>";

            } else {

                $qtyOfEventsByRangeOfTimestampsSummary =
                    "{$qtyOfEventsByRangeOfTimestamps} events found between {$from} and {$to}<br><br>";
            }

        } elseif (!empty($_POST['combinedQuery'])) {

            if (!empty($eventsByFieldsUpdatedAccordingToCombinedSearch)) {

                foreach($eventsByFieldsUpdatedAccordingToCombinedSearch as $event) {

                    $timestamp = date_format(date_create(substr($event, -25)), 'Y-m-d H:i:s.v');

                    if ($from === 'From timestamp' || $to === 'To timestamp') {

                        $noTimestampRangeSelectedError = 'No timestamp range selected.';

                    } elseif ($timestamp >= $from && $timestamp <= $to) {

                        array_push($eventsByRangeOfTimestampsAccordingToCombinedSearch, $event);

                    } elseif ($from > $to) {

                        $invalidRangeOfTimestampsError = '\'From\' cannot be greater than \'To\', you silly sausage!';
                    }
                }

                if (empty($eventsByRangeOfTimestampsAccordingToCombinedSearch) &&
                    $from <= $to &&
                    $from !== 'From timestamp' &&
                    $to !== 'To timestamp') {

                    $noEntriesAccordingToCombinedSearch = 'No entries exist with chosen options.';
                }

            } else {

                if ($from > $to) {

                    $invalidRangeOfTimestampsError = '\'From\' cannot be greater than \'To\', you silly sausage!';

                } else {

                    $noEntriesAccordingToCombinedSearch = 'No entries exist with chosen options.';
                }
            }
        }

        return [
            $eventsByRangeOfTimestamps,                           /* [0] */
            $oneEventByRangeOfTimestampsSummary,                  /* [1] */
            $qtyOfEventsByRangeOfTimestampsSummary,               /* [2] */
            $noTimestampRangeSelectedError,                       /* [3] */
            $invalidRangeOfTimestampsError,                       /* [4] */
            $eventsByRangeOfTimestampsAccordingToCombinedSearch,  /* [5] */
            $noEntriesAccordingToCombinedSearch,                  /* [6] */
            $from,                                                /* [7] */
            $to                                                   /* [8] */
        ];
    }
}

function showResultSummaryForCombinedSearch()
{
    $eventsByCombinedSearch = $typesOfEvents = $fieldsUpdated = $qtyOfIndividualOccuranceOfInsertedEvent =
        $qtyOfIndividualOccuranceOfUpdatedEvent = $qtyOfIndividualOccuranceOfDeletedEvent = [];
    $qtyOfEventsInserted = $qtyOfEventsUpdated = $qtyOfEventsDeleted = 0;
    $qtyOfEventsInsertedMsg = $qtyOfEventsUpdatedMsg = $qtyOfEventsDeletedMsg = '';

    $eventsByCombinedSearch = showEventsByRangeOfTimestamps()[5];
    $typesOfEvents = ['INSERTED', 'UPDATED', 'DELETED'];
    $fieldUpdated = showEventsByFieldsUpdated()[5];
    $from = showEventsByRangeOfTimestamps()[7];
    $to = showEventsByRangeOfTimestamps()[8];

    if (!empty($eventsByCombinedSearch)) {

        foreach ($eventsByCombinedSearch as $event) {

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

        if ($qtyOfEventsInserted > 1) {

            $qtyOfEventsInsertedMsg = "{$qtyOfEventsInserted} INSERTED events found between {$from} and {$to}<br><br>";

        } else {

            $qtyOfEventsInsertedMsg = "{$qtyOfEventsInserted} INSERTED event found between {$from} and {$to}<br><br>";
        }

        if ($qtyOfEventsUpdated > 1) {

            if ($fieldUpdated === 'Fields updated') {

                $qtyOfEventsUpdatedMsg = "{$qtyOfEventsUpdated} UPDATED events found between {$from} and {$to}<br><br>";

            } else {

                $qtyOfEventsUpdatedMsg =
                    "{$qtyOfEventsUpdated} UPDATED events found with updated field '{$fieldUpdated}' between {$from}
                    and {$to}<br><br>";
            }

        } elseif ($qtyOfEventsUpdated > 0) {

            if ($fieldUpdated === 'Fields updated') {

                $qtyOfEventsUpdatedMsg = "{$qtyOfEventsUpdated} UPDATED event found between {$from} and {$to}<br><br>";

            } else {

                $qtyOfEventsUpdatedMsg =
                    "{$qtyOfEventsUpdated} UPDATED event found with updated field '{$fieldUpdated}' between {$from}
                    and {$to}<br><br>";
            }
        }

        if ($qtyOfEventsDeleted < 1) {

            $qtyOfEventsDeletedMsg = null;

        } elseif ($qtyOfEventsDeleted < 2) {

            if ($fieldUpdated === 'Fields updated') {

                $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED event found between {$from} and {$to}<br><br>";

            } else {

                if ($fieldUpdated === 'null') {

                    $qtyOfEventsDeletedMsg =
                        "{$qtyOfEventsDeleted} DELETED event found between {$from} and {$to}<br><br>";

                } else {

                    $qtyOfEventsDeletedMsg =
                        "{$qtyOfEventsDeleted} DELETED event found with updated field '{$fieldUpdated}' between {$from}
                        and {$to}<br><br>";
                }
            }

        } else {

            if ($fieldUpdated === 'Fields updated') {

                $qtyOfEventsDeletedMsg = "{$qtyOfEventsDeleted} DELETED events found between {$from} and {$to}<br><br>";

            } else {

                if ($fieldUpdated === 'null') {

                    $qtyOfEventsDeletedMsg =
                        "{$qtyOfEventsDeleted} DELETED events found between {$from} and {$to}<br><br>";

                } else {

                    $qtyOfEventsDeletedMsg =
                        "{$qtyOfEventsDeleted} DELETED events found with updated field '{$fieldUpdated}' between {$from}
                        and {$to}<br><br>";
                }
            }
        }

        echo $qtyOfEventsInsertedMsg;
        echo $qtyOfEventsUpdatedMsg;
        echo $qtyOfEventsDeletedMsg;
    }
}
