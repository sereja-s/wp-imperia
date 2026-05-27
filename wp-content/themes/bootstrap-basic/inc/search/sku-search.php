<?php

/**
 * =========================================================
 * WooCommerce SKU Search Extension
 * =========================================================
 *
 * Расширяет стандартный поиск WordPress:
 * - сохраняет поиск по title/content
 * - добавляет поиск по SKU (артикулу)
 *
 * Ничего не заменяет в ядре WordPress.
 * Используются стандартные hooks:
 * - posts_join
 * - posts_where
 * - posts_distinct
 *
 * =========================================================
 */


/**
 * Защита от прямого открытия файла.
 *
 * ABSPATH определяется WordPress при загрузке.
 * Если файла WordPress нет — выполнение прекращается.
 */
if (!defined('ABSPATH')) {
	exit;
}


/**
 * =========================================================
 * 1. Подключаем таблицу wp_postmeta
 * =========================================================
 *
 * WordPress ищет только в wp_posts.
 * SKU WooCommerce хранится в wp_postmeta:
 *
 * meta_key = '_sku'
 *
 * Поэтому нам нужен JOIN.
 */
add_filter(
	'posts_join',
	'mytheme_sku_search_join',
	10,
	1
);


/**
 * Добавляет JOIN таблицы postmeta.
 *
 * @param string $join
 * Текущий SQL JOIN WordPress.
 *
 * @return string
 * Возвращает изменённый JOIN.
 */
function mytheme_sku_search_join($join)
{
	global $wpdb;

	/**
	 * Не работаем:
	 * - в админке
	 * - вне поиска
	 */
	if (is_admin() || !is_search()) {
		return $join;
	}

	/**
	 * LEFT JOIN:
	 * подключаем postmeta как sku_meta
	 *
	 * ON:
	 * связываем posts.ID с postmeta.post_id
	 *
	 * meta_key = '_sku':
	 * берём только SKU WooCommerce
	 */
	$join .= "
        LEFT JOIN {$wpdb->postmeta} AS sku_meta
        ON (
            {$wpdb->posts}.ID = sku_meta.post_id
            AND sku_meta.meta_key = '_sku'
        )
    ";

	return $join;
}


/**
 * =========================================================
 * 2. Добавляем поиск по SKU
 * =========================================================
 *
 * Расширяем WHERE стандартного поиска WordPress.
 */
add_filter(
	'posts_where',
	'mytheme_sku_search_where',
	10,
	1
);


/**
 * Добавляет условие поиска по SKU.
 *
 * @param string $where
 * Текущий WHERE WordPress.
 *
 * @return string
 * Возвращает расширенный WHERE.
 */
function mytheme_sku_search_where($where)
{
	global $wpdb;

	/**
	 * Не работаем:
	 * - в админке
	 * - вне поиска
	 */
	if (is_admin() || !is_search()) {
		return $where;
	}

	/**
	 * Получаем поисковый запрос пользователя.
	 *
	 * Например:
	 * ?s=ABC-100
	 */
	$search_term = get_query_var('s');

	/**
	 * Если строка поиска пустая —
	 * ничего не меняем.
	 */
	if (empty($search_term)) {
		return $where;
	}

	/**
	 * esc_like()
	 * экранирует спецсимволы LIKE.
	 *
	 * %term%
	 * позволяет искать частичные совпадения.
	 */
	$like = '%' . $wpdb->esc_like($search_term) . '%';

	/**
	 * Добавляем:
	 *
	 * OR sku_meta.meta_value LIKE '%ABC%'
	 *
	 * ВАЖНО:
	 * Мы НЕ заменяем стандартный поиск.
	 * Мы только добавляем SKU как дополнительное условие.
	 */
	$where .= $wpdb->prepare(
		" OR sku_meta.meta_value LIKE %s ",
		$like
	);

	return $where;
}


/**
 * =========================================================
 * 3. Убираем дубликаты товаров
 * =========================================================
 *
 * JOIN может создавать повторяющиеся строки.
 *
 * DISTINCT решает проблему.
 */
add_filter(
	'posts_distinct',
	'mytheme_sku_search_distinct',
	10,
	1
);


/**
 * Добавляет DISTINCT в SQL.
 *
 * @param string $distinct
 * Текущее значение DISTINCT.
 *
 * @return string
 */
function mytheme_sku_search_distinct($distinct)
{
	/**
	 * Только для поиска на фронтенде.
	 */
	if (is_search() && !is_admin()) {
		return 'DISTINCT';
	}

	return $distinct;
}


/**
 * =========================================================
 * 4. Ограничиваем поиск товарами WooCommerce
 * =========================================================
 *
 * Улучшает:
 * - производительность
 * - точность поиска
 *
 * Иначе WordPress ищет:
 * - посты
 * - страницы
 * - товары
 * - всё подряд
 */
add_action(
	'pre_get_posts',
	'mytheme_sku_search_products_only'
);


/**
 * Ограничиваем поиск только post_type=product.
 *
 * @param WP_Query $query
 */
function mytheme_sku_search_products_only($query)
{
	/**
	 * Не изменяем:
	 * - админку
	 * - вторичные запросы
	 */
	if (
		is_admin()
		|| !$query->is_main_query()
		|| !$query->is_search()
	) {
		return;
	}

	/**
	 * Ищем только WooCommerce товары.
	 */
	$query->set('post_type', 'product');
}
