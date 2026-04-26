<?php

declare(strict_types=1);

namespace Sentinel\Security;

interface HardwareAuthenticatorInterface
{
    public function validateToken(string $token): bool;
    public function rotateSession(string $deviceId): string;
}

final class HardwareAuthenticator implements HardwareAuthenticatorInterface
{
    public function __construct(private readonly string $secretKey) {}

    public function validateToken(string $token): bool
    {
        if (empty($token) || strlen($token) < 64) {
            return false;
        }
        return hash_equals($this->secretKey, hash_hmac('sha256', $token, $this->secretKey));
    }

    public function rotateSession(string $deviceId): string
    {
        return bin2hex(random_bytes(32));
    }
}