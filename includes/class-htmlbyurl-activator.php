<?php
/**
 * @since      1.0.0
 *
 * @package    HTML_by_URL
 * @subpackage HTML_by_URL/includes
 */

class HTMLBYURL_Activator {

	public static function activate() {
		global $wpdb;
		$db 	= $wpdb;
		$table  = HTMLBYURL::getInstance()->table_name;
		$charset_collate = $db->get_charset_collate();

		if ( $db->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE $table (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				url varchar(255) NOT NULL DEFAULT '',
				html longtext NOT NULL DEFAULT '',
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

}
