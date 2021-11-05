<?php
// Code below populates the input 'event-file.txt' on the server with random event entries
// too see how the searching system works.
// The number of entries can be between 500 and 1000, and they are
// generated upon clicking the button 'Generate event file'.

// This function is used to pupulate 'UPDATED' events.
// They always contain fields updated.
function createRandomFieldsUpdated()
{
    $randomArrayWithFieldsUpdated = [];
    $staticArrayWithFieldsUpdated = ['status', 'companyUrl', 'hoursPerDay', 'overtimeRate'];
    
    $arrayWithRandomKeys = array_rand($staticArrayWithFieldsUpdated, rand(1, count($staticArrayWithFieldsUpdated)));

    // If there is only one element in the array, return it, as it is not an iterable data type.
    if (!is_array($arrayWithRandomKeys)) {
        return "[{$staticArrayWithFieldsUpdated[$arrayWithRandomKeys]}]";

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {
          array_push($randomArrayWithFieldsUpdated, $staticArrayWithFieldsUpdated[$randomKeys]);
        }

        return "[".implode(', ', $randomArrayWithFieldsUpdated)."]";
    }
}

// This function randomly populates 'DELETED' events.
// They can contain either fields updated or 'null' instead.
function createRandomFieldsUpdatedOrNull()
{
    $randomArrayWithFieldsUpdatedOrNull = [];
    $staticArrayWithFieldsUpdatedAndNull = ['null', createRandomFieldsUpdated()];

    $arrayWithRandomKeys =
    array_rand($staticArrayWithFieldsUpdatedAndNull, rand(1, count($staticArrayWithFieldsUpdatedAndNull)));

    if (count($arrayWithRandomKeys) === 1) {

        return 'null';

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {
            array_push($randomArrayWithFieldsUpdatedOrNull, $staticArrayWithFieldsUpdatedAndNull[$randomKeys]);
        }

        return substr(implode(', ', $randomArrayWithFieldsUpdatedOrNull), 5);
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

function createEventFileWithRandomEntries()
{

    if (!empty($_POST['generateEventFile']) && validateSearchForm() === true) {
        $eventFile = fopen('../assessment/event-file/event-file.txt', 'w') or die("Unable to open file!");

        for ($i = 1; $i < rand(500, 1000); $i++) {
            fwrite($eventFile, createRandomEvents());
        }

        fclose($eventFile);

        // For browsers with JavaScript disabled, otherwise there is a custom
        // notification showing upon successful Ajax operation.
        echo '<script>alert("New event file has been generated.")</script>';
    }
}

createEventFileWithRandomEntries();
