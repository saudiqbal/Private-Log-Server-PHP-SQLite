<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


foreach(file('/proc/meminfo') as $ri)
$m[strtok($ri, ':')] = strtok('');
$totalmemory = 1024 * floatval( trim( str_replace( ' kB', '', $m['MemTotal'] ) ) );
$freememory = 1024 * floatval( trim( str_replace( ' kB', '', $m['MemFree'] ) ) );
$cachedmemory = 1024 * floatval( trim( str_replace( ' kB', '', $m['Cached'] ) ) );
$Buffersmemory = 1024 * floatval( trim( str_replace( ' kB', '', $m['Buffers'] ) ) );
$ramusage = $totalmemory - $freememory - $cachedmemory - $Buffersmemory;

function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}
return $bytes;
}

function get_percentage($total, $number)
{
if ( $total > 0 ) {
return round($number / ($total / 100),2);
} else {
return 0;
}
}
$mempercentage = get_percentage($totalmemory,$ramusage);
$servertime = date('Y-m-d H:i:s');
$uptiime = shell_exec("cut -d. -f1 /proc/uptime | tr -d '\n'");

$cpu = shell_exec('(grep \'cpu \' /proc/stat;sleep 0.1;grep \'cpu \' /proc/stat)|awk -v RS="" \'{print "CPU "($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5)"%"}\'');
$cpu = str_replace("\n", '', $cpu);
$cpu = trim($cpu, "%");
$cpu = trim($cpu, "CPU ");
$cpu = number_format($cpu, 2, '.', '');

echo "retry: 1000\n";
echo "data: {\n";
echo "data: \"ramusage\": \"".formatSizeUnits($ramusage)."\",\n";
echo "data: \"ramtotal\": \"".formatSizeUnits($totalmemory)."\",\n";
echo "data: \"rampercentage\": \"$mempercentage\",\n";
echo "data: \"cpu\": \"$cpu\",\n";
echo "data: \"servertime\": \"$servertime\",\n";
echo "data: \"uptime\": \"$uptiime\"\n";
echo "data: }\n\n";

flush();
?>