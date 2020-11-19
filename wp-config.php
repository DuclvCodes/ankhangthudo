<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'homeo' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '8GL$-kTroYoVTt?3Y:}eA^L%;b7|?fBCV-pO8:Ye~~. ~%c0o[v7nm7,`@V$YR,d' );
define( 'SECURE_AUTH_KEY',  '}}YIwL`@XHM(O-CUQ3r4CRGP#GDlA%8xvz]=2@I&_*8fwMUy5G~Orch7bk{KIGx_' );
define( 'LOGGED_IN_KEY',    'D?^gsA.?3|o*uv3snX@,%[d<C.uAZkf#]kbJ%N0eN4}xDv`BQz_{@|HHUTyVxV%>' );
define( 'NONCE_KEY',        'Ct46_u3$,(IneaXq-FdAY@Q]x2X}}[r@;kt6~Y@re<-^dx<J5PWd[5A~;(Y@5u@z' );
define( 'AUTH_SALT',        '2V5m*l|}|]2A;%2f.(b},:Pjc1;yu,MA9gsE;:-piP{7hgO0hMO;0fs9hmZ8:^tC' );
define( 'SECURE_AUTH_SALT', 'VO:l NvBG|QOv*x7Q|ytf8aptCLs` zo7-*,9s7E;<xkzjBzM5FI~DE !,yHg=BV' );
define( 'LOGGED_IN_SALT',   'Ght;Nnic8$Ch97nE#g~_D>(.?5;2JtTG{wr4bfz$,<!Viwix)o,U_I#=}^kX3/9$' );
define( 'NONCE_SALT',       'VB./w!^FyvF6L:DymRrL-#R[y`Z55u^t:^mQiC>)sB$LB:VE>-pm]dC,BgC0_+|2' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
