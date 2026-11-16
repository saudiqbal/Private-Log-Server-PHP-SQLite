<?php
$db = new PDO('sqlite:db/EventLog.db');
$db->exec("DELETE FROM EventTable ORDER BY rowid DESC LIMIT -1 OFFSET 25000");
// Uncomment below to delete older records by days (Example: 90 days)
//$oldtimestamp = strtotime(date("Y-m-d", strtotime("-90 day")));
//$db->exec("DELETE FROM EventTable WHERE Date < $oldtimestamp");
$db->exec('VACUUM;');
$db = NULL;
?>