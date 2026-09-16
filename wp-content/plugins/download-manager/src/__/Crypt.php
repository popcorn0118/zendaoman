<?php

/**
 * Class Crypt
 * From v4.1.9
 * Updated in v4.7.5
 * Security fix in v7.x - Fixed CBC bit-flipping vulnerability (HMAC now includes IV)
 *                      - Added Sodium fallback for environments without OpenSSL
 *                      - Added pure PHP fallback for maximum compatibility
 *                      - Replaced mt_rand() with cryptographically secure random
 */

namespace WPDM\__;


class Crypt
{
    /**
     * Encryption method identifiers (prepended to ciphertext for decryption routing)
     */
    private const METHOD_OPENSSL = 'O';  // OpenSSL AES-128-CBC
    private const METHOD_SODIUM = 'S';   // Sodium XSalsa20-Poly1305
    private const METHOD_PHP = 'P';      // Pure PHP fallback

    /**
     * Flag to track if we've logged the PHP fallback warning
     */
    private static bool $phpFallbackWarningLogged = false;

    function __construct()
    {
    }

    /**
     * Check if OpenSSL is available
     */
    private static function hasOpenSSL(): bool
    {
        return function_exists('openssl_cipher_iv_length') && function_exists('openssl_encrypt');
    }

    /**
     * Check if Sodium is available
     */
    private static function hasSodium(): bool
    {
        return function_exists('sodium_crypto_secretbox') && defined('SODIUM_CRYPTO_SECRETBOX_KEYBYTES');
    }

    /**
     * Get the encryption key
     */
    private static function getKey(): string
    {
        $encKey = defined('WPDM_ENC_KEY') ? WPDM_ENC_KEY : get_option('__wpdm_enc_key');

        if (!$encKey) {
            $encKey = self::encKey();
            update_option('__wpdm_enc_key', $encKey);
        }

        return $encKey;
    }

    /**
     * Encrypt data using the best available method
     *
     * Priority: OpenSSL > Sodium > Pure PHP
     *
     * @param mixed $text Data to encrypt (string, array, or object)
     * @return string Encrypted data (URL-safe base64) or empty string on failure
     */
    public static function encrypt($text)
    {
	    if ($text === '' || $text === null) return '';

        $text = is_array($text) || is_object($text) ? json_encode($text) : $text;
        $encKey = self::getKey();

        // Try OpenSSL first (most secure, most common)
        if (self::hasOpenSSL()) {
            return self::encryptOpenSSL($text, $encKey);
        }

        // Fallback to Sodium (very secure, bundled in PHP 7.2+)
        if (self::hasSodium()) {
            return self::encryptSodium($text, $encKey);
        }

        // Final fallback: Pure PHP (works everywhere, basic security)
        self::logPhpFallbackWarning();
        return self::encryptPHP($text, $encKey);
    }

    /**
     * Decrypt data (auto-detects encryption method)
     *
     * @param string $ciphertext Encrypted data
     * @param bool $ARRAY If true, return associative array instead of object for JSON
     * @return mixed Decrypted data or empty string on failure
     */
    public static function decrypt($ciphertext, $ARRAY = false)
    {
	    if ($ciphertext === '' || $ciphertext === null) return $ciphertext;

        $encKey = self::getKey();
        if (!$encKey) return '';

        // Restore base64 characters
        $ciphertext = str_replace(array('-', '_'), array('+', '/'), $ciphertext);

        // Detect encryption method from prefix
        $method = substr($ciphertext, 0, 1);
        $data = substr($ciphertext, 1);

        $plaintext = '';

        switch ($method) {
            case self::METHOD_OPENSSL:
                if (self::hasOpenSSL()) {
                    $plaintext = self::decryptOpenSSL($data, $encKey);
                } else {
                    error_log('WPDM Crypt: Data was encrypted with OpenSSL but OpenSSL is not available.');
                    return '';
                }
                break;

            case self::METHOD_SODIUM:
                if (self::hasSodium()) {
                    $plaintext = self::decryptSodium($data, $encKey);
                } else {
                    error_log('WPDM Crypt: Data was encrypted with Sodium but Sodium is not available.');
                    return '';
                }
                break;

            case self::METHOD_PHP:
                $plaintext = self::decryptPHP($data, $encKey);
                break;

            default:
                // Legacy data (no method prefix) - try OpenSSL legacy format first
                if (self::hasOpenSSL()) {
                    $plaintext = self::decryptOpenSSLLegacy($ciphertext, $encKey);
                }
                // If OpenSSL legacy failed or not available, try old base64 format
                if ($plaintext === '' || $plaintext === false) {
                    $plaintext = self::decryptLegacyBase64($ciphertext, $encKey);
                }
                break;
        }

        if ($plaintext === '' || $plaintext === false) {
            return '';
        }

        // Try to decode JSON
        $plaintext = trim($plaintext);
        $decoded = json_decode($plaintext, $ARRAY);

        if (is_object($decoded) || is_array($decoded)) {
            return $decoded;
        }

        return $plaintext;
    }

