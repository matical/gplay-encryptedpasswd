<?php

namespace ksmz\GPEncryptedPasswd;

use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;
use ParagonIE\HiddenString\HiddenString;

class Encrypt
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var \phpseclib\Crypt\RSA */
    protected $rsa;

    /** @var string */
    protected $payloadFormat = "%s\u{0000}%s";

    /** @var string */
    const GOOGLE_DEFAULT_PUBLIC_KEY = 'AAAAgMom/1a/v0lblO2Ubrt60J2gcuXSljGFQXgcyZWveWLEwo6prwgi3iJIZdodyhKZQrNWp5nKJ3srRXcUW+F1BD3baEVGcmEgqaLZUNBjm057pKRI16kB0YppeGx5qIQ5QjKzsR8ETQbKLNWgRY0QRNVz34kMJR3P/LgHax/6rmf5AAAAAwEAAQ==';

    /**
     * @param string                               $email    Gmail Address
     * @param \ParagonIE\HiddenString\HiddenString $password Gmail Password
     */
    public function __construct(string $email, HiddenString $password)
    {
        $this->email = $email;
        $this->password = $password->getString();
    }

    /**
     * @return string
     */
    public function encrypt()
    {
        [$modulus, $exponent] = $this->decomposeKey($this->hexKey());
        $this->initRSA($modulus, $exponent);

        $encrypted = $this->rsa->encrypt(
            sprintf($this->payloadFormat, $this->email, $this->password)
        );
        $finalPayload = hex2bin($this->signature() . bin2hex($encrypted));
        $encryptedPasswd = $this->safeBase64($finalPayload);

        return $encryptedPasswd;
    }

    /**
     * @return string
     */
    protected function hexKey()
    {
        return bin2hex($this->binaryKey());
    }

    /**
     * @return string
     */
    protected function binaryKey()
    {
        return base64_decode(self::GOOGLE_DEFAULT_PUBLIC_KEY);
    }

    /**
     * @param $modulus
     * @param $exponent
     */
    protected function initRSA($modulus, $exponent)
    {
        $this->rsa = new RSA();
        $this->rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_PKCS1_RAW);
        $this->rsa->loadKey(["modulus" => $modulus, "exponent" => $exponent]);
        $this->rsa->setPublicKey();
    }

    /**
     * @param $binaryKey
     * @return array
     */
    protected function decomposeKey($binaryKey)
    {
        $firstSegment = substr($binaryKey, 8, 256);
        $modulus = $this->toBigInt($firstSegment);

        $secondSegment = substr($binaryKey, 256 + 16, 6);
        $exponent = $this->toBigInt($secondSegment);

        return [$modulus, $exponent];
    }

    /**
     * @param string $segment
     * @return \phpseclib\Math\BigInteger
     */
    protected function toBigInt($segment)
    {
        return new BigInteger(hex2bin($segment), 256);
    }

    /**
     * @return string
     */
    protected function signature()
    {
        $digest = sha1($this->binaryKey(), true);
        $trimmed = substr($digest, 0, 4);

        return "00" . bin2hex($trimmed);
    }

    /**
     * @param $output
     * @return string
     */
    protected function safeBase64($output)
    {
        return str_replace(["+", "/"], ["-", "_"], base64_encode($output));
    }
}
