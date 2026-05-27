<?php

/**
 * =========================================================
 * В начале авторизоваться на сайте, потом переййти по адресу: https://imperia.local/orphan-unattached-images.php
 * WORDPRESS LOAD
 * =========================================================
 */

require_once 'wp-load.php';


/**
 * =========================================================
 * SECURITY CHECK
 * =========================================================
 */

if (!is_user_logged_in()) {
	wp_die('Требуется авторизация.');
}

if (!current_user_can('manage_options')) {
	wp_die('Недостаточно прав.');
}


/**
 * =========================================================
 * DATABASE
 * =========================================================
 */

global $wpdb;


/**
 * =========================================================
 * KEEP STORAGE PATHS
 * =========================================================
 */

$keep_dir = WP_CONTENT_DIR . '/uploads/wp-cleanup';

$keep_file = $keep_dir . '/keep-ids.json';


/**
 * =========================================================
 * AUTO CREATE DIRECTORY
 * =========================================================
 */

if (!file_exists($keep_dir)) {

	wp_mkdir_p($keep_dir);
}


/**
 * =========================================================
 * LOAD KEEP IDS
 * =========================================================
 *
 * Загружаем ID изображений,
 * которые пользователь решил оставить.
 *
 * Эти изображения:
 * - больше НЕ показываются
 * - НЕ удаляются
 */

$keep_ids = [];

if (file_exists($keep_file)) {

	$json = file_get_contents($keep_file);

	$decoded = json_decode($json, true);

	if (is_array($decoded)) {

		$keep_ids = array_map(
			'intval',
			$decoded
		);
	}
}


/**
 * =========================================================
 * ITEMS PER PAGE
 * =========================================================
 */

$per_page = 50;


/**
 * =========================================================
 * EXCLUDE KEEP IDS
 * =========================================================
 *
 * Исключаем:
 * - keep изображения
 */

$exclude_sql = '';

if (!empty($keep_ids)) {

	$exclude_sql = '
		AND p.ID NOT IN (' . implode(',', $keep_ids) . ')
	';
}


/**
 * =========================================================
 * MAIN QUERY
 * =========================================================
 *
 * Показываем:
 * - attachment images
 *
 * Сортировка:
 * - от меньшего ID к большему
 *
 * Исключаем:
 * - keep ids
 *
 * Удалённые attachment:
 * автоматически исчезают,
 * потому что их уже нет в wp_posts
 */

$query = "
	SELECT
		p.ID,
		p.post_title,
		p.post_date

	FROM {$wpdb->posts} p

	WHERE p.post_type = 'attachment'

	AND p.post_mime_type LIKE 'image/%'

	{$exclude_sql}

	ORDER BY p.ID ASC

	LIMIT {$per_page}
";


/**
 * =========================================================
 * EXECUTE QUERY
 * =========================================================
 */

$results = $wpdb->get_results($query);


/**
 * =========================================================
 * COUNT REMAINING
 * =========================================================
 */

$count_query = "
	SELECT COUNT(*)

	FROM {$wpdb->posts} p

	WHERE p.post_type = 'attachment'

	AND p.post_mime_type LIKE 'image/%'

	{$exclude_sql}
";

$total_remaining = (int) $wpdb->get_var($count_query);

?>

<!DOCTYPE html>
<html lang="ru">

<head>

	<meta charset="UTF-8">

	<title>

		Media Cleaner

	</title>

	<style>
		body {
			font-family: Arial;
			margin: 20px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th,
		td {
			border: 1px solid #ccc;
			padding: 10px;
		}

		th {
			background: #f2f2f2;
		}

		.preview {
			width: 100px;
			height: auto;
		}

		.warning {
			background: #fff3cd;
			border: 1px solid #ffe69c;
			padding: 15px;
			margin-bottom: 20px;
		}

		.top-bar {
			margin-bottom: 20px;
		}

		.delete-button {
			background: #d63638;
			color: white;
			border: none;
			padding: 14px 26px;
			font-size: 16px;
			cursor: pointer;
		}
	</style>

</head>

<body>

	<h1>MEDIA CLEANER</h1>

	<div class="warning">

		<strong>Как работает система:</strong>

		<ul>

			<li>Показываются изображения из media library.</li>

			<li>Изображения показываются по 50 штук.</li>

			<li>Показ идёт от меньшего ID к большему.</li>

			<li>Удалённые изображения исчезают автоматически.</li>

			<li>Снятая галочка добавляет ID в keep-ids.json.</li>

			<li>Keep изображения больше не показываются.</li>

			<li>Удаление выполняется окончательно.</li>

		</ul>

	</div>

	<div class="top-bar">

		<p>

			<strong>Осталось изображений:</strong>

			<?php echo number_format($total_remaining); ?>

		</p>

		<p>

			<a href="orphan-keep-list.php">

				Открыть keep list

			</a>

		</p>

	</div>

	<?php if (empty($results)): ?>

		<h2>Изображения больше не найдены.</h2>

	<?php else: ?>

		<form
			method="post"
			action="orphan-unattached-images-delete.php">

			<?php
			wp_nonce_field(
				'safe_media_delete_action',
				'safe_media_delete_nonce'
			);
			?>


			<?php
			/**
			 * =========================================================
			 * HIDDEN CURRENT BATCH IDS
			 * =========================================================
			 *
			 * Передаём:
			 * все ID текущего batch.
			 *
			 * Это нужно:
			 * чтобы delete engine понял:
			 * - какие checkbox были сняты
			 * - какие изображения нужно добавить в keep list
			 */
			?>

			<?php foreach ($results as $image): ?>

				<input
					type="hidden"
					name="current_batch_ids[]"
					value="<?php echo intval($image->ID); ?>">

			<?php endforeach; ?>


			<table>

				<thead>

					<tr>

						<th>ID</th>

						<th>Preview</th>

						<th>Название</th>

						<th>Дата</th>

						<th>Файл</th>

						<th>Удалить</th>

					</tr>

				</thead>

				<tbody>

					<?php foreach ($results as $image): ?>

						<?php

						$image_url = wp_get_attachment_url($image->ID);

						$file_path = get_attached_file($image->ID);

						?>

						<tr>

							<td>

								<?php echo intval($image->ID); ?>

							</td>

							<td>

								<?php if ($image_url): ?>

									<img
										src="<?php echo esc_url($image_url); ?>"
										class="preview">

								<?php endif; ?>

							</td>

							<td>

								<?php echo esc_html($image->post_title); ?>

							</td>

							<td>

								<?php echo esc_html($image->post_date); ?>

							</td>

							<td>

								<?php echo esc_html($file_path); ?>

							</td>

							<td>

								<input
									type="checkbox"
									name="delete_ids[]"
									value="<?php echo intval($image->ID); ?>"
									checked>

							</td>

						</tr>

					<?php endforeach; ?>

				</tbody>

			</table>

			<div style="margin-top:30px;">

				<button
					type="submit"
					class="delete-button"
					onclick="return confirm('Удалить выбранные изображения?');">

					Удалить выбранные изображения

				</button>

			</div>

		</form>

	<?php endif; ?>

</body>

</html>