<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function GlobalXSSFilter($param)
{
	$custom_entities = array(
	'&' => '&amp;',
	'"' => '&quot;',
	"'" => '&apos;',
	'/' => '&sol;',
	'<' => '&lt;',
	'>' => '&gt;',
	'\\' => '&bsol;'
	);
	$param = strtr($param, $custom_entities);
	$normal_characters = 'a-zA-Z0-9\s`~!@#$\€\£\¥%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]\r\n\f\v';
	$param = preg_replace("/[^$normal_characters]/", '', $param);
	return $param;
}

$token = $_GET['token'] ?? NULL;
$authentication_status = false;
$authentication_users=[
'7tnz5Yc8xjQp' => 'localhost',
'6ZQmRVJywWEJ' => 'RPI-1',
'0tkELU4RN9QY' => 'Home Assistant VM 102',
'wCMxgjB0wgUn' => 'Router',
'NloqYbEIS14h' => 'Jellyfin VM 103',
'b4zY64y29NdX' => 'HomeServer',
'vK5ax8v2S9z7' => 'Proxmox',
'bT2T9e8HFeIT' => 'Synology'
];
foreach ($authentication_users as $key => $keyvalue) {
$authentication_status = ($key == $token) ? true : false;
if($authentication_status) break;
}
if(!$authentication_status) {
	http_response_code(401);
	echo 'badauth';
	exit;
}
else
{
$EventLogNotification = true;

$date = time();
$ip = $_SERVER['REMOTE_ADDR'];

if(empty($_GET['title']))
{
	echo 'Error: 2BBY-7LFE-Z4CF-YCLN-53W7-NTYX';
	exit;
}
if(empty($_GET['message']))
{
	echo 'Error: EYYJ-CC2M-SXH5-7VTK-UK72-5WXX';
	exit;
}

$title = isset($_GET['title']) ? GlobalXSSFilter($_GET['title']) : NULL;
$message = isset($_GET['message']) ? GlobalXSSFilter($_GET['message']) : NULL;
if (isset($_GET['priority']))
{
	$priority = $_GET['priority'];
	if(preg_match('/[^a-zA-Z0-9\s]/', $priority))
	{
		echo 'Error: G6GE-A8AU-06HB-RQCZ-XBRG-R1M5';
		exit;
	}
}
else
{
	$priority = NULL;
}

// Event Log
if ($EventLogNotification)
{
	$dbEventLog = new PDO('sqlite:db/EventLog.db');
	$dbEventLog->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbEventLog->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $dbEventLog->prepare('INSERT INTO EventTable (Title, Message, DATE, IP, User, P) VALUES (:Title , :Message, :Date, :IP, :User, :Priority)');
	$stmt->bindValue(':Title', $title);
	$stmt->bindValue(':Message', $message);
	$stmt->bindValue(':Date', $date);
	$stmt->bindValue(':IP', $ip);
	$stmt->bindValue(':User', $keyvalue);
	$stmt->bindValue(':Priority', $priority);
	$stmt->execute();
	$status = true;
}

echo ($status) ? 'OK' : 'ERROR';
}
?>