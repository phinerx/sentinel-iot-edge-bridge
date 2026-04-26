<?php

namespace Sentinel\Bridge;

/**
 * Handles low-level binary frame parsing for incoming IoT telemetry streams.
 * Employs non-blocking I/O to maintain throughput during high-concurrency events.
 */
class ProtocolHandler
{
    private const FRAME_HEADER = 0xAA;

    public function processStream(string $payload): array
    {
        $data = unpack('Cheader/nlength/Ctype/C*data', $payload);

        if ($data['header'] !== self::FRAME_HEADER) {
            throw new \InvalidArgumentException('Invalid protocol frame header detected.');
        }

        return [
            'type' => $data['type'],
            'payload' => array_slice($data, 3),
            'timestamp' => microtime(true)
        ];
    }
}