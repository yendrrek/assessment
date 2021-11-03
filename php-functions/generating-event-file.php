<?php
// Every time 'Generate event file' button is used,
// generate random entries in the 'event-file.txt' for testing purposes.
// The amount of entries can be a random number between 500 and 1000.
function createRandomEventType()
{
    $typesOfEvents = [
      'INSERTED',
      'UPDATED',
      'DELETED'
    ];

    $randomKeys = array_rand($typesOfEvents);

    return $typesOfEvents[$randomKeys];
}

function createRandomFieldsUpdated()
{
    $randomArrayWithFieldsUpdated = [];
    $staticArrayWithFieldsUpdated = [
      'status',
      'companyUrl',
      'hoursPerDay',
      'overtimeRate'
    ];
    
    $arrayWithRandomKeys = array_rand($staticArrayWithFieldsUpdated, rand(1, count($staticArrayWithFieldsUpdated)));

    // If there is only one element in the array, return it, as it is not an iterable data type.
    if (count($arrayWithRandomKeys) === 1) {
        $stringWithRandomFieldsUpdated = '[' . $staticArrayWithFieldsUpdated[$arrayWithRandomKeys] . ']';

        return $stringWithRandomFieldsUpdated;

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {
          array_push($randomArrayWithFieldsUpdated, $staticArrayWithFieldsUpdated[$randomKeys]);
        }
    }

    $stringWithRandomFieldsUpdated = '[' . implode(', ', $randomArrayWithFieldsUpdated) . ']';

    return $stringWithRandomFieldsUpdated;
}

// Instead of fields updated some entries contain 'null'.
// This function randomly populates event entries with either fields updated or 'null'. 
function createRandomFieldsUpdatedOrNull()
{
    $randomArrayWithFieldsUpdatedOrNull = [];
    $staticArrayWithFieldsUpdatedAndNull = [
      'null',
      createRandomFieldsUpdated()
    ];

    $arrayWithRandomKeys =
    array_rand($staticArrayWithFieldsUpdatedAndNull, rand(1, count($staticArrayWithFieldsUpdatedAndNull)));

    // If there is only one element in the array, it must be 'null'.
    if ($arrayWithRandomKeys < 2) {
        $stringNull = 'null';

        return $stringNull;

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {
            array_push($randomArrayWithFieldsUpdatedOrNull, $staticArrayWithFieldsUpdatedAndNull[$randomKeys]);
        }
    }
    
    // Get rid of 'null' to obtain only fields updated.
    $stringStillWithNull = implode(', ', $randomArrayWithFieldsUpdatedOrNull);

    $stringAlreadyWithoutNull = substr($stringStillWithNull, 5);

    return $stringAlreadyWithoutNull;
}   

function createRandomTimeStamp()
{
    $integer = rand(0000000001, time());

    $milliseconds = rand(100, 999);

    return date("Y-m-d H:i:s.{$milliseconds}", $integer);
}

// This function creates strings with random event entries.
// 'Placement' and '123' are not a part of the requirement of the assessment scenario,
// hence they are not randomised.
function createRandomEvents()
{
    if (createRandomEventType() === 'INSERTED') {

        $events = [
          'INSERTED',
          'Placement',
          '123',
          'null',
          createRandomTimeStamp()
        ];

    } elseif (createRandomEventType() === 'UPDATED') {

        $events = [
          'UPDATED',
          'Placement',
          '123',
          createRandomFieldsUpdated(),
          createRandomTimeStamp()
        ];

    } elseif (createRandomEventType() === 'DELETED') {

        $events = [
          'DELETED',
          'Placement',
          '123',
          createRandomFieldsUpdatedOrNull(),
          createRandomTimeStamp()
        ];
    }

    $stringWithRandomEvents = implode(', ', $events);

    return  $stringWithRandomEvents . "\r\n";
}

// Update 'event-file.txt' with new entries.
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
