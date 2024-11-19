<?php
/*
*******************************************************************************************************************
* Warning!!!, Tidak untuk diperjual belikan!, Cukup pakai sendiri atau share kepada orang lain secara gratis
*******************************************************************************************************************
* Author : @Maizil https://t.me/maizil41
*******************************************************************************************************************
* © 2024 Mutiara-Net By @Maizil
*******************************************************************************************************************
*/
function getCpuUsage() {
    $cpuUsage = [];

    if (file_exists("/sys/devices/system/cpu/cpufreq/policy0/scaling_available_frequencies")) {
        $command = "expr 100 - $(top -n 1 | grep 'CPU:' | awk -F '%' '{print $4}' | awk -F ' ' '{print $2}')";
        $output = shell_exec($command);

        if ($output !== null) {
            $cpuValue = intval(trim($output));
            $cpuUsage[] = ['cpu' => $cpuValue];
        }
    }

    return $cpuUsage;
}

$data = getCpuUsage();

$cpuValue = $data[0]['cpu'];

$output = shell_exec('/usr/bin/cpustat');

list($frequency, $temperature) = explode(' / ', trim($output));

$freq = htmlspecialchars($frequency);
$temp = htmlspecialchars($temperature);

function getMemoryInfo() {
    $data = file_get_contents('/proc/meminfo');
    $lines = explode("\n", trim($data));

    $memInfo = [];
    foreach ($lines as $line) {
        if (preg_match('/^(\w+):\s+(\d+)\s+(\w+)/', $line, $matches)) {
            $memInfo[$matches[1]] = [
                'value' => (int)$matches[2],
                'unit' => $matches[3]
            ];
        }
    }

    return $memInfo;
}

function calculatePercentage($part, $total) {
    if ($total == 0) {
        return 0;
    }
    return ($part / $total) * 100;
}

function convertToMBandGB($valueInKB) {
    $valueInMB = $valueInKB / 1024;
    $valueInGB = $valueInMB / 1024;
    
    return [
        'MB' => $valueInMB,
        'GB' => $valueInGB
    ];
}

$memoryInfo = getMemoryInfo();

$totalMemory = convertToMBandGB($memoryInfo['MemTotal']['value']);
$freeMemory = convertToMBandGB($memoryInfo['MemFree']['value']);

function formatMemory($valueInMB) {
    if ($valueInMB < 1024) {
        return number_format($valueInMB, 2) . " MiB";
    } else {
        return number_format($valueInMB / 1024, 2) . " GiB";
    }
}

$rootInfo = shell_exec('df -h /');

$lines = explode("\n", trim($rootInfo));

$data = preg_split('/\s+/', $lines[1]);

$freehdd = $data[3];
$used = $data[4];

$freehdd = preg_replace('/([0-9\.]+)([A-Za-z])/', '$1 $2iB', $freehdd);
$used = preg_replace('/([0-9\.]+)([A-Za-z])/', '$1 $2iB', $used);
    
$model = trim(shell_exec('cat /proc/device-tree/model | tr -d "\000"'));

$releaseInfo = shell_exec("cat /etc/openwrt_release");

$lines = explode("\n", trim($releaseInfo));
$id = "";
$version = "";

foreach ($lines as $line) {
    if (strpos($line, "DISTRIB_ID=") === 0) {
        $id = trim(str_replace(["DISTRIB_ID='", "'"], "", $line));
    }
    if (strpos($line, "DISTRIB_RELEASE=") === 0) {
        $version = trim(str_replace(["DISTRIB_RELEASE='", "'"], "", $line));
    }
}

if (file_exists("/usr/bin/cpustat") && is_executable("/usr/bin/cpustat")) {
    $time = shell_exec("/usr/bin/cpustat -u");
    $load = shell_exec("/usr/bin/cpustat -l");
} else {
    $uptimeString = shell_exec('uptime | tr -d \',\'');

    preg_match("/up\s+(.+?),/", $uptimeString, $uptimeMatches);
    $time = $uptimeMatches[1];

    preg_match("/average:\s+(.+)/", $uptimeString, $loadMatches);
    $load = $loadMatches[1];

    if (strpos($time, "h") !== false) {
        $time = trim($time);
    } elseif (strpos($time, "day") !== false) {
        preg_match("/(\d+) day/", $time, $daysMatches);
        $days = $daysMatches[1] . "d ";
        $time = preg_replace("/\d+ day\s+/", "", $time);
        $time = $days . str_replace(":", "h ", trim($time));
    }
}

$model = htmlspecialchars($model);
$uptime = htmlspecialchars($time);
$load = htmlspecialchars($load);
$board = 'AMLogic';
?> 