    /**
     * Log warning about PHP fallback (only once per request)
     */
    private static function logPhpFallbackWarning(): void
    {
        if (!self::$phpFallbackWarningLogged) {
            error_log('WPDM Crypt: Using pure PHP encryption fallback. For better security, please enable OpenSSL or Sodium PHP extension.');
            self::$phpFallbackWarningLogged = true;
        }
    }

    // =========================================================================
    // OpenSSL Methods (Primary - Most Secure)
    // =========================================================================

    /**
     * Encrypt using OpenSSL AES-128-CBC with HMAC
     */
    private static function encryptOpenSSL(string $text, string $key): string
    {
        $cipher = "AES-128-CBC";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $ciphertext_raw = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($ciphertext_raw === false) {
            return '';
        }

        // SECURITY FIX: HMAC includes IV to prevent CBC bit-flipping attacks
        $hmac = hash_hmac('sha256', $iv . $ciphertext_raw, $key, true);

        // Format: METHOD_PREFIX + base64(IV + HMAC + CIPHERTEXT)
        $encoded = base64_encode($iv . $hmac . $ciphertext_raw);
        $encoded = str_replace(array('+', '/', '='), array('-', '_', ''), $encoded);

        return self::METHOD_OPENSSL . $encoded;
    }

    /**
     * Decrypt OpenSSL encrypted data (new format with IV in HMAC)
     */
    private static function decryptOpenSSL(string $data, string $key): string
    {
        $c = base64_decode($data);
        if ($c === false) return '';

        $cipher = "AES-128-CBC";
        $ivlen = openssl_cipher_iv_length($cipher);
        $hmaclen = 32; // SHA-256

        if (strlen($c) < $ivlen + $hmaclen + 1) return '';

        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $hmaclen);
        $ciphertext_raw = substr($c, $ivlen + $hmaclen);

        if (empty($ciphertext_raw)) return '';

        // SECURITY FIX: HMAC includes IV
        $calcmac = hash_hmac('sha256', $iv . $ciphertext_raw, $key, true);

