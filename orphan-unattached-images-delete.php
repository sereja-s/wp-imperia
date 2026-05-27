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
 *
 * Защита от CSRF
 */

if (
	!isset($_POST['safe_media_delete_nonce'])
	||
	!wp_verify_nonce(
		$_POST['safe_media_delete_nonce'],
		'safe_media_delete_action'
	)
) {
	wp_die('Ошибка проверки безопасности.');
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
 * AUTO CREATE DIRECTORY
 * =========================================================
 */

if (!file_exists($keep_dir)) {

	wp_mkdir_p($keep_dir);
}


/**
 * =========================================================
 * LOAD EXISTING KEEP IDS
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


/**
 * =========================================================
 * GET IDS FROM FORM
 * =========================================================
 *
 * delete_ids[]:
 * содержит только отмеченные checkbox
 *
 * Всё что НЕ отмечено:
 * считается keep images
 */

$delete_ids = [];

if (!empty($_POST['delete_ids'])) {

	$delete_ids = array_map(
		'intval',
		$_POST['delete_ids']
	);
}


/**
 * =========================================================
 * GET CURRENT BATCH IDS
 * =========================================================
 *
 * Чтобы определить:
 * какие checkbox были сняты
 */

$current_batch_ids = [];

if (!empty($_POST['current_batch_ids'])) {

	$current_batch_ids = array_map(
		'intval',
		$_POST['current_batch_ids']
	);
}


/**
 * =========================================================
 * DETECT KEEP IDS
 * =========================================================
 *
 * Всё что было показано,
 * но НЕ отмечено:
 * сохраняем в keep list
 */

$new_keep_ids = array_diff(
	$current_batch_ids,
	$delete_ids
);


/**
 * =========================================================
 * MERGE KEEP IDS
 * =========================================================
 */

$keep_ids = array_merge(
	$keep_ids,
	$new_keep_ids
);


/**
 * =========================================================
 * UNIQUE KEEP IDS
 * =========================================================
 */

$keep_ids = array_unique(
	array_map(
		'intval',
		$keep_ids
	)
);


/**
 * =========================================================
 * SAVE KEEP FILE
 * =========================================================
 */

file_put_contents(
	$keep_file,
	json_encode(
		array_values($keep_ids),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
	)
);


/**
 * =========================================================
 * DELETE COUNTERS
 * =========================================================
 */

$deleted = 0;

$failed = 0;


/**
 * =========================================================
 * OUTPUT
 * =========================================================
 */

echo '<h1>Результаты удаления</h1>';


/**
 * =========================================================
 * DELETE LOOP
 * =========================================================
 */

foreach ($delete_ids as $image_id) {

	/**
	 * =====================================================
	 * VALIDATE ATTACHMENT
	 * =====================================================
	 */

	$post = get_post($image_id);

	if (
		!$post
		||
		$post->post_type !== 'attachment'
	) {

		echo '<p style="color:red">';
		echo 'ID ' . $image_id . ' не attachment.';
		echo '</p>';

		$failed++;

		continue;
	}


	/**
	 * =====================================================
	 * DELETE ATTACHMENT
	 * =====================================================
	 *
	 * wp_delete_attachment():
	 * - удаляет attachment
	 * - удаляет postmeta
	 * - удаляет metadata
	 * - удаляет thumbnails
	 * - удаляет physical files
	 */

	$result = wp_delete_attachment(
		$image_id,
		true
	);


	/**
	 * =====================================================
	 * RESULT
	 * =====================================================
	 */

	if ($result) {

		$deleted++;
	} else {

		$failed++;

		echo '<p style="color:red">';
		echo 'Ошибка удаления ID: ' . $image_id;
		echo '</p>';
	}
}


/**
 * =========================================================
 * RESULTS
 * =========================================================
 */

echo '<hr>';

echo '<p><strong>Удалено:</strong> ';
echo $deleted;
echo '</p>';

echo '<p><strong>Ошибок:</strong> ';
echo $failed;
echo '</p>';

echo '<p><strong>Добавлено в keep list:</strong> ';
echo count($new_keep_ids);
echo '</p>';


/**
 * =========================================================
 * NAVIGATION
 * =========================================================
 */

echo '<p style="margin-top:30px;">';

echo '<a href="orphan-unattached-images.php">';

echo 'Продолжить очистку →';

echo '</a>';

echo '</p>';
