<?php
/**
 * @since      1.0.0
 *
 * @package    HTML_by_URL
 * @subpackage HTML_by_URL/includes
 */

class HTMLBYURL_Admin {
	public $path, $url, $table_name;

	public function __construct( $main_class ) {
		$this->path 	  = $main_class->path;
		$this->url  	  = $main_class->url;
		$this->table_name = $main_class->table_name;
	}

	public function hooks() {
		add_action( 'admin_init', [ $this, 'init' ] );
		add_action( 'admin_menu', [ $this, 'admin_page' ] );

		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_htmlbyurl_import', [ $this, 'import' ] );
		}
	}

	public function init() {
		if ( isset( $_COOKIE['htmlbyurl_success'] ) && $_COOKIE['htmlbyurl_success'] == '1' ) {
            add_action( 'admin_notices', [ $this, 'notice_import_success' ] );
            setcookie( 'htmlbyurl_success', null, -1, '/');
        }

		if ( isset( $_COOKIE['htmlbyurl_error'] ) && $_COOKIE['htmlbyurl_error'] == '1' ) {
            add_action( 'admin_notices', [ $this, 'notice_import_error' ] );
            setcookie( 'htmlbyurl_error', null, -1, '/');
        }
	}

	public function admin_page() {
		add_menu_page( 'HTML по URL', 'HTML по URL', 'manage_options', 'htmlbyurl', [ $this, 'render' ] );
	}

	function notice_import_error() {
        echo '<div class="notice notice-success is-dismissible"><p>Ошибка при импорте</p></div>';
    }

	function notice_import_success() {
		echo '<div class="notice notice-error is-dismissible"><p>Импорт завершён</p></div>';p
    }

	public function render() {
		require_once $this->path . '/templates/admin.php';
	}

	public function import() {
		global $wpdb;

		if ( ! check_admin_referer( 'htmlbyurl-import-key', 'htmlbyurl-import-nonce' ) )
			return;

		if ( ! ( isset( $_FILES ) && $_FILES['file']['tmp_name'] != '' ) )
			return;

		$ext = pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION );

		if ( $ext != 'csv' ) {
			setcookie( 'htmlbyurl_error', '1', time() + ( 5 * 60 ), '/' );
			wp_redirect( admin_url('admin.php?page=htmlbyurl') );
			exit;
		}

		$file_name 	   = $_FILES['file']['tmp_name'];
		$csvData 	   = file_get_contents( $file_name );
        $csvDelimiter  = ';';
        $csvLines      = str_getcsv( $csvData, "\n" );
        $linesToImport = [];

        foreach ( $csvLines as $line ) {
            $data = explode( $csvDelimiter, $line );

			if ( count( $data ) == 2 ) {
				$url  = esc_url( $data[0] );
				$html = esc_sql( $data[1] );

				if ( $url && $html )
					$linesToImport[] = [
						'url'  => $url,
						'html' => $html
					];
			}
        }

        if ( empty( $linesToImport ) ) {
			setcookie( 'htmlbyurl_error', '1', time() + ( 5 * 60 ), '/' );
			wp_redirect( admin_url('admin.php?page=htmlbyurl') );
			exit;
		}

		$chunks 	= array_chunk( $linesToImport, 10000 );
		$table_temp = "{$this->table_name}_temp";
		$sql 	    = "INSERT INTO `$table_temp` ( `url`, `html` ) VALUES ";

		$wpdb->query( 'CREATE TABLE ' . $table_temp  . ' LIKE ' . $this->table_name );

		foreach ( $chunks as $chunk ) {
			$placeholders = [];
			$data_values  = [];

			foreach ( $chunk as $key => $value ) {
				array_push( $data_values, $value['url'], $value['html'] );
				$placeholders[] = "('%s', '%s')";
			}
			$query = $sql . implode( ', ', $placeholders );
			$wpdb->query( $wpdb->prepare( $query, $data_values ) );
		}

		$wpdb->query( 'DROP TABLE ' . $this->table_name );
		$wpdb->query( 'RENAME TABLE ' . $table_temp . ' TO ' . $this->table_name );

		setcookie( 'htmlbyurl_success', '1', time() + ( 5 * 60 ), '/' );
		wp_redirect( admin_url('admin.php?page=htmlbyurl') );
		exit;
	}
}
