<?php

require_once(__DIR__ . '/AndoraWashAPI.php');

class NetworkScanner {
    use AndoraWashAPI;
    public int $port = 80;
    public static array $onlineHosts = [];
    public static string $persistentFile = __DIR__ . '/NetworkScanner.json';

    public function setPort(int $port): void {
        $this->$port = $port;
    }

    public static function addHost(string $ip, float $latency): void {
        $persistent = self::getHosts();
        $persistent[] = (object)['ip' => $ip, 'latency' => $latency];
        file_put_contents(NetworkScanner::$persistentFile, json_encode($persistent));
    }

    public static function getHosts(): array {
        if (!file_exists(NetworkScanner::$persistentFile)) {
            file_put_contents(NetworkScanner::$persistentFile, json_encode([]));
        }
        $persistent = json_decode(file_get_contents(NetworkScanner::$persistentFile));
        return is_array($persistent) ? $persistent : [];
    }

    public static function usePersistentStore(bool $create = true) {
        if ($create) {
            file_put_contents(NetworkScanner::$persistentFile, json_encode([]));
        } else {
            unlink(NetworkScanner::$persistentFile);
        }
    }

    public static function isPortOpen(string $ip, int $port = 80, int $timeout = 100): float | null {
        $t = round($timeout / 1000, 4);
        $s = microtime(true);
        $connection = @fsockopen($ip, $port, $errno, $errstr, $t);
        $latency = round((microtime(true) - $s) * 1000, 4);
        if (is_resource($connection)) {
            fclose($connection);
            return $latency;
        }
        return null;
    }

    public static function PingHost(string $ip, string $port, string $timeout): float | null {
        $latency = NetworkScanner::isPortOpen($ip, $port, $timeout);
        if ($latency !== null) {
            NetworkScanner::addHost($ip, $latency);
        }
        return $latency;
    }

    public function scanRange(string $startIP, string $endIP, int $threads = 10): array {
        NetworkScanner::usePersistentStore(true);
        $ipRange = range(ip2long($startIP), ip2long($endIP));
        foreach ($ipRange as $idx => $ip) {
            $ip = long2ip($ip);
            $port = $this->port;
            $timeout = 100;
            if ($threads <= 1) {
                NetworkScanner::PingHost($ip, $port, $timeout);
            } else {
                $code = "require_once('" . __DIR__ . "/NetworkScanner.php');\n";
                $code .= "return NetworkScanner::PingHost('$ip', $port, $timeout);\n";
                if (($idx === 0 || $idx % $threads) && $idx < count($ipRange) - 1) {
                    $res = IPS_RunScriptText($code);
                } else {
                    $res = IPS_RunScriptTextWait($code);
                }
            }
        }
        $ret = $this->getHosts();
        NetworkScanner::usePersistentStore(false);
        return $ret;
    }

    public function getLocalSubnetInfo(): array {
        $interfaces = net_get_interfaces();
        foreach ($interfaces as $key => $interface) {
            if (
                $interface['up'] &&
                !$this->startsWith($key, 'lo') &&
                (!isset($interface['description']) ||
                    stripos($interface['description'], 'Loopback') === false) &&
                isset($interface['unicast'][1]) &&
                isset($interface['unicast'][1]['netmask']) &&
                $this->isPrivateIP($interface['unicast'][1]['address'])
            ) {
                $ipAddress = $interface['unicast'][1]['address'];
                $subnetMask = $interface['unicast'][1]['netmask'];
                return $this->getNetworkInfo($ipAddress, $subnetMask);
            }
        }
        return [];
    }

    private function getNetworkInfo(string $subnetIP, string $subnetMask): array {
        $subnetIPInt = ip2long($subnetIP);
        $subnetMaskInt = ip2long($subnetMask);
        $networkAddress = $subnetIPInt & $subnetMaskInt;
        $broadcastAddress = $networkAddress | ~$subnetMaskInt;
        $gatewayAddress = $networkAddress + 1;
        $networkIP = long2ip($networkAddress);
        $broadcastIP = long2ip($broadcastAddress);
        $gatewayIP = long2ip($gatewayAddress);
        return [
            'hostIP' => $subnetIP,
            'networkIP' => $networkIP,
            'broadcastIP' => $broadcastIP,
            'gatewayIP' => $gatewayIP,
            'subnetMask' => $subnetMask,
        ];
    }


    private function isPrivateIP(string $ip): bool {
        $privateCIDRs = [
            '192.168.0.0/16', '172.16.0.0/12', '10.0.0.0/8', '100.64.0.0/10'
        ];
        $checkResults = array_map(fn ($cidr) => $this->ipCIDRCheck($ip, $cidr), $privateCIDRs);
        return (bool)array_sum($checkResults);
    }

    private function ipCIDRCheck(string $ip, string $cidr): bool {
        list($net, $mask) = explode('/', $cidr);
        $ip_net = ip2long($net);
        $ip_mask = ~((1 << (32 - $mask)) - 1);
        $ip_ip = ip2long($ip);
        return (($ip_ip & $ip_mask) == ($ip_net & $ip_mask));
    }

    // Helper Functions

    private function startsWith($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
