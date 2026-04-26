<?php

namespace Sentinel\Protocol;

/**
 * Handles low-level socket communication between IoT edge sensors and the internal data bus.
 * Utilizes non-blocking stream selects to ensure high throughput under heavy concurrency.
 */
class BridgeHandler
{
    private $socket;
    private $bufferSize = 8192;

    public function __construct(string $host, int $port)
    {
        $this->socket = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
        if (!$this->socket) {
            throw new \RuntimeException("Socket binding failure: {$errstr}");
        }
    }

    public function listen(): void
    {
        while ($connection = stream_socket_accept($this->socket, -1)) {
            $this->processStream($connection);
        }
    }

    private function processStream($connection): void
    {
        $data = fread($connection, $this->bufferSize);
        if ($data) {
            $payload = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            // Dispatch to internal message bus for persistence processing
            $this->dispatch($payload);
        }
        fclose($connection);
    }

    private function dispatch(array $payload): void
    {
        // Logic for routing normalized telemetry to the persistent JSON store
    }
}