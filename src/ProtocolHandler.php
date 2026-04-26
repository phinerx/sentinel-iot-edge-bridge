<?php

namespace Sentinel\Edge;

/**
 * Handles low-latency ingestion of heterogeneous sensor telemetry.
 */
class ProtocolHandler
{
    private \Redis $cache;

    public function __construct(string $host, int $port)
    {
        $this->cache = new \Redis();
        $this->cache->connect($host, $port);
    }

    public function processTelemetry(string $payload): bool
    {
        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        
        if (!isset($data['sensor_id'], $data['timestamp'])) {
            throw new \InvalidArgumentException('Malformed telemetry packet received.');
        }

        return $this->cache->lPush('telemetry_buffer', json_encode($data));
    }
}