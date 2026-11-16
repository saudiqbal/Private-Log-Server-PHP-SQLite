<?php
if($_SERVER['HTTPS'] != 'on')
{
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

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

$token = $_GET['token'] ?? null;
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
if($authentication_status) goto Authenticated;
}
$headers = getallheaders();
if (isset($headers['Authorization'])) {
	$authorizationHeader = $headers['Authorization'];
	$matches = array();
	if (preg_match('/Bearer (.+)/', $authorizationHeader, $matches)) {
		if (isset($matches[1])) {
			$token = $matches[1];
			foreach ($authentication_users as $key => $keyvalue) {
				$authentication_status = ($key == $token) ? true : false;
				if($authentication_status) goto Authenticated;
			}
		}
	}
}
if(!$authentication_status) {
	http_response_code(401);
	echo 'badauth';
	exit;
}
Authenticated:
// Retrieve the raw POST data
$jsonData = file_get_contents('php://input');
// Decode the JSON data into a PHP associative array
$data = json_decode($jsonData, true);
$title = isset($data['title']) ? GlobalXSSFilter($data['title']) : NULL;
$message = isset($data['message']) ? GlobalXSSFilter($data['message']) : NULL;
if (isset($data['priority']))
{
	$priority = $data['priority'];
	if(preg_match('/[^a-zA-Z0-9\s]/', $priority))
	{
		echo 'Error: T2CQ-GC0M-9KP9-MBE3-PTV2-WL0F';
		exit;
	}
}
else
{
	$priority = NULL;
}
// Check if decoding was successful
if ($data !== null) {
	// Access the data and perform operations
	$date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
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
echo 'OK';
   // Perform further processing or respond to the request
} else {
	// JSON decoding failed
	http_response_code(400); // Bad Request
	echo 'Invalid JSON data';
}
?>