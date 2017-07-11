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
define('DB_NAME', 'chewytes_ss_dbname194');

/** MySQL database username */
define('DB_USER', 'chewytes_ss_d194');

/** MySQL database password */
define('DB_PASSWORD', 'YJl6RnNJiCpL');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY', '@FM;oc_Z&GuOr_(<[<%k}JXXnbmjSGk?uhK_iVzv*gPdSbXj?Akzj;qA-x{yadTrJY-={|FVl|Ox_ZjrjhvNMxCBV$*|(*Rjk/*Ph_a-msE{Hs}NlXRgDbKDZOtC?*Eu');
define('SECURE_AUTH_KEY', 'z=EOQj?gcfS+SSp^))&$$fTroA{FaRv$PzJ^HOc[dKov%C_Duz+V!oRP=S*NS)_]w}Gn_tO+S]kgFe/jTCY$|FR]{@+<K-TtkJvPUtMlmnFWCPmiw@?S{fu<!u%cK(O$');
define('LOGGED_IN_KEY', 'nC&*lE</WqripqhbVKWg{XQ@Xrl!-U[YsY&SZ}H(!hU$=V{PiU*h@)ab{Zj]&/PPLhPatz[*^I;uxKXFgg%vbp*f|anWr^vuZz=a+>>*DL+YQTEOG{u;TsKr[HSd[xyS');
define('NONCE_KEY', '^Lfj+/KX>@ARF-$FP(>%}kBfw(*TorQu-My-|}A+y!W+V[d]HYopXPS=ySS$%Lqc{X@IVqh/R[mdS+Z*$YRtA;a%_HpMTSCLS&frO)>;vGgIn&=T(QhfWrUd>zcL[OFw');
define('AUTH_SALT', 'l((eYe^hbq[*r@&a!>i<QLpy$XnKn-|ke$;t?wm&AaKE|NM>b[ig=bjNT|*Ypu{YLK+&N>_zo=cr{%Wbg=T]N]cni)aqmv$)wLZNI{()}QncEoHvomH*(yMqHh?e)eef');
define('SECURE_AUTH_SALT', 'xw/Fce{u+cZ@g?I$mZa]RJ/wTut$uU[UdVoQp=vrB_;vqT$ZXlSh-G?RYwJxWzwS|RPD*hi{oT}&H^UilrcJBaqBEa]Y)G-EQmN]}wTTY%Xr-LuRrUL^NMrUmC>?*_Vg');
define('LOGGED_IN_SALT', 'zm<QKdw*}--f*cLJf/vSZ/X^w_f{-{sr}t!zoG@dEX$}G>=-&+?_MQXpL(x]oTbnKSUeCIPjZ}qs>%k/VxAzU&o]]$ZudWBM(ySdXpgsATeS}t{yx>X|gYg*$H{<Y{rH');
define('NONCE_SALT', 'C^{bG%^=@H@dwLyiBT&<F&-Pgv**%>I=eG_/^CiZ?&;aq$Vfyj?OlV?&v-uXQF^^j@j]?EWIz-dlMjnG?{>ONSZG!!uTMWRGd&gkcHVHyotGaE$ov+qIlKI(}}F*JKtZ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_dwon_';

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
