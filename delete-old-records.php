<?php
$db = new PDO('sqlite:db/EventLog.db');

// Uncomment below to keep the last 25000 records
//$db->exec("DELETE FROM EventTable ORDER BY rowid DESC LIMIT -1 OFFSET 25000");

// Uncomment the code block below to delete older records by days (Example: 90 days)
//$oldtimestamp = strtotime(date("Y-m-d", strtotime("-90 day")));
//$stmt = $db->prepare('DELETE FROM EventTable WHERE CAST(Date AS INTEGER) < :limit_number');
//$stmt->bindValue(':limit_number', $oldtimestamp, PDO::PARAM_INT);
//$stmt->execute();
//$deletedCount = $stmt->rowCount();
//echo "Deleted {$deletedCount} rows.";

$db->exec('VACUUM;');
$db = NULL;
?>