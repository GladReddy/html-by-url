<?php
/**
 * @since      1.0.0
 *
 * @package    HTML_by_URL
 * @subpackage HTML_by_URL/templates
 */
 ?>
 <div class="wrap">
	<h2><?php echo get_admin_page_title();?></h2>
	<h4>Требования к файлу:</h4>
	<ul>
		<li>Формат - <strong>CSV</strong></li>
		<li>Разделитель - <strong>точка с запятой (;)</strong></li>
		<li>Кодировка - <strong>UTF-8</strong></li>
		<li>Первый столбец - <strong>URL для поиска</strong>, второй <strong>HTML для вывода</strong></li>
		<li>Шорткод для вывода - <strong>[htmlbyurl]</strong></li>
	</ul>
	&nbsp;
	<form action="<?php echo admin_url( 'admin-ajax.php' );?>" method="post" enctype="multipart/form-data">
		<div><input required="" type="file" name="file" style="margin-bottom:20px" /></div>
		<?php wp_nonce_field( 'htmlbyurl-import-key', 'htmlbyurl-import-nonce' ); ?>
		<button class="button-primary" type="submit" name="action" value="htmlbyurl_import">Импорт</button>
	</form>
</div>
