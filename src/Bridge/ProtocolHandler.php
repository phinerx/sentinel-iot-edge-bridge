<?php

namespace Sentinel\Bridge;

use Sentinel\Contracts\ProtocolInterface;
use Sentinel\Exceptions\ValidationException;

/**
 * Handles low-latency message routing between hardware interfaces and the persistence layer.
 */
class ProtocolHandler implements ProtocolInterface
{
    private array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public function process(string $payload): bool
    {
        $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        if (!$this->validate($decoded)) {
            throw new ValidationException('Invalid sensor schema detected.');
        }

        return $this->dispatch($decoded);
    }

    private function validate(array $data): bool
    {
        return isset($data['sensor_id'], $data['timestamp'], $data['payload']);
    }

    private function dispatch(array $data): bool
    {
        // Logic for offloading to the message queue
        return true;
    }
}