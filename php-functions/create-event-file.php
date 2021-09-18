<?php
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

    $staticArrayWithfieldsUpdated = [
      'status',
      'companyUrl',
      'hoursPerDay',
      'overtimeRate'
    ];
    
    $arrayWithRandomKeys = array_rand($staticArrayWithfieldsUpdated, rand(1, count($staticArrayWithfieldsUpdated)));

    if (count($arrayWithRandomKeys) === 1) {

        $stringWithRandomFieldsUpdated = '[' . $staticArrayWithfieldsUpdated[$arrayWithRandomKeys] . ']';

        return $stringWithRandomFieldsUpdated;

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {

          array_push($randomArrayWithFieldsUpdated, $staticArrayWithfieldsUpdated[$randomKeys]);
        }
    }

    $stringWithRandomFieldsUpdated = '[' . implode(', ', $randomArrayWithFieldsUpdated) . ']';

    return $stringWithRandomFieldsUpdated;
}

function createRandomFieldsUpdatedOrNull()
{
    $randomArrayWithFieldsUpdatedOrNull = [];

    $staticArrayWithFieldsUpdatedAndNull = [
      'null',
      createRandomFieldsUpdated()
    ];

    $arrayWithRandomKeys =
    array_rand($staticArrayWithFieldsUpdatedAndNull, rand(1, count($staticArrayWithFieldsUpdatedAndNull)));

    if ($arrayWithRandomKeys < 2) {

        $stringNull = 'null';

        return $stringNull;

    } else {

        foreach ($arrayWithRandomKeys as $randomKeys) {

            array_push($randomArrayWithFieldsUpdatedOrNull, $staticArrayWithFieldsUpdatedAndNull[$randomKeys]);
        }
    }

    $stringStillWithNull = implode(', ', $randomArrayWithFieldsUpdatedOrNull);

    $stringAlreadyWithoutNull = substr($stringStillWithNull, 5);

    return $stringAlreadyWithoutNull;
}   

function createRandomTimeStamp()
{
    $integer = rand(0000000000,1262055681);

    $milliseconds = rand(000, 999);

    $randomTimestamp = date('Y-m-d H:i:s.' . $milliseconds, $integer);
    
    return $randomTimestamp;
}

function createRandomEvents()
{
    $randomEvents = [
      createRandomEventType(),
      'Placement',
      '123',
      createRandomFieldsUpdatedOrNull(),
      createRandomTimeStamp()
    ];

    $stringWithRandomEvents = implode(', ', $randomEvents);

    return $stringWithRandomEvents . "\r\n";

}

function createEventFileWithRandomEntries()
{

    if (!empty($_POST['createFile']) && validateSearchForm() === true) {

        $eventFile = fopen('../assessment/event-file.txt', 'w') or die("Unable to open file!");

        for ($i = 1; $i < rand(500, 1000); $i++) {

            fwrite($eventFile, createRandomEvents());

        }

        fclose($eventFile);

        echo '<script>alert("New event file generated.")</script>';
    }

    
}

createEventFileWithRandomEntries();
