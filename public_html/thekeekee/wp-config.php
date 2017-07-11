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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'chewytes_theKeeKee');

/** MySQL database username */
define('DB_USER', 'chewytes_keekee');

/** MySQL database password */
define('DB_PASSWORD', 'ChewyCharla');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1_1(j7GYXfPl`uk`dq4ACQv=+d##P>RH{9`m/_#w#L4f[shWCG 17{A*j0i]O`ZP');
define('SECURE_AUTH_KEY',  '1N4)J^ZC4N(d((3<a(RT=qqr}Rdg &eIGt`E3ytYAloT]Ry>My{=}!X%RIkQ!$Tf');
define('LOGGED_IN_KEY',    'oDjAx{IGyv%rg[Lt N%&nYrE h2lw>GHn0Pa]-@f@cBton}>KRs$XwaiBQdCYjFq');
define('NONCE_KEY',        'v}d.q}7%TWp 4 `L#gMZIKc2+Y7>HU|Y4hou*q=YKJY[#W?B3 $SH AnVdU8|&v-');
define('AUTH_SALT',        'UFta;7BhN{LR9tFn~yEt:k8{!}p[O-Cez8dBRK6q{h3N2;2>`Vy*bfMtd5xQ34-B');
define('SECURE_AUTH_SALT', '%y3/?*(%LF0A2OGA%,0K</,}.PeeGlN<O^MZfZ%ry<;5|&48Aa2SrZH]AvRm]POw');
define('LOGGED_IN_SALT',   ':X46pu`$7U}llanwz~]72X3+DZ/&:8+e?NvTg7?Qj,IRkbzJ[SCp+d2N{T^VL}WP');
define('NONCE_SALT',       '%f_%I?ICY7BkRG3VGZ>whERZitj3vZEU83jH[uEC|^Z(2l,.@JluX7+BLBB$FNHm');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
