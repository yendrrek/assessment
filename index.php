<?php
session_start();

include 'php-functions/form-validation.php';
include 'php-functions/generating-event-file.php';
include 'php-functions/getting-event-file.php';
include 'php-functions/all-timestamps.php';
include 'php-functions/search-queries.php';
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
    <link rel="stylesheet" href="style/reset.css">
    <link rel="stylesheet" href="style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body class="body">
    <form id="form-search-options" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
                <button class="btn" type="submit"
                        name="btnEventType" value="btnEventType">Search</button>

                <select class="select-box select-box_fields-updated-margin-left" name="fieldsUpdated">
                    <option value="Fields updated">Fields updated</option>
                    <option value="status">status</option>
                    <option value="companyUrl">companyUrl</option>
                    <option value="hoursPerDay">hoursPerDay</option>
                    <option value="overtimeRate">overtimeRate</option>
                    <option value="null">not updated</option>
                </select>
                <button class="btn" type="submit"
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
                <button class="btn btn_timestamps" type="submit"
                        name="btnTimestamps" value="btnTimestamps">Search</button>
            </div>

        </div>

        <!-- Closing DIV is contained in another FORM further down. -->
        <div class="combined-and-generate-btns-container">
            <button class="btn btn_combined-query-and-generate" type="submit"
                    name="combinedQuery" value="combinedQuery">Search combination</button>
            <input id="token-search-options" type="hidden" name="tokenCsrf"
                   value="<?php echo $_SESSION['tokenCsrf']; ?>">

    </form>

    <form id="form-generate-event-file" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            <button class="btn btn_combined-query-and-generate" type="submit"
                    name="generateEventFile" value="generateEventFile">Generate event file</button>
            <input id="token-generate-event-file" type="hidden" name="tokenCsrf"
                   value="<?php echo $_SESSION['tokenCsrf']; ?>">
        </div>

    </form>

    <div class="result-container">
        <span class="result-header">Result:
            <div class="result-summary">

                <?php
                if (!empty(showResultSummary())) {

                    if (is_array(showResultSummary())) {

                        foreach (showResultSummary() as $summary) {

                            if (!empty($summary)) {
                                echo "{$summary}<br><br>";
                            }
                        }

                    } else {
                        echo "".showResultSummary()."<br><br>";
                    }
                }
                ?>

            </div>
        </span>
        <div class="result-content">
            <div class="event-file-generated-info">New event file has been generated.</div>

        <?php
        if (!empty(getSearchedEvents())) {

            foreach (getSearchedEvents() as $event) {
                echo $event . '<br><br>';
            }

        } else {
            echo showSearchErrors();
        }

        if (!empty(getEventsByRangeOfTimestampsForCombinedSearch())) {

            foreach (getEventsByRangeOfTimestampsForCombinedSearch() as $event) {
                echo $event . '<br><br>';
            }

        } else {
            echo showCombinedSearchErrors();
        }
        ?>

        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
