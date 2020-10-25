<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

define ('WPLANG', 'zh_CN'); 

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'diancloud.cn');

/** MySQL database username */
define('DB_USER', 'diancloud.cn');

/** MySQL database password */
define('DB_PASSWORD', 'diancloud.cn');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY',         'Ly62oiel{ ONdZUmQ+Wv>B+FDklD~4(p8?X*!HE9N^0!(CJp6hH|;}pw5(Mk;T#Y');
define('SECURE_AUTH_KEY',  ' aZ]`+cs;{4nZXiDI]w&yeGL{S}c$jcZcj;`yVC(OA4e9MYfpNC8%mW VB-~LwVs');
define('LOGGED_IN_KEY',    '@>z#,!JVPendoJFd*G@*_^1zf|%PT4^iT@lZ)J4TAcx@-{}~n&];PcsPku7.Ppos');
define('NONCE_KEY',        'MBlSM%s,x0n>N/bl,5>CLsi0M4j]IUAN/4unA%xp+>L.bZ**[h+bV]{;ah9*NLO?');
define('AUTH_SALT',        'o5 }[^GL7ad/;*:<]glI*pd#f=l-@mb7Hs.F;G{J}@;w*OPIxngDAQi.{Gmces|1');
define('SECURE_AUTH_SALT', 'r%8Y7<XBf?2.fSTF/IqTupH{o;/D<@QT$8Pwe0$Cc&gP=E5x_7LK+uj>r]{[lz]6');
define('LOGGED_IN_SALT',   'wN#&DsI0~WS@f4EAB*`Uf+}!iv;%~En8ssR>5&.i?Ta)*Q/koQU4W~5I)1Q][6d>');
define('NONCE_SALT',       'wUjYZjpwkgY42$_>VN}rOcjQ-5;/7-R$0nbq0K%}ZxjBbUlYLpyD74M20i7{5N5i');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
