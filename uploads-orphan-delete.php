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
 * NONCE SECURITY
 * =========================================================
 */

if (
	!isset($_POST['uploads_orphan_delete_nonce'])
	||
	!wp_verify_nonce(
		$_POST['uploads_orphan_delete_nonce'],
		'uploads_orphan_delete'
	)
) {
	wp_die('Ошибка безопасности.');
}


/**
 * =========================================================
 * UPLOADS
 * =========================================================
 */

$upload_dir = wp_get_upload_dir();

$base_dir = wp_normalize_path(
	$upload_dir['basedir']
);


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
 * GET CURRENT FILES
 * =========================================================
 */

$current_files = [];

if (!empty($_POST['current_files'])) {

	$current_files = array_map(
		'wp_normalize_path',
		array_values(
			array_filter($_POST['current_files'])
		)
	);
}


/**
 * =========================================================
 * GET DELETE FILES
 * =========================================================
 */

$delete_files = [];

if (!empty($_POST['delete_files'])) {

	$delete_files = array_map(
		'wp_normalize_path',
		array_values(
			array_filter($_POST['delete_files'])
		)
	);
}


/**
 * =========================================================
 * DETECT KEEP FILES
 * =========================================================
 */

$new_keep = array_diff(
	$current_files,
	$delete_files
);


/**
 * =========================================================
 * MERGE KEEP FILES
 * =========================================================
 */

$keep_files = array_merge(
	$keep_files,
	$new_keep
);

$keep_files = array_unique($keep_files);


/**
 * =========================================================
 * SAVE KEEP FILE
 * =========================================================
 */

file_put_contents(
	$keep_file,
	json_encode(
		array_values($keep_files),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
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
 * DELETE LOOP
 * =========================================================
 */

$deleted = 0;

$failed = 0;

foreach ($delete_files as $file) {

	/**
	 * =====================================================
	 * NORMALIZE
	 * =====================================================
	 */

	$file = wp_normalize_path($file);


	/**
	 * =====================================================
	 * ONLY INSIDE UPLOADS
	 * =====================================================
	 */

	if (
		strpos($file, $base_dir) !== 0
	) {

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * IGNORE wp-cleanup
	 * =====================================================
	 */

	if (
		strpos($file, '/wp-cleanup/') !== false
	) {

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * FILE EXISTS
	 * =====================================================
	 */

	if (!file_exists($file)) {

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * ONLY FILE
	 * =====================================================
	 */

	if (!is_file($file)) {

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * EXTENSION VALIDATION
	 * =====================================================
	 */

	$extension = strtolower(
		pathinfo($file, PATHINFO_EXTENSION)
	);

	if (
		!in_array(
			$extension,
			$allowed_extensions,
			true
		)
	) {

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * SAFE DELETE
	 * =====================================================
	 */

	if (@unlink($file)) {

		$deleted++;
	} else {

		$failed++;
	}
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>

	<meta charset="UTF-8">

	<title>

		Delete Results

	</title>

	<style>
		body {
			font-family: Arial;
			margin: 20px;
		}
	</style>

</head>

<body>

	<h1>Результаты удаления</h1>

	<p>

		<strong>Удалено файлов:</strong>

		<?php echo number_format($deleted); ?>

	</p>

	<p>

		<strong>Ошибок:</strong>

		<?php echo number_format($failed); ?>

	</p>

	<p>

		<strong>Добавлено в keep list:</strong>

		<?php echo number_format(count($new_keep)); ?>

	</p>

	<p style="margin-top:30px;">

		<a href="uploads-orphan-cleaner.php">

			Продолжить очистку →

		</a>

	</p>

</body>

</html>
```