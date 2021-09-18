<?php
session_start();

include 'php-functions/form-validation.php';
include 'php-functions/create-event-file.php';
include 'php-functions/get-event-file.php';
include 'php-functions/all-timestamps.php';
include 'php-functions/csrf-token.php';
?>

<!DOCTYPE html>
<html lang="en-gb">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <title>Junior Developer Assessment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="body">
    <div class="container">
        <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="event-type-and-fields-updated-container">
                <label class="select" for="event-type-selection">Select:</label>
                <select class="select-box" name="eventType" id="">
                    <option value="Event type">Event type</option>
                    <option value="INSERTED">INSERTED</option>
                    <option value="UPDATED">UPDATED</option>
                    <option value="DELETED">DELETED</option>
                </select>
                <button class="btn-single-query" id="btn-event-type" type="submit" name="btnEventType" value="btnEventType">Search</button>
                <label class="select select-fields-updated" for="fields-updated-selection">Select:</label>
                <select class="select-box" name="fieldsUpdated" id="">
                    <option value="Fields updated">Fields updated</option>
                    <option value="status">status</option>
                    <option value="companyUrl">companyUrl</option>
                    <option value="hoursPerDay">hoursPerDay</option>
                    <option value="overtimeRate">overtimeRate</option>
                </select>
                <button class="btn-single-query" id="btn-fields-updated" type="submit" name="btnFieldsUpdated" value="btnFieldsUpdated">Search</button>
            </div>
            <label class="select" for="from-timestamp">Select:</label>
            <select class="select-box" name="fromTimestamp" id="">
                <option value="From timestamp">From timestamp</option>

                <?php
                foreach(getAllTimestampsInAscendingOrder() as $timestamp):
                ?>

                <option value="<?php echo $timestamp; ?>"><?php echo $timestamp; ?></option>

                <?php
                endforeach;
                ?>

            </select>
            <label class="select select-to-timestamp" for="to-timestamp">Select:</label>
            <select class="select-box" name="toTimestamp" id="">
                <option value="To timestamp">To timestamp</option>
                
                <?php
                foreach(getAllTimestampsInAscendingOrder() as $timestamp):
                ?>

                <option value="<?php echo $timestamp; ?>"><?php echo $timestamp; ?></option>

                <?php
                endforeach; 
                ?>

            </select>
            <button class="btn-single-query" id="btn-timestamps-range" type="submit" name="btnTimestamps" value="btnTimestamps">Search</button>
            <button class="btn-combined-query" type="submit" name="combinedQuery" value="combinedQuery">Search combination</button>
            <input type="hidden" name="tokenCsrf" value="<?php echo createCsrfToken(); ?>">
        </form>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <button class="btn-create" type="submit" name="createFile" value="createFile">Generate event file</button>
            <input type="hidden" name="tokenCsrf" value="<?php echo createCsrfToken(); ?>">
        </form>
        <span class="result-header">Result:</span>
        <div class="result-container">

            <?php
            include 'php-functions/functions.php'; 
            ?>

        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>
