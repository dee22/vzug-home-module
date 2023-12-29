<?php
require_once(__DIR__ . '/NetworkScanner.php');

$networkScanner = new NetworkScanner();
$latency = $networkScanner->isPortOpen($ip, $port, $timeout);
if ($latency !== null) {
    NetworkScanner::addHost('', $latency);
}
return $latency;
