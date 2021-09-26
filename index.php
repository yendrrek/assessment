<?php
session_start();

include 'php-functions/csrf-token.php';
include 'php-functions/form-validation.php';
include 'php-functions/generate-event-file.php';
include 'php-functions/get-event-file.php';
include 'php-functions/all-timestamps.php';
?>

<!DOCTYPE html>
<html lang="en-gb">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="noindex">
    <title>Searching system</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="body">

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

        <div class="search-options-container">

            <!-- All drop-down menus with respective search buttons are wrapped in a <div>,
                so they can be treated as one element by flexbox. -->
            <div>
                <select class="select-box" name="eventType">
                    <option value="Event type">Event type</option>
                    <option value="INSERTED">INSERTED</option>
                    <option value="UPDATED">UPDATED</option>
                    <option value="DELETED">DELETED</option>
                </select>

                <button class="btn" id="btn-event-type" type="submit"
                        name="btnEventType" value="btnEventType">Search</button>

                <select class="select-box select-box_fields-updated-margin-left" name="fieldsUpdated">
                    <option value="Fields updated">Fields updated</option>
                    <option value="status">status</option>
                    <option value="companyUrl">companyUrl</option>
                    <option value="hoursPerDay">hoursPerDay</option>
                    <option value="overtimeRate">overtimeRate</option>
                    <option value="null">not updated</option>
                </select>

                <button class="btn" id="btn-fields-updated" type="submit"
                        name="btnFieldsUpdated" value="btnFieldsUpdated">Search</button>
            </div>

            <div>
                <select class="select-box" name="fromTimestamp">
                    <option value="From timestamp">From timestamp</option>

                    <?php
                    foreach(getAllTimestampsInAscendingOrder() as $timestamp):
                    ?>

                    <option value="<?php echo $timestamp; ?>"><?php echo $timestamp; ?></option>

                    <?php
                    endforeach;
                    ?>

                </select>

                <select class="select-box" name="toTimestamp">
                    <option value="To timestamp">To timestamp</option>

                    <?php
                    foreach(getAllTimestampsInAscendingOrder() as $timestamp):
                    ?>

                    <option value="<?php echo $timestamp; ?>"><?php echo $timestamp; ?></option>

                    <?php
                    endforeach;
                    ?>

                </select>

                <button class="btn" id="btn-timestamps-range" type="submit"
                        name="btnTimestamps" value="btnTimestamps">Search</button>
            </div>

        </div>

        <div class="combined-and-generate-btns-container">

            <button class="btn btn_combined-query-and-generate" type="submit"
                    name="generateEventFile" value="generateEventFile">Generate event file</button>

            <input type="hidden" name="tokenCsrf" value="<?php echo createCsrfToken(); ?>">

    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            <button class="btn btn_combined-query-and-generate" type="submit"
            name="combinedQuery" value="combinedQuery">Search combination</button>

            <input type="hidden" name="tokenCsrf" value="<?php echo createCsrfToken(); ?>">

        </div>

    </form>

    <div class="result-container">

        <span class="result-header">Result:</span>

        <div class="result-content">

        <?php
        include 'php-functions/search-queries.php';
        ?>

        </div>

    </div>
        
    <script src="main.js"></script>

</body>
</html>
