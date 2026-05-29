<?php

/**
 * =========================================================
 * WORDPRESS LOAD
 * =========================================================
 */

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
 * DATABASE
 * =========================================================
 */

global $wpdb;


/**
 * =========================================================
 * UPLOADS DIRECTORY
 * =========================================================
 *
 * basedir:
 * physical path
 *
 * baseurl:
 * browser URL
 */

$upload_dir = wp_get_upload_dir();

$base_dir = wp_normalize_path(
	$upload_dir['basedir']
);

$base_url = $upload_dir['baseurl'];


/**
 * =========================================================
 * CLEANUP STORAGE
 * =========================================================
 */

$cleanup_dir = $base_dir . '/wp-cleanup';

$keep_file = $cleanup_dir . '/keep-files.json';


/**
 * =========================================================
 * AUTO CREATE CLEANUP DIRECTORY
 * =========================================================
 */

if (!file_exists($cleanup_dir)) {

	wp_mkdir_p($cleanup_dir);
}


/**
 * =========================================================
 * LOAD KEEP FILES
 * =========================================================
 */

$keep_files = [];

if (file_exists($keep_file)) {

	$json = file_get_contents($keep_file);

	$decoded = json_decode($json, true);

	if (is_array($decoded)) {

		$keep_files = array_map(
			'wp_normalize_path',
			$decoded
		);
	}
}


/**
 * =========================================================
 * GET ALL ATTACHMENTS
 * =========================================================
 *
 * Получаем:
 * все attachment изображения.
 */

$attachments = $wpdb->get_results("
	SELECT ID

	FROM {$wpdb->posts}

	WHERE post_type = 'attachment'

	AND post_mime_type LIKE 'image/%'
");


/**
 * =========================================================
 * REGISTERED FILES
 * =========================================================
 *
 * Здесь будут:
 * - originals
 * - thumbnails
 * - generated sizes
 * - webp
 */

$registered_files = [];


/**
 * =========================================================
 * PROCESS ATTACHMENTS
 * =========================================================
 */

foreach ($attachments as $attachment) {

	$attachment_id = (int) $attachment->ID;


	/**
	 * =====================================================
	 * ORIGINAL FILE
	 * =====================================================
	 */

	$original = get_attached_file($attachment_id);

	if (!$original) {
		continue;
	}

	$original = wp_normalize_path($original);

	if (file_exists($original)) {

		$registered_files[] = $original;
	}


	/**
	 * =====================================================
	 * ORIGINAL WEBP
	 * =====================================================
	 */

	$original_webp = $original . '.webp';

	if (file_exists($original_webp)) {

		$registered_files[] = wp_normalize_path(
			$original_webp
		);
	}


	/**
	 * =====================================================
	 * ATTACHMENT METADATA
	 * =====================================================
	 */

	$meta = wp_get_attachment_metadata($attachment_id);

	if (
		empty($meta['sizes'])
		||
		!is_array($meta['sizes'])
	) {
		continue;
	}


	/**
	 * =====================================================
	 * BASE PATH
	 * =====================================================
	 */

	$base_path = dirname($original);


	/**
	 * =====================================================
	 * THUMBNAILS
	 * =====================================================
	 */

	foreach ($meta['sizes'] as $size) {

		if (empty($size['file'])) {
			continue;
		}

		$thumb = wp_normalize_path(
			$base_path . '/' . $size['file']
		);

		if (file_exists($thumb)) {

			$registered_files[] = $thumb;
		}


		/**
		 * =================================================
		 * WEBP VERSION
		 * =================================================
		 */

		$thumb_webp = $thumb . '.webp';

		if (file_exists($thumb_webp)) {

			$registered_files[] = wp_normalize_path(
				$thumb_webp
			);
		}
	}
}


/**
 * =========================================================
 * UNIQUE REGISTERED FILES
 * =========================================================
 */

$registered_files = array_unique($registered_files);


/**
 * =========================================================
 * SCAN UPLOADS
 * =========================================================
 */

$all_files = [];


/**
 * =========================================================
 * RECURSIVE ITERATOR
 * =========================================================
 */

$iterator = new RecursiveIteratorIterator(

	new RecursiveDirectoryIterator(
		$base_dir,
		FilesystemIterator::SKIP_DOTS
	)
);


/**
 * =========================================================
 * ALLOWED EXTENSIONS
 * =========================================================
 */

$allowed_extensions = [

	'jpg',
	'jpeg',
	'png',
	'gif',
	'webp',
	'avif'
];


/**
 * =========================================================
 * PROCESS FILES
 * =========================================================
 */

foreach ($iterator as $file) {

	if (!$file->isFile()) {
		continue;
	}


	/**
	 * =====================================================
	 * NORMALIZED PATH
	 * =====================================================
	 */

	$path = wp_normalize_path(
		$file->getPathname()
	);


	/**
	 * =====================================================
	 * ONLY INSIDE UPLOADS
	 * =====================================================
	 */

	if (
		strpos($path, $base_dir) !== 0
	) {
		continue;
	}


	/**
	 * =====================================================
	 * IGNORE wp-cleanup
	 * =====================================================
	 */

	if (
		strpos($path, '/wp-cleanup/') !== false
	) {
		continue;
	}


	/**
	 * =====================================================
	 * EXTENSION
	 * =====================================================
	 */

	$extension = strtolower(
		pathinfo($path, PATHINFO_EXTENSION)
	);

	if (
		!in_array(
			$extension,
			$allowed_extensions,
			true
		)
	) {
		continue;
	}


	/**
	 * =====================================================
	 * FILE EXISTS
	 * =====================================================
	 */

	if (!file_exists($path)) {
		continue;
	}

	$all_files[] = $path;
}


/**
 * =========================================================
 * UNIQUE FILES
 * =========================================================
 */

$all_files = array_unique($all_files);


/**
 * =========================================================
 * FIND ORPHAN FILES
 * =========================================================
 *
 * orphan =
 * file exists physically
 * but WordPress does not know it.
 */

$orphan_files = array_diff(
	$all_files,
	$registered_files,
	$keep_files
);


/**
 * =========================================================
 * SORT
 * =========================================================
 */

sort($orphan_files);


/**
 * =========================================================
 * TOTAL COUNT
 * =========================================================
 */

$total_files = count($all_files);

$total_orphans = count($orphan_files);


/**
 * =========================================================
 * PAGINATION
 * =========================================================
 */

$per_page = 50;

$show_files = array_slice(
	array_values($orphan_files),
	0,
	$per_page
);

?>

<!DOCTYPE html>
<html lang="ru">

<head>

	<meta charset="UTF-8">

	<title>

		Uploads Orphan Cleaner

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
			vertical-align: top;
		}

		th {
			background: #f2f2f2;
		}

		.preview {
			width: 140px;
			height: auto;
			display: block;
			border: 1px solid #ddd;
		}

		.warning {
			background: #fff3cd;
			border: 1px solid #ffe69c;
			padding: 15px;
			margin-bottom: 20px;
		}

		.delete-button {
			background: #d63638;
			color: #fff;
			border: none;
			padding: 14px 26px;
			font-size: 16px;
			cursor: pointer;
		}

		.path {
			font-size: 12px;
			word-break: break-all;
			max-width: 700px;
		}
	</style>