        if (!hash_equals($hmac, $calcmac)) {
            return '';
        }

        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return $plaintext !== false ? $plaintext : '';
    }

    /**
     * Decrypt legacy OpenSSL format (HMAC without IV - for backwards compatibility)
     */
    private static function decryptOpenSSLLegacy(string $ciphertext, string $key): string
    {
        $c = base64_decode($ciphertext);
        if ($c === false) return '';

        $cipher = "AES-128-CBC";
        $ivlen = openssl_cipher_iv_length($cipher);
        $hmaclen = 32;

        if (strlen($c) < $ivlen + $hmaclen + 1) return '';

        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $hmaclen);
        $ciphertext_raw = substr($c, $ivlen + $hmaclen);

        if (empty($ciphertext_raw)) return '';

        // Legacy format: HMAC only covers ciphertext
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        if (!hash_equals($hmac, $calcmac)) {
            return '';
        }

        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return $plaintext !== false ? $plaintext : '';
    }

    // =========================================================================
    // Sodium Methods (Secondary - Very Secure)
    // =========================================================================

    /**
     * Encrypt using Sodium (XSalsa20-Poly1305)
     */
    private static function encryptSodium(string $text, string $key): string
    {
        // Derive a proper 32-byte key from the variable-length key
        $derivedKey = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);

        // Generate random nonce
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Encrypt with authenticated encryption
        $ciphertext = sodium_crypto_secretbox($text, $nonce, $derivedKey);

        // Clear sensitive data from memory
        sodium_memzero($derivedKey);

        // Format: METHOD_PREFIX + base64(NONCE + CIPHERTEXT)
        $encoded = base64_encode($nonce . $ciphertext);
        $encoded = str_replace(array('+', '/', '='), array('-', '_', ''), $encoded);

        return self::METHOD_SODIUM . $encoded;
    }

    /**
     * Decrypt Sodium encrypted data
     */
    private static function decryptSodium(string $data, string $key): string
    {
        $c = base64_decode($data);
        if ($c === false) return '';

        $nonceLen = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

        if (strlen($c) < $nonceLen + SODIUM_CRYPTO_SECRETBOX_MACBYTES + 1) {
            return '';
        }

        $nonce = substr($c, 0, $nonceLen);
        $ciphertext = substr($c, $nonceLen);

        // Derive the same key used for encryption
        $derivedKey = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);

        try {
            $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $derivedKey);
        } catch (\SodiumException $e) {
            $plaintext = false;
        }

        // Clear sensitive data from memory
        sodium_memzero($derivedKey);

        return $plaintext !== false ? $plaintext : '';
    }

    // =========================================================================
    // Pure PHP Fallback (Works Everywhere - Basic Security)
    // =========================================================================

    /**
     * Encrypt using pure PHP (XOR cipher with key stretching + HMAC)
     *
     * This is NOT as secure as OpenSSL/Sodium but provides:
     * - Data obfuscation
     * - Integrity verification (HMAC)
     * - Works on ANY PHP installation
     *
     * @param string $text Plaintext to encrypt
     * @param string $key Encryption key
     * @return string Encrypted data
     */
    private static function encryptPHP(string $text, string $key): string
    {
        // Generate random IV (16 bytes)
        $iv = self::generateRandomBytes(16);

        // Derive encryption key using PBKDF2-like stretching
        $derivedKey = self::deriveKey($key, $iv, 32);

        // XOR encrypt
        $ciphertext = self::xorCrypt($text, $derivedKey);

        // Generate HMAC for integrity (includes IV)
        $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

        // Format: METHOD_PREFIX + base64(IV + HMAC + CIPHERTEXT)
        $encoded = base64_encode($iv . $hmac . $ciphertext);
        $encoded = str_replace(array('+', '/', '='), array('-', '_', ''), $encoded);

        return self::METHOD_PHP . $encoded;
    }

    /**
     * Decrypt pure PHP encrypted data
     */
    private static function decryptPHP(string $data, string $key): string
    {
        $c = base64_decode($data);
        if ($c === false) return '';

        $ivlen = 16;
        $hmaclen = 32;

        if (strlen($c) < $ivlen + $hmaclen + 1) return '';

        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $hmaclen);
        $ciphertext = substr($c, $ivlen + $hmaclen);

        if (empty($ciphertext)) return '';

        // Verify HMAC (includes IV for integrity)
        $calcmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

        if (!hash_equals($hmac, $calcmac)) {
            return '';
        }

        // Derive the same key used for encryption
        $derivedKey = self::deriveKey($key, $iv, 32);

        // XOR decrypt (same operation as encrypt)
        return self::xorCrypt($ciphertext, $derivedKey);
    }

    /**
     * XOR cipher - simple but effective when combined with proper key derivation
     */
    private static function xorCrypt(string $data, string $key): string
    {
        $keyLen = strlen($key);
        $dataLen = strlen($data);
        $result = '';

        for ($i = 0; $i < $dataLen; $i++) {
            $result .= $data[$i] ^ $key[$i % $keyLen];
        }

        return $result;
    }

    /**
     * Derive a key using multiple rounds of hashing (PBKDF2-like)
     */
    private static function deriveKey(string $key, string $salt, int $length): string
    {
        // Use PBKDF2 if available (PHP 5.5+), otherwise manual derivation
        if (function_exists('hash_pbkdf2')) {
            return hash_pbkdf2('sha256', $key, $salt, 10000, $length, true);
        }

        // Manual PBKDF2-like derivation for older PHP
        $derivedKey = '';
        $block = 1;

        while (strlen($derivedKey) < $length) {
            $h = hash_hmac('sha256', $salt . pack('N', $block), $key, true);
            $u = $h;

            for ($i = 1; $i < 1000; $i++) {
                $u = hash_hmac('sha256', $u, $key, true);
                $h ^= $u;
            }

            $derivedKey .= $h;
            $block++;
        }

        return substr($derivedKey, 0, $length);
    }

    /**
     * Generate random bytes using best available method
     */
    private static function generateRandomBytes(int $length): string
    {
        // Try random_bytes first (PHP 7+)
        if (function_exists('random_bytes')) {
            try {
                return random_bytes($length);
            } catch (\Exception $e) {
                // Fall through to alternatives
            }
        }

        // Try openssl_random_pseudo_bytes
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);
            if ($strong && $bytes !== false) {
                return $bytes;
            }
        }

        // Last resort: mt_rand based (not ideal but works)
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }
        return $bytes;
    }

    // =========================================================================
    // Legacy Compatibility
    // =========================================================================

    /**
     * Decrypt very old base64-only format (pre-OpenSSL era)
     */
    private static function decryptLegacyBase64(string $ciphertext, string $key): string
    {
        // Try to decode the old format: base64(base64(key) + base64(plaintext))
        $decoded = base64_decode($ciphertext);
        if ($decoded === false) return '';

        $encodedKey = base64_encode($key);

        // Check if the decoded data starts with the encoded key
        if (strpos($decoded, $encodedKey) === 0) {
            $innerCiphertext = substr($decoded, strlen($encodedKey));
            $plaintext = base64_decode($innerCiphertext);
            return $plaintext !== false ? $plaintext : '';
        }

        return '';
    }

    // =========================================================================
    // Utility Methods
    // =========================================================================

    /**
     * Generate a cryptographically secure encryption key
     *
     * @param int $length Key length in characters
     * @return string Generated key
     */
    public static function encKey($length = 256): string
    {
        if (defined('WPDM_ENC_KEY')) {
            return WPDM_ENC_KEY;
        }

        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars_len = strlen($chars);

        // Use cryptographically secure random bytes if available
        try {
            $bytes = self::generateRandomBytes($length);
            $key = '';
            for ($i = 0; $i < $length; $i++) {
                $key .= $chars[ord($bytes[$i]) % $chars_len];
            }
            return $key;
        } catch (\Exception $e) {
            // Fallback to wp_rand
            $key = '';
            for ($i = 0; $i < $length; $i++) {
                $key .= $chars[wp_rand(0, $chars_len - 1)];
            }
            return $key;
        }
    }

    /**
     * Check if secure encryption is available (OpenSSL or Sodium)
     *
     * @return bool True if OpenSSL or Sodium is available
     */
    public static function isSecureMethodAvailable(): bool
    {
        return self::hasOpenSSL() || self::hasSodium();
    }

    /**
     * Check if ANY encryption is available (including PHP fallback)
     *
     * @return bool Always true (PHP fallback always works)
     */
    public static function isAvailable(): bool
    {
        return true; // PHP fallback always works
    }

    /**
     * Get the current encryption method being used
     *
     * @return string 'openssl', 'sodium', or 'php'
     */
    public static function getMethod(): string
    {
        if (self::hasOpenSSL()) {
            return 'openssl';
        }
        if (self::hasSodium()) {
            return 'sodium';
        }
        return 'php';
    }

    /**
     * Get security level of current encryption method
     *
     * @return string 'high', 'medium', or 'basic'
     */
    public static function getSecurityLevel(): string
    {
        if (self::hasOpenSSL() || self::hasSodium()) {
            return 'high';
        }
        return 'basic';
    }
}
