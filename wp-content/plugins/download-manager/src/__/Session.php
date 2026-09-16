<?php
/**
 * Session Management Class
 *
 * Handles user session data with database or file-based storage.
 * Uses cookie-based device ID for session tracking.
 *
 * @package    WPDM
 * @subpackage Core
 * @author     WPDM Team
 * @since      4.7.9
 * @version    3.3.39
 *
 * @updated    2024-12-23
 * @changelog  Added function_exists() checks for WordPress functions
 *             Added fallbacks for early initialization scenarios
 *             Fixed namespace prefix for global PHP functions
 *             Improved compatibility with various server configurations
 */

namespace WPDM\__;

class Session
{
    private static $data = [];      // In-memory cache
    public static $deviceID = null;
    private static $store;
    private static $initialized = false;

    /**
     * Initialize session - call once per request
     */
    static function init()
    {
        if (self::$initialized) return;
        self::$initialized = true;

        // get_option should be available, but fallback to 'db' if not
        self::$store = \function_exists('get_option') ? \get_option('__wpdm_tmp_storage', 'db') : 'db';
        self::initDeviceID();

        if (self::$store === 'file') {
            self::loadFileSession();
            \register_shutdown_function([__CLASS__, 'saveSession']);
        }
		//wp_die(self::deviceID());
    }

    /**
     * Constructor for backward compatibility
     */
    function __construct()
    {
        self::init();
    }

    /**
     * Get or generate device ID - cookie-first approach
     */
    private static function initDeviceID()
    {
        // Check cached value
        if (self::$deviceID) return self::$deviceID;

        // Check existing cookie
        if (!empty($_COOKIE['__wpdm_client'])) {
            self::$deviceID = __::sanitize_var($_COOKIE['__wpdm_client'], 'alphanum');
            return self::$deviceID;
        }

        // Generate new ID (random is more reliable than IP+UA)
        // Use wp_generate_password if available, otherwise fallback to PHP random
        if (\function_exists('wp_generate_password')) {
            $deviceID = \wp_generate_password(32, false);
        } else {
            // Fallback for early initialization before WordPress is fully loaded
            $deviceID = \bin2hex(\random_bytes(16));
        }
        self::$deviceID = $deviceID;
        self::setDeviceCookie($deviceID);

        return self::$deviceID;
    }

    /**
     * Set device cookie with proper domain and security flags
     */
    private static function setDeviceCookie($deviceID)
    {
        if (\defined('WPDM_ACCEPT_COOKIE') && WPDM_ACCEPT_COOKIE === false) return;

        // Check if apply_filters is available (WordPress fully loaded)
        if (\function_exists('apply_filters') && !\apply_filters('wpdm_user_accept_cookies', true)) return;

        // Get domain - with fallback for early initialization
        if (\function_exists('home_url')) {
            $domain = \wp_parse_url(\home_url(), PHP_URL_HOST);
        } else {
            // Fallback: parse from server variables
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $domain = \preg_replace('/:\d+$/', '', $domain); // Remove port if present
        }

        $secure = \function_exists('is_ssl') ? \is_ssl() : (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        @\setcookie('__wpdm_client', $deviceID, 0, "/", $domain, $secure, true);
        $_COOKIE['__wpdm_client'] = $deviceID;
    }

    /**
     * Get or set device ID
     *
     * @param string|null $deviceID Optional device ID to set
     * @return string Current device ID
     */
    static function deviceID($deviceID = null)
    {
        if ($deviceID) {
            self::$deviceID = __::sanitize_var($deviceID, 'alphanum');
            self::setDeviceCookie(self::$deviceID);
        } elseif (!self::$deviceID) {
            self::init();
        }
        return self::$deviceID;
    }

    /**
     * Set session value - with in-memory caching
     *
     * @param string $name Session key
     * @param mixed $value Session value
     * @param int $expire Expiration time in seconds (default 30 minutes)
     */
    static function set($name, $value, $expire = 1800)
    {
        if (!$name) return;
        if (!self::$initialized) self::init();

        $expireTime = \time() + $expire;

        // Always cache in memory
        self::$data[$name] = ['value' => $value, 'expire' => $expireTime];

        if (self::$store === 'file') {
            // File storage saves on shutdown
            return;
        }

        // DB storage. REPLACE INTO only de-duplicates on a PRIMARY/UNIQUE
        // collision; (deviceID, name) is not unique here, so REPLACE would keep
        // inserting duplicate rows on every set(). Delete-then-insert (both
        // index-backed by `name_device`) guarantees a single row per key.
        global $wpdb;
        if ($value) {
            $wpdb->delete("{$wpdb->prefix}ahm_sessions", ['deviceID' => self::$deviceID, 'name' => $name]);
            $wpdb->insert("{$wpdb->prefix}ahm_sessions", [
                'deviceID'   => self::$deviceID,
                'name'       => $name,
                'value'      => \maybe_serialize($value),
                'lastAccess' => \time(),
                'expire'     => $expireTime,
            ]);
        } else {
            self::clear($name);
        }
    }

    /**
     * Get session value - with in-memory caching
     *
     * @param string $name Session key
     * @return mixed|null Session value or null if not found/expired
     */
    static function get($name)
    {
        if (!self::$initialized) self::init();

        // Check in-memory cache first
        if (isset(self::$data[$name])) {
            $cached = self::$data[$name];
            if ($cached['expire'] > \time()) {
                return $cached['value'];
            }
            // Expired - remove from cache
            unset(self::$data[$name]);
            return null;
        }

        if (self::$store === 'file') {
            return null; // File storage loads everything upfront
        }

        // DB storage - use prepared statement
        global $wpdb;
        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT value FROM {$wpdb->prefix}ahm_sessions
             WHERE deviceID = %s AND name = %s AND expire > %d",
            self::$deviceID, $name, \time()
        ));

