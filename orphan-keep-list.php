<?php

require_once 'wp-load.php';


/**
 * =========================================================
 * SECURITY
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
 * KEEP STORAGE
 * =========================================================
 */

$keep_dir = WP_CONTENT_DIR . '/uploads/wp-cleanup';

$keep_file = $keep_dir . '/keep-ids.json';


/**
 * =========================================================
 * LOAD KEEP IDS
 * =========================================================
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

?>

<!DOCTYPE html>
<html lang="ru">

<head>

	<meta charset="UTF-8">

	<title>

		Keep List

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

		.preview {
			width: 100px;
			height: auto;
		}
	</style>

</head>

<body>

	<h1>KEEP LIST</h1>

	<p>

		<a href="orphan-unattached-images.php">

			← Вернуться к cleaner

		</a>

	</p>

	<p>

		<a href="orphan-keep-reset.php">

			Очистить keep list

		</a>

	</p>

	<?php if (empty($keep_ids)): ?>

		<h2>Keep list пуст.</h2>

	<?php else: ?>

		<table>

			<thead>

				<tr>

					<th>ID</th>

					<th>Preview</th>

					<th>Название</th>

					<th>Файл</th>

				</tr>

			</thead>

			<tbody>

				<?php foreach ($keep_ids as $image_id): ?>

					<?php

					$post = get_post($image_id);

					if (!$post) {
						continue;
					}

					$image_url = wp_get_attachment_url($image_id);

					$file_path = get_attached_file($image_id);

					?>

					<tr>

						<td>

							<?php echo intval($image_id); ?>

						</td>

						<td>

							<?php if ($image_url): ?>

								<img
									src="<?php echo esc_url($image_url); ?>"
									class="preview">

							<?php endif; ?>

						</td>

						<td>

							<?php echo esc_html($post->post_title); ?>

						</td>

						<td>

							<?php echo esc_html($file_path); ?>

						</td>

					</tr>

				<?php endforeach; ?>

			</tbody>

		</table>

	<?php endif; ?>

</body>

</html>