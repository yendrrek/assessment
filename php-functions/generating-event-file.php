<?php
// Code below populates the input 'event-file.txt' on the server with random event entries
// too see how the searching system works.
// The number of entries can be between 500 and 1000, and they are
// generated upon clicking the button 'Generate event file'.

// This function is used to pupulate 'UPDATED' events.
// They always contain fields updated.
function createRandomFieldsUpdated()
{
    $randomFieldsUpdated = [];
    $fieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate'];
    
    $randomKeys = array_rand($fieldsUpdated, rand(1, count($fieldsUpdated)));

    if (!is_array($randomKeys)) {

        return "[{$fieldsUpdated[$randomKeys]}]";

    } else {

        foreach ($randomKeys as $keys) {
          array_push($randomFieldsUpdated, $fieldsUpdated[$keys]);
        }

        return "[".implode(', ', $randomFieldsUpdated)."]";
    }
}

// This function randomly populates 'DELETED' events.
// They can contain either fields updated or 'null' instead.
function createRandomFieldsUpdatedOrNull()
{
    $randomFieldsUpdatedWithNull = [];
    $fieldsUpdatedWithNull = ['null', createRandomFieldsUpdated()];

    $randomKeys = array_rand($fieldsUpdatedWithNull, rand(1, count($fieldsUpdatedWithNull)));

    if (!is_array($randomKeys)) {

        return 'null';

    } else {

        foreach ($randomKeys as $key) {
            array_push($randomFieldsUpdatedWithNull, $fieldsUpdatedWithNull[$key]);
        }

        $randomFieldsUpdatedWithoutNull = substr(implode(', ', $randomFieldsUpdatedWithNull), 5);

        return $randomFieldsUpdatedWithoutNull;
    }
}   

// 'Placement' and '123' are not a part of the requirement of the assessment scenario, so they are not randomised.
function createRandomEvents()
{
    switch (['INSERTED', 'UPDATED', 'DELETED'][array_rand([0, 1, 2])]) {
        case 'INSERTED':
            $events = [
                'INSERTED',
                'Placement',
                '123',
                'null',
                date("Y-m-d H:i:s.".rand(100, 999)."", rand(0000000001, time()))
            ];
            break;

        case 'UPDATED':
            $events = [
                'UPDATED',
                'Placement',
                '123',
                createRandomFieldsUpdated(),
                date("Y-m-d H:i:s.".rand(100, 999)."", rand(0000000001, time()))
            ];
            break;

        case 'DELETED':
            $events = [
                'DELETED',
                'Placement',
                '123',
                createRandomFieldsUpdatedOrNull(),
                date("Y-m-d H:i:s.".rand(100, 999)."", rand(0000000001, time()))
            ];
            break;
    }

    return  implode(', ', $events) . "\r\n";
}

function populateEventFileWithRandomEntries()
{
    $pathToFile = '../assessment/event-file/event-file.txt';
    $errorMsg = 'Unfortunately, the file into which generated data is written is not accissible at the moment.';
    $msgIfJsDisabled = '<script>alert("New event file has been generated.")</script>';

    if (!empty($_POST['generateEventFile']) && validateSearchForm() === true) {
        $eventFile = fopen($pathToFile, 'w') or die($errorMsg);

        for ($i = 1; $i < rand(500, 1000); $i++) {
            fwrite($eventFile, createRandomEvents());
        }

        fclose($eventFile);

        echo $msgIfJsDisabled;
    }
}

populateEventFileWithRandomEntries();
