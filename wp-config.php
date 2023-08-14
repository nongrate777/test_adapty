<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

$_SERVER['HTTPS'] = 'on';
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
define( 'AUTH_KEY',         '809bv,NJONGs{NpSKtcIt}(}Rgq<XA9-b[+W0&?(4jk0qTqh*_FDDH;,-^Z1:!08' );
define( 'SECURE_AUTH_KEY',  '42@,N)H(&hu[n4.pAz;Sb.J-+Tx$];^zbLFucp%Cx5Ysx?L:T#.JtUK?}&w{Z^kN' );
define( 'LOGGED_IN_KEY',    '^j2X r9pd FQu&x{848x,/UlU?yL(5) U:j;V;mK`tV=k;QO;:iGpR@X j:l3@O3' );
define( 'NONCE_KEY',        'z{E?sx[s:YKQ&ci5/b3au_UBqR&9zwl8m-1@SF2e5$77^PgJ=^:N>RAQQI).SRxx' );
define( 'AUTH_SALT',        'fv>X#d6okEsgb- acPBw^sm^|Td(;f#<r-yd/j!s4&@lAow21uD+rqDVoSO)5#O$' );
define( 'SECURE_AUTH_SALT', '8Lqa<D]c8f9f]$ke XKZj(-lUP^g?n!-(?4C86auIPw:lR,h?22g%t|7Ejw$/ZK%' );
define( 'LOGGED_IN_SALT',   'SYc]v_}A)*vXXNQ64Bs@Xb>_=^;5.`J]+Oi}i]}A-zN{RRy|Q|cgikZ><5L*#PYU' );
define( 'NONCE_SALT',       'P#{2D!jTkSBtBO$Ued(juMr bejF n+o7.dDD <NZ!wW[lZ`lanzSO$ au|px`mi' );

/* - - - - - deploy settings begin - - - - - */
$configs_file = ( file_exists( '/var/www/html/wp-globals.json' ) ) ? '/var/www/html/wp-globals.json' : __DIR__ .'/wp-globals-dev.json';
$configs = json_decode( file_get_contents( $configs_file ) );

$table_prefix = $configs->table_prefix;

define( 'WP_DEBUG', $configs->wp_debug );

define( 'DB_HOST', $configs->db_host );
define( 'DB_NAME', $configs->db_name );
define( 'DB_USER', $configs->db_user );
define( 'DB_PASSWORD', $configs->db_password );
define( 'WP_HOME', $configs->new_domain );
define( 'WP_SITEURL', $configs->new_domain );
define( 'API_PATH',    $configs->api_host );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'ASSETS_VERSION', time() );

define( 'WPMS_ON', true );
define( 'WPMS_SMTP_PASS', '63vBfMz02SlO37Oz' );

define( 'ACF_PRO_LICENSE', 'YzM1NjI5NDE3ODE5YTlkNDQ2NTczOGJlZDM1MTdmZWExZjRmMDUwNWQ3ODk5OTNiODA5MmQy');

define( 'WPMDB_LICENCE', 'c41e43c8-656e-42f4-8c92-098a417ab7e4' );
/* - - - - - deploy settings end - - - - - */

define( 'FORCE_SSL_ADMIN', true );
define( 'FORCE_SSL_LOGIN', false );
define( 'ALLOW_UNFILTERED_UPLOADS', true );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

require_once( ABSPATH . 'wp-settings.php' ); //Disable File Edits
//define( 'DISALLOW_FILE_EDIT', true );
