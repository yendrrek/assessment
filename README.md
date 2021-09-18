# Searching system assessment

## Scenario

A system generates events that are stored in a file in the following format:
event type, entity name, entity id, [fields updated], timestamp

Example:

INSERTED, Placement, 12, null, 2018-04-10 12:34:56.789
INSERTED, Placement, 13, null, 2018-04-10 12:43:10.123
UPDATED, Company, 123, [status, companyUrl], 2018-04-10 12:44:00.123
UPDATED, Placement, 13, [status, hoursPerDay, overtimeRate], 2018-04-10 14:52:43.699

You should assume that:

Event type – can be one of INSERTED, UPDATED, DELETED

Timestamp will always be in the format yyyy-MM-dd HH:mm:ss.ms

Fields updated are the names of fields rather actual values

Events will not necessarily be in a predictable order

## Requirement

Develop a system that takes an event file (as outlined above) as an input and can support
the following functions:

1. Display all events of a specific event type

2. Get all events where a specific field has been updated. For example all events where
the ‘status’ field changed

3. Get all events between two timestamps.

4. Allow combinations of (1), (2) and (3). For example, all UPDATED events where status
changed between 2018-04-10 12:00:00.000 and 2018-04-10 12:00:11.500


## MY APPROACH

### HOW TO RUN THE SYSTEM

You will need Apache and at least PHP 7.1.

Project is located in 'localhost/assessment'.

In my httpd.conf I created a virtual host:

```
<VirtualHost 127.0.0.1:8082>
<Directory "{$path}/www/assessment">
    Options FollowSymLinks Indexes
    AllowOverride All
    Order deny,allow
    allow from All
</Directory>
ServerName assessment
ServerAlias assessment 127.0.0.1
ScriptAlias /cgi-bin/ "{$path}/www/assessment/cgi-bin/"
DocumentRoot "{$path}/www/assessment"
ErrorLog "{$path}/apache/logs/error_log"
CustomLog "{$path}/apache/logs/access.log" combined
</VirtualHost>

```
'www' is localhost

### TESTING THE SYSTEM

Click 'Generate event file' button to randomly create a file on your server with event entries.
All the parts of each event mentioned in 'Requriment' above are randomly generated.