</head>

<body>

	<h1>UPLOADS ORPHAN CLEANER</h1>

	<div class="warning">

		<ul>

			<li>Сканируется весь uploads.</li>

			<li>Attachment files исключаются автоматически.</li>

			<li>WordPress thumbnails исключаются автоматически.</li>

			<li>WEBP versions исключаются автоматически.</li>

			<li>Показываются только orphan files.</li>

			<li>Снятая галочка = сохранить файл.</li>

			<li>Keep files больше не показываются.</li>

			<li>Удаление безопасно для WordPress.</li>

		</ul>

	</div>

	<p>

		<strong>Всего image files в uploads:</strong>

		<?php echo number_format($total_files); ?>

	</p>

	<p>

		<strong>Всего orphan files:</strong>

		<?php echo number_format($total_orphans); ?>

	</p>

	<form
		method="post"
		action="uploads-orphan-delete.php">

		<?php
		wp_nonce_field(
			'uploads_orphan_delete',
			'uploads_orphan_delete_nonce'
		);
		?>


		<?php foreach ($show_files as $file): ?>

			<input
				type="hidden"
				name="current_files[]"
				value="<?php echo esc_attr($file); ?>">

		<?php endforeach; ?>


		<table>

			<thead>

				<tr>

					<th>Preview</th>

					<th>Файл</th>

					<th>Удалить</th>

				</tr>

			</thead>

			<tbody>

				<?php foreach ($show_files as $file): ?>

					<?php

					/**
					 * =====================================================
					 * RELATIVE PATH
					 * =====================================================
					 */

					$relative = str_replace(
						$base_dir,
						'',
						$file
					);

					$relative = str_replace(
						'\\',
						'/',
						$relative
					);


					/**
					 * =====================================================
					 * IMAGE URL
					 * =====================================================
					 */

					$image_url = $base_url . $relative;

					?>

					<tr>

						<td>

							<img
								src="<?php echo esc_url($image_url); ?>"
								class="preview"
								loading="lazy">

						</td>

						<td class="path">

							<?php echo esc_html($file); ?>

						</td>

						<td>

							<input
								type="checkbox"
								name="delete_files[]"
								value="<?php echo esc_attr($file); ?>"
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
				onclick="return confirm('Удалить выбранные orphan files?');">

				Удалить выбранные orphan files

			</button>

		</div>

	</form>

</body>

</html>
```