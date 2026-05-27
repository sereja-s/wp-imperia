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
define('DB_NAME', 'wp-imperia');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
//define('DB_HOST', 'localhost');
define('DB_HOST', 'MySQL-8.0');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         'GC;>UnG4!gPEz;$1zZFI8=Ed#QqPsx@1*b2#Fr1@fNzG72-j}>cPCzRnFrlRkwSP');
define('SECURE_AUTH_KEY',  '$Pxl%5-xOJnh*]]1GRhQW2#wN&--!prg3jE`c~Yxu*S~<j`5P>KTIZQ}59|%;6^[');
define('LOGGED_IN_KEY',    'J<I3(G2U[G-TYC&}4^0_a.!>*+U]4],XS![x<<7wkbbBcs<?4PC~?1ng.:h<e;{a');
define('NONCE_KEY',        '=Y229,4??BrL%; eOLP_4$g=j0J2oc7@oc))K4LAtk*V/;|^Z0L=jq78u^L2s=.p');
define('AUTH_SALT',        '11-Vd}ahs9n@C.Dz{*>sQX Er#OL|wBSE;ljKJy;7_.M9hr?WU8pk7>MPu|{1y~#');
define('SECURE_AUTH_SALT', ': jx4{yhNA&kSxM$s gh2:kqmVLFt1>V}47;xoYoS,ZwYK5%z LtT6_XT!s?/r8.');
define('LOGGED_IN_SALT',   ' ,<Ua[o6uR8kqG7lWq!#WPj#jh1uL<ICxRcv]KM7kLOlP,dv].Vs,vQ0#_)tB(Y-');
define('NONCE_SALT',       '`3YeDLG VJz[5G-IDjtCqM4<wE/KA}ZWV6Ms-fP[1_d8ILX/7K,9Te2ay`}%kP=j');

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */

/**
 * Отключаем автоматические обновления
 */
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);


/**
 * Запрещаем встроенный редактор файлов
 */
//define('DISALLOW_FILE_EDIT', true);


/**
 * DEV домены
 */
$dev_hosts = [
	'imperia.local',
	'localhost',
];

/**
 * На production запрещаем файловые изменения, т.е. на боевом сервере запрещаем:
 * - установку плагинов
 * - обновление плагинов
 * - обновление тем
 * - обновление WordPress
 * - редактор файлов
 */
if (!in_array($_SERVER['HTTP_HOST'], $dev_hosts, true)) {

	define('DISALLOW_FILE_MODS', true);
}

//if ($_SERVER['HTTP_HOST'] !== 'imperia.local') {
//
//	define('DISALLOW_FILE_MODS', true);
//}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
