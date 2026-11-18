<?php
$startTime = array_sum(explode(' ', microtime()));
header("Cache-Control: no-store, no-cache, must-revalidate");

$HDD_info=shell_exec('df -k / | awk \'NR==2 {print $2, $3, $4}\' | tr -d \'\n\'');
[$dt, $du, $df] = explode(' ', $HDD_info);
$df = $df * 1024;
$dt = $dt * 1024;
$du = $du * 1024;
$dp = sprintf('%.2f',($du / $dt) * 100);

/* and we formate the size from bytes to MB, GB, etc. */
$df = formatSize($df);
$du = formatSize($du);
$dt = formatSize($dt);

function formatSize( $bytes )
{
$types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
return( round( $bytes, 2 ) . " " . $types[$i] );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Log Server</title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1, width=device-width">
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<link rel="stylesheet" type="text/css" href="css/dashboard-header.css">
<script src="js/keyboard-navigation.js"></script>
<script>
if(typeof(EventSource) !== "undefined")
{
var source = new EventSource("php/notification-server-json.php");
source.onmessage = function(event) {
var data = JSON.parse(event.data);
document.getElementById("serverresponse").innerHTML = data.ramusage;
document.getElementById("ramtotal").innerHTML = data.ramtotal;
document.getElementById("servertime").innerHTML = data.servertime;
var totalSeconds = data.uptime;
var timeBreakdown = convertSecondsToTime(totalSeconds);
var array = [];
Object.keys(timeBreakdown).forEach((key, index) =>{
	array.push({[key]: timeBreakdown[key]});
})
var uptimeValue = "";
if (array[0]['years'] !== 0) {
	uptimeValue += pluralize("year", array[0]['years'], true);
	uptimeValue += " ";
}
if (array[1]['days'] !== 0) {
	uptimeValue += pluralize("day", array[1]['days'], true);
	uptimeValue += ", ";
}
uptimeValue += String(array[2]['hours']).padStart(2, '0') + ":" + String(array[3]['minutes']).padStart(2, '0') + ":" + String(array[4]['seconds']).padStart(2, '0');
document.getElementById("uptime").innerHTML = uptimeValue;

document.getElementById("rambar").innerHTML = drawPercentBar(200, data.rampercentage, '#4eaeff', '#cce9ff');
document.getElementById("cpubar").innerHTML = drawCPUBar(200, data.cpu, '#4eaeff', '#cce9ff');
};
} else {
document.getElementById("serverresponse").innerHTML = "Sorry, your browser does not support server-sent events...";
}
function setDISK() {
document.getElementById("diskbar").innerHTML = drawDISKBar(200, <?php echo $dp; ?>, '#4eaeff', '#cce9ff');
}
window.onload = setDISK;
</script>
<script language="javascript">
// drawPercentBar()
// Written by Matthew Harvey (matt AT unlikelywords DOT com)
// (http://www.unlikelywords.com/html-morsels/)
// Distributed under the Creative Commons
// "Attribution-NonCommercial-ShareAlike 2.0" License
// (http://creativecommons.org/licenses/by-nc-sa/2.0/)
function drawPercentBar(width, percent, color, background)
{
var pixels = width * (percent / 100);
if (!background) { background = "none"; }
if(percent>=50 && percent<=75)
{
color = "#ff8c00";
}
else if(percent>75 && percent<=100)
{
color = "#ff0000";
}
var htmlcode = "<div style=\"position: relative; line-height: 1em; background-color: " + background + "; border: 1px solid black; width: " + width + "px\">"
+ "<div style=\"height: 1.5em; width: " + pixels + "px; background-color: " + color + ";\"></div>"
+ "<div style=\"position: absolute; text-align: center; padding-top: .25em; width: " + width + "px; top: 0; left: 0\">" + percent + "%</div>"
+ "</div>";
return htmlcode;
}

function drawCPUBar(width, percent, color, background)
{
var pixels = width * (percent / 100);
if (!background) { background = "none"; }
if(percent>=50 && percent<=75)
{
color = "#ff8c00";
}
else if(percent>75 && percent<=100)
{
color = "#ff0000";
}
var cpucode = "<div style=\"position: relative; line-height: 1em; background-color: " + background + "; border: 1px solid black; width: " + width + "px\">"
+ "<div style=\"height: 1.5em; width: " + pixels + "px; background-color: " + color + ";\"></div>"
+ "<div style=\"position: absolute; text-align: center; padding-top: .25em; width: " + width + "px; top: 0; left: 0\">" + percent + "%</div>"
+ "</div>";
return cpucode;
}

function drawDISKBar(width, percent, color, background)
{
var pixels = width * (percent / 100);
if (!background) { background = "none"; }
if(percent>=50 && percent<=75)
{
color = "#ff8c00";
}
else if(percent>75 && percent<=100)
{
color = "#ff0000";
}
var diskcode = "<div style=\"position: relative; line-height: 1em; background-color: " + background + "; border: 1px solid black; width: " + width + "px\">"
+ "<div style=\"height: 1.5em; width: " + pixels + "px; background-color: " + color + ";\"></div>"
+ "<div style=\"position: absolute; text-align: center; padding-top: .25em; width: " + width + "px; top: 0; left: 0\">" + percent + "%</div>"
+ "</div>";
return diskcode;
}
</script>
</head>
<body>
<header class="commonnavbar">
<div class="nav-left">
<div class="dropdownClickable">
<span class="dropbtnClickable nav-logo">Log Server</span>
<div class="dropdownClickable-content">
<a href="https://www.google.com">Google</a>
<a href="https://www.youtube.com">YouTube</a>
<a href="https://www.github.com">Other Services</a>
</div>
</div>
<form action="index.php" method="POST"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form>
</div>
<div class="nav-right">
<button class="nav-menu-btn" aria-label="Toggle menu" aria-expanded="false"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="18" x2="20" y2="18" /></svg></button>
<span class="navlinks">
<a href="/" class="nav-link">Home</a>
</span>
</div>
</header>
<div class="content">
<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
	$searchTerm = $_GET['search'];
	$searchTerm = trim($searchTerm);
	$searchactive = true;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
	$searchTerm = $_POST['search'];
	$searchTerm = trim($searchTerm);
	$searchactive = true;
}
$db = new PDO("sqlite:db/EventLog.db");
if (($searchactive ?? false) === true)
{
	$result = $db->prepare('SELECT Title, Message, IP FROM EventTable WHERE Title LIKE :search OR Message LIKE :search OR IP LIKE :search');
	$result->execute([':search' => '%' . $searchTerm . '%']);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);
}
else
{
	$result = $db->query("SELECT rowid FROM EventTable");
	$rows = $result->fetchAll();
}
$total_pages = count($rows);
if(!empty($rows))
{
echo <<<HTML
<div class="table-wrapper">
<table id="serverDataTable">
<thead style="cursor: pointer;">
<tr id="DataTableTitle">
<th style="width: 10px;"></th>
<th>Status</th>
<th>Timestamp</th>
<th>IP</th>
</tr>
</thead>
<tbody>
HTML;
$limit = 25;
$adjacents = 3;
if(isset($_GET['page']))
{
$page = $_GET['page'];
if(preg_match('/[^0-9]/i', $page))
{
echo "SQL Injection detected!";
exit();
}
}
if(isset($page))
$start = ($page - 1) * $limit; 			//first item to display on this page
else
$start = 0;
$i = 1;
//now output the data to a simple html table...
if (($searchactive ?? false) === true)
{
	$result = $db->prepare('SELECT Title, Message, Date, IP, User, P FROM EventTable WHERE Title LIKE :search OR Message LIKE :search OR IP LIKE :search ORDER BY Date DESC LIMIT '.$start.', '.$limit.'');
	$result->execute([':search' => '%' . $searchTerm . '%']);
	$result = $result->fetchAll(PDO::FETCH_ASSOC);
}
else
{
	$result = $db->query("SELECT Title, Message, Date, IP, User, P FROM EventTable ORDER BY rowid DESC LIMIT '$start', '$limit'");
}

foreach($result as $row) {
if(isset($row['P']))
{
$priority = $row['P'];
if($priority <= 3 || $priority == 'info')
{
	$priorityicon = '<svg fill="#3399ff" viewBox="0 0 16 16" style="vertical-align:text-bottom;width:16px;height:16px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg> ';
}
elseif($priority >= 4 && $priority <= 7 || $priority == 'unknown' || $priority == 'warning')
{
	$priorityicon = '<svg fill="#FFA500" viewBox="0 0 16 16" style="vertical-align:text-bottom;width:16px;height:16px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg> ';
}
elseif($priority > 7 || $priority == 'error')
{
	$priorityicon = '<svg fill="#FF0000" viewBox="0 0 16 16" style="vertical-align:text-bottom;width:16px;height:16px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg> ';
}
}
else
{
	$priorityicon = NULL;
	$priority = NULL;
}
$user = isset($row['User']) ? " / User: " . $row['User'] : NULL;
$priority = isset($priority) ? " / P: " . $priority : NULL;
echo "<tr class=\"main-row\" tabindex=\"0\" aria-expanded=\"false\">
<td><span class=\"chevron\">&#9656;</span></td>
<td data-label=\"Status\">" . $priorityicon . $row['Title'] . "</td>
<td data-label=\"Timestamp\">";
echo date('Y-m-d H:i:s', $row['Date']);
echo "</td>
<td data-label=\"IP\">".$row['IP']."</td>
</tr>
<tr class=\"detail-row\">
<td colspan=\"4\">
<div class=\"detail-wrapper\">
<div class=\"detail-content\">
<div class=\"detail-inner\">
<pre>".$row["Message"]."</pre>
<hr class=\"hr-style\">";
echo date('l, F d Y, h:i:s A', $row['Date']) . $priority . $user;
echo "</div>
</div>
</div>
</td>
</tr>\n";
}
echo "</div>";
// echo $result->fetchColumn(1);
// close the database connection
$db = NULL;
/*
	Plugin Name: *Digg Style Paginator
	Plugin URI: http://www.mis-algoritmos.com/2006/11/23/paginacion-al-estilo-digg-y-sabrosus/
	Description: Adds a <strong>digg style pagination</strong>.
	Version: 0.1 Beta
*/
function pagination($total_pages,$limit,$page,$file,$adjacents){
		if($page)
				$start = ($page - 1) * $limit; 			//first item to display on this page
			else
				$start = 0;								//if no page var is given, set start to 0

		/* Setup page vars for display. */
		if ($page == 0) $page = 1;					//if no page var is given, default to 1.
		$prev = $page - 1;							//anterior page is page - 1
		$siguiente = $page + 1;							//siguiente page is page + 1
		$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;						//last page minus 1

		$link_previous = "&#x276E; Previous";
		$link_next = "Next &#x276F;";

		$p = false;
		if(strpos($file,"?")>0)
			$p = true;

		//ob_start();
		if($lastpage > 1){
				//anterior button
				if($page > 1)
								if($p)
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
									else
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
					else
						echo "<span class=\"buttonDisabled leftDisabled\">$link_previous</span>";
				//pages
				if ($lastpage < 7 + ($adjacents * 2)){//not enough pages to bother breaking it up
						for ($counter = 1; $counter <= $lastpage; $counter++){
								if ($counter == $page)
										echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
									else
												if($p)
												echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
												else
												echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				elseif($lastpage > 5 + ($adjacents * 2)){//enough pages to hide some
						//close to beginning; only hide later pages
						if($page < 1 + ($adjacents * 2)){
								for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
										if ($counter == $page)
												echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
											else
														if($p)
														echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
														else
														echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
									}
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}

							}
						//in middle; hide some front and some back
						elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}
							}
						//close to end; only hide early pages
						else{
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				if ($page < $counter - 1)
								if($p)
								echo "<span class=\"pagination-next\"><a href=\"$file$siguiente\" class=\"pagination-button right\">$link_next</a></span>";
								else
								echo "<span class=\"pagination-next\"><a href=\"$file?page=$siguiente\" class=\"pagination-button rightDisabled\">$link_next</a></span>";
					else
						echo "<span class=\"buttonDisabled rightDisabled pagination-next\">$link_next</span>";
			}
	}
// Pagination Ends
if(!isset($page))
$page=1;
echo "\n</tbody>";
echo "\n</table>";
echo "\n</div>";
echo "\n</div>\n";
echo "<nav class=\"pagination_keyboard\">\n";
echo "<div class=\"pagination_style\">\n";
if (($searchactive ?? false) === true)
{
	echo pagination($total_pages,$limit,$page,$_SERVER["PHP_SELF"]."?search=$searchTerm&page=",$adjacents);
}
else
{
	echo pagination($total_pages,$limit,$page,$_SERVER["PHP_SELF"]."?page=",$adjacents);
}
}
else
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg width="20" height="20"><path style="fill:#ffe2c3" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No record or data was found in the database!</div>
</div>';
}
echo "</div>\n";
echo "</nav>\n";
?>
</div>
<div class="footer">
<div style="margin: 0 auto;padding:25px;font-family: monospace, monospace;font-size: 0.7em;">
Server time: <span id="servertime"></span>
<br>
Uptime: <span id="uptime"></span>
</div>
<div style="margin: 0 auto;display:inline-block;font-family: monospace, monospace;font-size: 0.7em;">
RAM
<div style="font-family: Arial; font-size: 0.875em; color: #000">
<span id="rambar"></span>
</div>
<span style='float: left;'><span id="serverresponse"></span> of <span id="ramtotal"></span> used</span>
<br><br>
CPU
<div style="font-family: Arial; font-size: 0.875em; color: #000">
<span id="cpubar"></span>
</div>
<br>
DISK
<div style="font-family: Arial; font-size: 0.875em; color: #000">
<span id="diskbar"></span>
</div>
<span style='float: left;'><?php echo "$du of $dt used"; ?></span>
</div>
<div style="margin: 0 auto; padding:25px;">
<?php $totalTime = array_sum(explode(' ', microtime())) - $startTime;
$totalTime = round($totalTime, 2);
echo "Page generated in " . $totalTime . " seconds";
?>
</div>
</div>
<script>
keyboardPagination('.pagination_keyboard',
{
	prev: '.pagination-prev',
	next: '.pagination-next'
});
</script>
<script>
window.addEventListener('load', function() {
document.addEventListener('click', function(event) {
	document.querySelectorAll('.dropdownClickable-content').forEach(function(el) {
	if (el !== event.target) el.classList.remove('dropbtnClickableshow')
	});
	if (event.target.matches('.dropbtnClickable')) {
	event.target.closest('.dropdownClickable').querySelector('.dropdownClickable-content').classList.toggle('dropbtnClickableshow')
	}
})
})
// Uptime calculator
function pluralize(word, count, inclusive = false) {
	const pluralized = count === 1 ? word : word + 's';
	return inclusive ? `${count} ${pluralized}` : pluralized;
}
function convertSecondsToTime(totalSeconds) {
	const secondsPerMinute = 60;
	const secondsPerHour = secondsPerMinute * 60;
	const secondsPerDay = secondsPerHour * 24;
	const secondsPerYear = secondsPerDay * 365.25;
	let remainingSeconds = totalSeconds;
	const years = Math.floor(remainingSeconds / secondsPerYear);
	remainingSeconds %= secondsPerYear;
	const days = Math.floor(remainingSeconds / secondsPerDay);
	remainingSeconds %= secondsPerDay;
	const hours = Math.floor(remainingSeconds / secondsPerHour);
	remainingSeconds %= secondsPerHour;
	const minutes = Math.floor(remainingSeconds / secondsPerMinute);
	remainingSeconds %= secondsPerMinute;
	const seconds = Math.floor(remainingSeconds);
	return {years: years,days: days,hours: hours,minutes: minutes,seconds: seconds};
}
</script>
<script>
document.getElementById('serverDataTable').addEventListener('click', function (e) {
	const row = e.target.closest('.main-row');
	if (!row) return;
	toggleRow(row);
});
// Allow keyboard users to expand rows with Enter or Space
document.getElementById('serverDataTable').addEventListener('keydown', function (e) {
	if (e.key === 'Enter' || e.key === ' ') {
	const row = e.target.closest('.main-row');
	if (!row) return;
	e.preventDefault();
	toggleRow(row);
	}
});
document.getElementById('DataTableTitle').addEventListener('click', function (e) {
	const rows = document.querySelectorAll('table [aria-expanded]');
	rows.forEach((row) => {
	if (!row) return;
	toggleRow(row);
	});
});
function toggleRow(row) {
	const detailRow = row.nextElementSibling;
	if (!detailRow || !detailRow.classList.contains('detail-row')) return;
	const isExpanded = row.getAttribute('aria-expanded') === 'true';
	row.setAttribute('aria-expanded', !isExpanded);
	detailRow.classList.toggle('open');
}
</script>
<script>
const btn = document.querySelector(".nav-menu-btn");
const nav = document.querySelector(".navlinks");
btn.addEventListener("click", () => {
const isOpen = nav.classList.toggle("open");
btn.setAttribute("aria-expanded", isOpen);
});
</script>
</body>
</html>