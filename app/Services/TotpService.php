<?php

namespace App\Services;

class TotpService
{
    /**
     * Standard RFC 4648 Base32 alphabet.
     */
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new random Base32 secret key.
     */
    public static function generateSecret(int $length = 16): string
    {
        $secret = '';
        $charsLen = strlen(self::BASE32_CHARS);
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $charsLen - 1)];
        }
        return $secret;
    }

    /**
     * Decode a Base32 string into binary bytes.
     */
    public static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(str_replace([' ', '-'], '', $b32));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) {
                continue; // Ignore invalid / padding characters
            }
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Calculate a 6-digit TOTP code for a secret and time slice (RFC 6238).
     */
    public static function getCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = (int) floor(time() / 30);
        }

        $secretBinary = self::base32Decode($secret);
        // Pack timeSlice into 64-bit big-endian binary
        $timePacked = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $timePacked, $secretBinary, true);

        $offset = ord($hash[19]) & 0x0F;
        $truncatedHash = (
            ((ord($hash[$offset + 0]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $code = $truncatedHash % 1000000;
        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify user code against secret with time window tolerance.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $currentTimeSlice = (int) floor(time() / 30);
        $code = trim(str_replace([' ', '-'], '', $code));

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the standard otpauth:// URI for QR Code scanning.
     * Compatible with Google Authenticator, Microsoft Authenticator, Huawei AppGallery Authenticators, etc.
     */
    public static function getQrUri(string $company, string $userEmail, string $secret): string
    {
        $label = rawurlencode($company) . ':' . rawurlencode($userEmail);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $company,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * Generate emergency recovery codes (e.g. 8 codes of 10 alphanumeric chars).
     */
    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $raw = bin2hex(random_bytes(5)); // 10 chars
            $codes[] = substr($raw, 0, 5) . '-' . substr($raw, 5, 5);
        }
        return $codes;
    }
}
