<?php

/**
 * Защита от прямого доступа к файлу
 */
if (! defined('ABSPATH')) {
	exit;
}

/**
 * URL fallback изображения
 *
 * Эта картинка будет подставляться,
 * если основное изображение:
 * - отсутствует
 * - удалено
 * - возвращает 404
 * - имеет пустой src
 */
function my_fallback_image_url()
{
	return get_stylesheet_directory_uri() . '/assets/img/no-photo.png';
}

/**
 * ALT текст fallback изображения
 */
function my_fallback_image_alt()
{
	return 'империя пола';
}

/**
 * Подключение JS fallback
 */
add_action('wp_enqueue_scripts', 'my_enqueue_image_fallback_script');

function my_enqueue_image_fallback_script()
{
	/**
	 * Регистрируем пустой script handle
	 *
	 * false = загрузка в <head>
	 *
	 * Это важно:
	 * listener начнёт работать
	 * ещё до загрузки изображений.
	 */
	wp_register_script(
		'my-image-fallback',
		'',
		[],
		null,
		false
	);

	/**
	 * Подключаем script
	 */
	wp_enqueue_script('my-image-fallback');

	/**
	 * Подготавливаем данные для JS
	 */
	$fallback = esc_url(my_fallback_image_url());
	$alt      = esc_js(my_fallback_image_alt());

	/**
	 * Inline JavaScript
	 */
	$inline_js = "
		/**
		 * Глобальные переменные fallback
		 */
		window.__IMG_FALLBACK__ = '{$fallback}';
		window.__IMG_FALLBACK_ALT__ = '{$alt}';

		/**
		 * Ловим ошибки загрузки изображений
		 *
		 * Срабатывает ТОЛЬКО если:
		 * - изображение не загрузилось
		 * - 404
		 * - битый URL
		 * - файл удалён
		 */
		document.addEventListener('error', function(e) {

			const img = e.target;

			/**
			 * Проверяем что ошибка относится к IMG
			 */
			if (!img || img.tagName !== 'IMG') {
				return;
			}

			/**
			 * Защита от бесконечного цикла
			 *
			 * Если fallback изображение
			 * тоже не загрузилось —
			 * повторную подмену НЕ делаем.
			 *
			 * Иначе был бы бесконечный loop:
			 *
			 * broken image →
			 * fallback →
			 * fallback broken →
			 * fallback →
			 * ...
			 */
			if (img.src === window.__IMG_FALLBACK__) {
				return;
			}

			/**
			 * Подменяем изображение
			 */
			img.src = window.__IMG_FALLBACK__;

			/**
			 * Подменяем ALT
			 */
			img.alt = window.__IMG_FALLBACK_ALT__;

		}, true);

		/**
		 * Проверка изображений с пустым src
		 *
		 * Срабатывает один раз
		 * после построения DOM.
		 */
		document.addEventListener('DOMContentLoaded', function() {

			/**
			 * Перебираем только IMG элементы
			 */
			document.querySelectorAll('img').forEach(function(img) {

				const src = img.getAttribute('src');

				/**
				 * Если src отсутствует
				 * или пустой
				 */
				if (
					src === null ||
					src.trim() === ''
				) {

					/**
					 * Подставляем fallback
					 */
					img.src = window.__IMG_FALLBACK__;

					/**
					 * Подставляем ALT
					 */
					img.alt = window.__IMG_FALLBACK_ALT__;
				}

			});

		});
	";

	/**
	 * Добавляем inline script
	 */
	wp_add_inline_script(
		'my-image-fallback',
		$inline_js
	);
}
