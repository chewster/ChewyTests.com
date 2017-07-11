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
define('DB_NAME', 'chewytes_ss_dbname59f');

/** MySQL database username */
define('DB_USER', 'chewytes_ss_d59f');

/** MySQL database password */
define('DB_PASSWORD', '9qgUuCopG3Jm');

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
define('AUTH_KEY', 'lPy$p{E_=QC@<bYK]^+h^*bSjNw^oUCd;p(DPU%sVFhhw=y<D>@l/QoTxhPE;[(kbaQmE|vtZ(_qmJz=upcK@UHBn=>;bFcGbP]W$V&zB/rG|qnt(EP;t+}Q]RwdTtv|');
define('SECURE_AUTH_KEY', '$OvTH)T=fodn-tLrBWz*XKEnKsCDZLd+m]toDE;E%VWpON)=I@+bGrm-vwHcy(lhr^_g|XzTM_h+McK&LMr=gomyg<o<xFG<Z)DB}ct;d-K]]hobG)^K^?Z^|$sdtNW-');
define('LOGGED_IN_KEY', ')kxIXn+jRyy*</gdD+jxIDIr$Tq=Bhp?{MuL]bBsS&O{D@BabBp$ezd-I&pOU>l{MWRu]ZWWF^A<^fNiSnKnl)YRji|)VFw{O@U(Mei%VFd{+Dn>L}_hfyuBNH;KCa|O');
define('NONCE_KEY', 'yGyd&jOaItBtgKhHx>/Fm;!*!ChYxFZ$$n^=Sx>R)d$Xs*A-brguxN_Wy]Pgpj%/eAZXfrL-tPgSJeVj)B^T-UK!CPJ[nHKux)!W]HJQ?($hh!$|Mk/h(a+B(B}SS]oO');
define('AUTH_SALT', 'slf}FenJ+uF+Oi&/wzp_&=Usqd$D-Pr*l(fLmS_N?&graxSLSDB(W)XJ*Gh@bGlLMY)-IH_aOLIB>Wv_d%b?<|J@(|);fqz>vSVyI?i-]PBUOVzzXcwZ-{udu^%/_Ae^');
define('SECURE_AUTH_SALT', 'gNc![+o^jZMVazb})w}-x^?c[YNbvtb?vN_Bqy^<GkPFZC/@$A_lyjX)fa?vibYIrvk^^QRd-C/rckzAbeE%lp@wOpVgQ{EwZpV=zj)[Dnd;M&XF@;*>Ahk%/VM_a-FD');
define('LOGGED_IN_SALT', ';&ix;wAAEZht=N?|y}M@}Eir&ZOx/pR$O>O)rpjTVmyb+HV%qR>F;w<RxWXWb$O?>?D+>kM;dYiw;D@NG_srvLA*%Uaz$FxbN$gx(@C{R{cCPPJrDP)>!o}tgc&DY=%=');
define('NONCE_SALT', 'yUl;OBr{%yP*%+!&/[zc[iCJ%s_]LH+f^agyVJ/z/|x^UZPN)X!)g)$uWj+<S!ctD**hkJ!h$VF>iw<VmV;e|RIt}R&]D{q[>>GKYyxR&ACVODTHtLyT%-UC/-uc?nMH');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_tpgo_';

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
