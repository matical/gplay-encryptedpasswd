<?php

namespace ksmz\GPEncryptedPasswd;

use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;
use ParagonIE\HiddenString\HiddenString;
use UnexpectedValueException;

class Encrypt
{
    protected string $email;

    protected HiddenString $password;

    protected PublicKey $rsa;

    private const PAYLOAD_FORMAT = "%s\u{0000}%s";

    public const GOOGLE_DEFAULT_PUBLIC_KEY = 'AAAAgMom/1a/v0lblO2Ubrt60J2gcuXSljGFQXgcyZWveWLEwo6prwgi3iJIZdodyhKZQrNWp5nKJ3srRXcUW+F1BD3baEVGcmEgqaLZUNBjm057pKRI16kB0YppeGx5qIQ5QjKzsR8ETQbKLNWgRY0QRNVz34kMJR3P/LgHax/6rmf5AAAAAwEAAQ==';

    /**
     * @param  string  $email  Gmail Address
     * @param  \ParagonIE\HiddenString\HiddenString  $password  Gmail Password
     */
    public function __construct(string $email, HiddenString $password)
    {
        $this->email = $email;
        $this->password = $password;

        [$modulus, $exponent] = $this->decomposeKey($this->hexKey());
        $this->initRSA($modulus, $exponent);
    }

    /**
     * Generates a valid `EncryptedPasswd` with the given email and password.
     *
     * @return string
     */
    public function encrypt(): string
    {
        $encrypted = $this->rsa->encrypt(
            sprintf(self::PAYLOAD_FORMAT, $this->email, $this->password->getString())
        );
        $finalPayload = hex2bin($this->signature().bin2hex($encrypted));

        return $this->safeBase64($finalPayload);
    }

    /**
     * @param  \phpseclib3\Math\BigInteger  $modulus
     * @param  \phpseclib3\Math\BigInteger  $exponent
     * @return void
     *
     * @throws \UnexpectedValueException
     */
    protected function initRSA(BigInteger $modulus, BigInteger $exponent): void
    {
        $this->rsa = RSA::loadPublicKey([
            'e' => $exponent,
            'n' => $modulus,
        ]);

        if (! ($this->rsa instanceof RSA\PublicKey)) {
            throw new UnexpectedValueException("Expected phpspeclib to return a RSA\PublicKey instance");
        }
    }

    protected function hexKey(): string
    {
        return bin2hex($this->binaryKey());
    }

    protected function binaryKey(): string
    {
        return base64_decode(self::GOOGLE_DEFAULT_PUBLIC_KEY);
    }

    /**
     * @param  string  $binaryKey
     * @return array<\phpseclib3\Math\BigInteger>
     */
    protected function decomposeKey(string $binaryKey): array
    {
        $firstSegment = substr($binaryKey, 8, 256);
        $modulus = $this->toBigInt($firstSegment);

        $secondSegment = substr($binaryKey, 256 + 16, 6);
        $exponent = $this->toBigInt($secondSegment);

        return [$modulus, $exponent];
    }

    protected function toBigInt(string $segment): BigInteger
    {
        return new BigInteger(hex2bin($segment), 256);
    }

    protected function signature(): string
    {
        $digest = sha1($this->binaryKey(), true);
        $trimmed = substr($digest, 0, 4);

        return '00'.bin2hex($trimmed);
    }

    protected function safeBase64(string $output): string
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($output));
    }
}
