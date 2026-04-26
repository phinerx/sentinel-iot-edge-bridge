<?php

declare(strict_types=1);

namespace Sentinel\Security;

interface EncryptorInterface
{
    public function encrypt(string $data): string;
    public function decrypt(string $encryptedData): string;
}

/**
 * Implements production-grade encryption for IoT edge payloads.
 */
final class SecureDataEncryptor implements EncryptorInterface
{
    private const ALGO = 'aes-256-gcm';

    public function __construct(private readonly string $secretKey)
    {
        if (mb_strlen($this->secretKey, '8bit') !== 32) {
            throw new \InvalidArgumentException('Key must be exactly 32 bytes for AES-256.');
        }
    }

    public function encrypt(string $data): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::ALGO));
        $encrypted = openssl_encrypt($data, self::ALGO, $this->secretKey, OPENSSL_RAW_DATA, $iv, $tag);
        
        return base64_encode($iv . $tag . $encrypted);
    }

    public function decrypt(string $encryptedData): string
    {
        $raw = base64_decode($encryptedData);
        $ivLen = openssl_cipher_iv_length(self::ALGO);
        $iv = substr($raw, 0, $ivLen);
        $tag = substr($raw, $ivLen, 16);
        $ciphertext = substr($raw, $ivLen + 16);

        $decrypted = openssl_decrypt($ciphertext, self::ALGO, $this->secretKey, OPENSSL_RAW_DATA, $iv, $tag);
        
        if ($decrypted === false) {
            throw new \RuntimeException('Integrity check failed: Decryption failed.');
        }

        return $decrypted;
    }
}