        if ($value !== null) {
            $unserialized = \maybe_unserialize($value);
            // Cache for subsequent calls in same request
            self::$data[$name] = ['value' => $unserialized, 'expire' => \time() + 300];
            return $unserialized;
        }

        return null;
    }

    /**
     * Clear session data
     *
     * @param string $name Optional key to clear. Empty clears all.
     */
    static function clear($name = '')
    {
        if (!self::$initialized) self::init();

        global $wpdb;

        if ($name === '') {
            self::$data = [];
            if (self::$store !== 'file') {
                $wpdb->delete("{$wpdb->prefix}ahm_sessions", ['deviceID' => self::$deviceID]);
            }
        } else {
            unset(self::$data[$name]);
            if (self::$store !== 'file') {
                $wpdb->delete("{$wpdb->prefix}ahm_sessions", ['deviceID' => self::$deviceID, 'name' => $name]);
            }
        }
    }

    /**
     * Cleanup expired sessions - call via cron
     */
    static function cleanup()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_sessions WHERE expire < %d AND deviceID != 'alldevice'",
            \time()
        ));
    }

    /**
     * Reset all sessions except 'alldevice' temp rows and durable download-key rows
     */
    static function reset()
    {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}ahm_sessions WHERE deviceID NOT IN ('alldevice', '" . TempStorage::DURABLE_SCOPE . "')");
    }

    /**
     * Debug: Show session data
     */
    static function show()
    {
        wpdmprecho(self::$data);
    }

    /**
     * Load session data from file storage
     */
    private static function loadFileSession()
    {
        $file = WPDM_CACHE_DIR . "/session-" . self::$deviceID . ".txt";
        $realpath = \realpath($file);
        if ($realpath && \file_exists($realpath) && \substr_count($realpath, WPDM_CACHE_DIR)) {
            $data = \file_get_contents($realpath);
            $data = Crypt::decrypt($data, true);
            self::$data = \is_array($data) ? $data : [];
        }
    }

    /**
     * Save session data to file storage (called on shutdown)
     */
    static function saveSession()
    {
        if (self::$store !== 'file' || empty(self::$data)) return;

        // Filter out expired entries before saving
        $now = \time();
        self::$data = \array_filter(self::$data, function($v) use ($now) {
            return $v['expire'] > $now;
        });

        if (empty(self::$data)) return;

        if (!\file_exists(WPDM_CACHE_DIR)) {
            @\mkdir(WPDM_CACHE_DIR, 0755, true);
        }

        $data = Crypt::encrypt(self::$data);
        \file_put_contents(WPDM_CACHE_DIR . 'session-' . self::$deviceID . '.txt', $data);
    }
}
