<?php
/*
 * @since             1.0.0
 * @package           HTML_by_URL
 *
 * @wordpress-plugin
 * Plugin Name:       HTML by URL
 * Description:       Show custom HTML by URL
 * Version:           1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HTMLBYURL_FILE', __FILE__ );

require __DIR__ . '/includes/class-htmlbyurl.php';

register_activation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-htmlbyurl-activator.php';
	HTMLBYURL_Activator::activate();
});

add_action( 'plugins_loaded', function() {
	HTMLBYURL::getInstance()->hooks();
}, 0 );
