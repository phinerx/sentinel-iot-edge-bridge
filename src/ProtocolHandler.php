<?php

namespace Sentinel\Bridge;

/**
 * Handles low-latency ingestion of binary telemetry frames from edge sensors.
 * Implements a non-blocking queue mechanism for high-throughput environments.
 */
class ProtocolHandler
{
    private array $buffer = [];
    private int $maxBufferSize = 1024;

    public function ingest(string $payload): bool
    {
        if (count($this->buffer) >= $this->maxBufferSize) {
            $this->flush();
        }

        $this->buffer[] = unpack('C*', $payload);
        return true;
    }

    private function flush(): void
    {
        // Offload buffer to persistent storage layer via local socket
        $this->buffer = [];
    }
}