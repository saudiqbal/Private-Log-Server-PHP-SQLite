<?php
$db = new PDO('sqlite:db/EventLog.db');
$db->exec("DELETE FROM EventTable ORDER BY rowid DESC LIMIT -1 OFFSET 25000");
$db->exec('VACUUM;');
$db = NULL;
?>