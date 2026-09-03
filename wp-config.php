<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'zendaoman' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost:8889' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'nbGGlA/4:#uhnr*d*XA]cWM#L(<h`a <H$SViIoXW{Gaku^q*FpXZ5hR:G424[UA' );
define( 'SECURE_AUTH_KEY',  '{gL!xpfUpJBH&)yf*1q.cb*V!,l~ bZ6]jNOV[^_8qE3N 2(HFrdc`ThfuX|m@62' );
define( 'LOGGED_IN_KEY',    'w^Hvl4!}M~$WWhssN?.IZ6DeOSjwBtUT@NWQYEyHO-;R{UCRSp^lDG!&w$TS(:2M' );
define( 'NONCE_KEY',        'yF)$E?!!x2)&s^0 %Lcw-J6K%lT+OT0b$J4U9|qQ7R/1Kl*kZg.{Lyl{v73uG|oi' );
define( 'AUTH_SALT',        '[=N4<#(XxjbP@@D>BKBwtA3n3.MFb?a)0L6JOV;`Lje;zk:~qS9aA}G^N}58Aa+{' );
define( 'SECURE_AUTH_SALT', 'AHvbsMA U<#4_Aysh8G>H@eb_F0[4?jR fSH|5d0[G`?LA>/UN:l!##.F6~k,Zr(' );
define( 'LOGGED_IN_SALT',   '?n#|j2,Lj]uv5y^fx69^c!wRjkf1rw/I05)1|=knt!4#w]6?Jd$rq8F4`#ch00oc' );
define( 'NONCE_SALT',       'd;]Jcyq`( 1}H1CJ.!ly2v*5k[X<IL,Izp2e4*jRM::@}X`+[{RC/2]XpOeWB Ez' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
