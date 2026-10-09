<?php
/**
 * Gravitas Coaching theme bootstrap.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELEVORA_VERSION', '1.0.1' );
define( 'ELEVORA_DIR', get_template_directory() );
define( 'ELEVORA_URI', get_template_directory_uri() );

$elevora_includes = array(
	'/inc/setup.php',
	'/inc/post-types.php',
	'/inc/meta-fields.php',
	'/inc/template-functions.php',
	'/inc/blog.php',
	'/inc/query.php',
	'/inc/ajax.php',
	'/inc/shortcodes.php',
	'/inc/homepage.php',
	'/inc/customizer.php',
	'/inc/compatibility.php',
	'/inc/plugin-installer.php',
	'/inc/demo-import.php',
	'/inc/setup-wizard.php',
);

foreach ( $elevora_includes as $elevora_file ) {
	require_once ELEVORA_DIR . $elevora_file;
}
