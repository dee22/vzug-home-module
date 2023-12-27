<?php

declare(strict_types=1);

trait AndoraWashAPI
{
    private function request($ip, $endpoint, $command)
    {
        $milliseconds = floor(microtime(true) * 1000);
        return file_get_contents("http://$ip/$endpoint?command=$command&_=$milliseconds");
    }

    public function getModelDescription(string $ip): string
    {
        return $this->request($ip, 'ai', 'getModelDescription');
    }
}
