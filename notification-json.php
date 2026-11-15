<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");
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

$data = json_decode(file_get_contents('php://input'), true);

$token = $data['token'] ?? NULL;
$authentication_status = false;
$authentication_users=[
'7tnz5Yc8xjQp' => 'localhost',
'6ZQmRVJywWEJ' => 'RPI-1',
'0tkELU4RN9QY' => 'S02',
'wCMxgjB0wgUn' => 'Router',
'NloqYbEIS14h' => 'Service2',
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
$date = time();
$ip = $_SERVER['REMOTE_ADDR'];
if(empty($data['title']))
{
	echo 'Error: 2BBY-7LFE-Z4CF-YCLN-53W7-NTYX';
	exit;
}
if(empty($data['message']))
{
	echo 'Error: EYYJ-CC2M-SXH5-7VTK-UK72-5WXX';
	exit;
}
$title = isset($data['title']) ? GlobalXSSFilter($data['title']) : NULL;
$message = isset($data['message']) ? GlobalXSSFilter($data['message']) : NULL;
if (isset($data['priority']))
{
	$priority = $data['priority'];
	if(preg_match('/[^a-zA-Z0-9\s]/', $priority))
	{
		echo 'Error: J0P6-B2NO-DRYC-Z768-CQOW-Y8C1';
		exit;
	}
}
else
{
	$priority = NULL;
}

$EventLogNotification = true;

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