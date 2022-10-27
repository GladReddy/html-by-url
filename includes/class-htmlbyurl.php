<?php
/**
 * @since      1.0.0
 *
 * @package    HTML_by_URL
 * @subpackage HTML_by_URL/includes
 */

class HTMLBYURL {
	private static $_instance;

	public $path, $url, $table_name;

	public static function getInstance( $file = '' ) {
		if ( static::$_instance === null ) {
			static::$_instance = new static( $file );
		}

		return static::$_instance;
	}

	private function __construct() {
		$this->url  	  = untrailingslashit( plugin_dir_url( HTMLBYURL_FILE ) );
		$this->path 	  = untrailingslashit( plugin_dir_path( HTMLBYURL_FILE ) );
		$this->table_name = 'wp_html_by_url';

		$this->_modules();
	}

	private function _modules() {
		require __DIR__ . '/class-htmlbyurl-admin.php';
		$admin = new HTMLBYURL_Admin( $this );
		$admin->hooks();
	}

	public function hooks() {
		add_shortcode( 'htmlbyurl', [ $this, 'shortcode'] );
	}

	public function shortcode() {
		global $wpdb;
		$current_url = home_url( $_SERVER['REQUEST_URI'] );
		$html = $wpdb->get_var( $wpdb->prepare(
			"SELECT html FROM $this->table_name WHERE url = %s",
			$current_url
		) );

		return stripcslashes( $html );
	}

}
