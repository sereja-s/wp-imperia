<?php

/**
 * Bootstrap Basic theme
 * 
 * @package bootstrap-basic
 */


add_filter('woocommerce_checkout_fields', 'remove_checkout_phone_field');
function remove_checkout_phone_field($fields)
{

	unset($fields['shipping']['shipping_phone']);

	return $fields;
}



add_action('template_redirect', 'truemisha_recently_viewed_product_cookie', 20);

function truemisha_recently_viewed_product_cookie()
{

	// если находимся не на странице товара, ничего не делаем
	if (! is_product()) {
		return;
	}


	if (empty($_COOKIE['woocommerce_recently_viewed_2'])) {
		$viewed_products = array();
	} else {
		$viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed_2']);
	}

	// добавляем в массив текущий товар
	if (! in_array(get_the_ID(), $viewed_products)) {
		$viewed_products[] = get_the_ID();
	}

	// нет смысла хранить там бесконечное количество товаров
	if (sizeof($viewed_products) > 15) {
		array_shift($viewed_products); // выкидываем первый элемент
	}

	// устанавливаем в куки
	wc_setcookie('woocommerce_recently_viewed_2', join('|', $viewed_products));
}

add_shortcode('recently_viewed_products', 'truemisha_recently_viewed_products');

function truemisha_recently_viewed_products()
{

	if (empty($_COOKIE['woocommerce_recently_viewed_2'])) {
		$viewed_products = array();
	} else {
		$viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed_2']);
	}

	if (empty($viewed_products)) {
		return;
	}
	$loop = new WP_Query(
		array(
			'post_type' => 'product',
			'posts_per_page' => 30,
			'post__in'  => array_reverse(array_map('absint', $viewed_products))
		)

	);

	$content = '';
	while ($loop->have_posts()) {
		$loop->the_post();

		ob_start(); // Начинаем буферизацию (запускает буфер — весь последующий вывод (включая echo внутри шаблона) сохраняется в памяти)
		wc_get_template_part('content', 'product'); // Вывод идёт в буфер (выполняет свой код, но результат не показывается на экране, а попадает в буфер)
		$product_content = ob_get_clean(); // Захватываем содержимое буфера (останавливает буфер и возвращает его содержимое в виде строки)

		$content .= $product_content;
	}

	wp_reset_postdata();

	return $content;
}




add_filter('wp_img_tag_add_auto_sizes', '__return_false');

add_filter('woocommerce_add_to_cart_fragments', 'coderun_wc_refresh_mini_cart_count');

function coderun_wc_refresh_mini_cart_count($fragments)
{
	ob_start();
?>
	<div id="mini-cart-count">
		<?php echo WC()->cart->get_cart_contents_count(); ?>
	</div>
<?php
	$fragments['#mini-cart-count'] = ob_get_clean();
	return $fragments;
}



/**
 * Меняем ярлык "Распродажа" на процент скидки
 */
add_filter('woocommerce_sale_flash', 'add_percentage_to_sale_badge', 20, 3);
function add_percentage_to_sale_badge($html, $post, $product)
{
	if ($product->is_type('variable')) {
		$percentages = array();

		// Get all variation prices

		$prices = $product->get_variation_prices();

		// Loop through variation prices
		foreach ($prices['price'] as $key => $price) {
			// Only on sale variations
			if ($prices['regular_price'][$key] !== $price) {
				// Calculate and set in the array the percentage for each variation on sale
				$percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
			}
		}
		$percentage = max($percentages) . '%';
	} else {
		$regular_price = (float) $product->get_regular_price();
		$sale_price    = (float) $product->get_sale_price();

		$percentage    = round(100 - ($sale_price / $regular_price * 100)) . '%';
	}
	return '<span class="onsale">' . esc_html__('-', 'woocommerce') . '' . $percentage . '</span>';
}

function my_output_related_products_args($args)
{
	$args['posts_per_page'] = 8;     // число выводимых товаров, по умолчанию 2
	$args['columns'] = 4;            // количество выводимых колонок, по умолчанию 2
	$args['orderby'] = 'rand';        // порядок сортировки. по умолчанию случайный 'rand'

	return $args;
}
add_filter('woocommerce_output_related_products_args', 'my_output_related_products_args');


/**
 * Required WordPress variable.
 */



add_filter('woocommerce_variable_price_html', 'truemisha_variation_price', 20, 2);

function truemisha_variation_price($price, $product)
{

	$min_regular_price = $product->get_variation_regular_price('min', true);
	$min_sale_price = $product->get_variation_sale_price('min', true);
	$max_regular_price = $product->get_variation_regular_price('max', true);
	$max_sale_price = $product->get_variation_sale_price('max', true);

	if (! ($min_regular_price == $max_regular_price && $min_sale_price == $max_sale_price)) {
		if ($min_sale_price < $min_regular_price) {
			$price = sprintf('от <del>%1$s</del><ins>%2$s</ins>', wc_price($min_regular_price), wc_price($min_sale_price));
		} else {
			$price = sprintf('от %1$s', wc_price($min_regular_price));
		}
	}

	return $price;
}


if (!isset($content_width)) {
	$content_width = 1170;
}


/**
 * The Bootstrap Basic main class.
 */
require_once get_template_directory() . '/inc/BootstrapBasic.php';


/**
 * Register commonly use scripts and styles.
 */
$BootstrapBasic = new \BootstrapBasic();
unset($BootstrapBasic);


if (!function_exists('bootstrapBasicSetup')) {
	/**
	 * Setup theme and register support wp features.
	 */
	function bootstrapBasicSetup()
	{
		/**
		 * Make theme available for translation
		 * Translations can be filed in the /languages/ directory
		 * 
		 * copy from underscores theme
		 */
		load_theme_textdomain('bootstrap-basic', get_template_directory() . '/languages');

		// add theme support title-tag
		add_theme_support('title-tag');

		// add theme support post and comment automatic feed links
		add_theme_support('automatic-feed-links');

		// enable support for post thumbnail or feature image on posts and pages
		add_theme_support('post-thumbnails');

		// allow the use of html5 markup
		// @link https://codex.wordpress.org/Theme_Markup
		add_theme_support('html5', array('caption', 'comment-form', 'comment-list', 'gallery', 'search-form'));

		// add support menu
		register_nav_menus(array(
			'primary' => __('Primary Menu', 'bootstrap-basic'),
		));

		// add post formats support
		add_theme_support('post-formats', array('aside', 'image', 'video', 'quote', 'link'));

		// add support custom background
		add_theme_support(
			'custom-background',
			apply_filters(
				'bootstrap_basic_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// @since 1.1 or WordPress 5.0+
		// make gutenberg support. --------------------------------------------------------------------------------------
		// @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/ reference.
		// add wide alignment ( https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#wide-alignment )
		add_theme_support('align-wide');
		// support default block styles for front-end ( https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#default-block-styles )
		add_theme_support('wp-block-styles');
		// support editor styles ( https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#editor-styles )
		// this one make appearance in editor more close to Bootstrap 3.
		add_theme_support('editor-styles');
		// support responsive embeds for front-end ( https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#responsive-embedded-content )
		add_theme_support('responsive-embeds');
		// end make gutenberg support. ---------------------------------------------------------------------------------
	} // bootstrapBasicSetup
}
add_action('after_setup_theme', 'bootstrapBasicSetup');


if (!function_exists('bootstrapBasicWidgetsInit')) {
	/**
	 * Register widget areas
	 */
	function bootstrapBasicWidgetsInit()
	{
		register_sidebar(array(
			'name' => __('Sidebar right', 'bootstrap-basic'),
			'id' => 'sidebar-right',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		));

		register_sidebar(array(
			'name' => __('Sidebar left', 'bootstrap-basic'),
			'id' => 'sidebar-left',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		));

		register_sidebar(array(
			'name' => __('Header right', 'bootstrap-basic'),
			'id' => 'header-right',
			'description' => __('Header widget area on the right side next to site title.', 'bootstrap-basic'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		));

		register_sidebar(array(
			'name' => __('Navigation bar right', 'bootstrap-basic'),
			'id' => 'navbar-right',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		));

		register_sidebar(array(
			'name' => __('Footer left', 'bootstrap-basic'),
			'id' => 'footer-left',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		));

		register_sidebar(array(
			'name' => __('Footer right', 'bootstrap-basic'),
			'id' => 'footer-right',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		));
	} // bootstrapBasicWidgetsInit
}
add_action('widgets_init', 'bootstrapBasicWidgetsInit');


if (!function_exists('bootstrapBasicEnqueueScripts')) {
	/**
	 * Enqueue scripts & styles
	 * 
	 * @global \WP_Scripts $wp_scripts
	 */
	function bootstrapBasicEnqueueScripts()
	{
		global $wp_scripts;
		$Theme = wp_get_theme();
		$themeVersion = $Theme->get('Version');
		unset($Theme);

		wp_enqueue_style('bootstrap-style');
		wp_enqueue_style('bootstrap-theme-style', get_template_directory_uri() . '/css/bootstrap-theme.min.css', array(), '3.4.1');
		wp_enqueue_style('fontawesome-style', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.7.0');
		wp_enqueue_style('main-style', get_template_directory_uri() . '/css/main.css', array(), $themeVersion);

		// check if there are any calendar widget block.
		if (bootstrapBasicHasWidgetBlock('calendar') === true) {
			// if theme using widget blocks.
			// enqueue css to fix calendar widget block to render as non widget block.
			// if you would like it to be render as new widget block, please dequeue this handle.
			wp_enqueue_style('bootstrapbasic-widgetblocks-calendar', get_template_directory_uri() . '/css/widget-blocks/calendar.css', array(), $themeVersion);
		}

		// js that is useful for development.
		wp_enqueue_script('modernizr-script', get_template_directory_uri() . '/js/vendor/modernizr.min.js', array(), '3.6.0-20190314', true);
		// js that is useful for old browsers.
		wp_register_script('respond-script', get_template_directory_uri() . '/js/vendor/respond.min.js', array(), '1.4.2', true);
		$wp_scripts->add_data('respond-script', 'conditional', 'lt IE 9');
		wp_enqueue_script('respond-script');
		wp_register_script('html5-shiv-script', get_template_directory_uri() . '/js/vendor/html5shiv.min.js', array(), '3.7.3', true);
		$wp_scripts->add_data('html5-shiv-script', 'conditional', 'lte IE 9');
		wp_enqueue_script('html5-shiv-script');

		if (is_singular() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		wp_enqueue_script('bootstrap-script');
		wp_enqueue_script('main-script', get_template_directory_uri() . '/js/main.js', array('jquery'), $themeVersion, true);
		wp_enqueue_style('bootstrap-basic-style', get_stylesheet_uri(), array(), $themeVersion);

		// move jquery to bottom ( https://wordpress.stackexchange.com/a/225936/41315 )
		$wp_scripts->add_data('jquery', 'group', 1);
		$wp_scripts->add_data('jquery-core', 'group', 1);
		$wp_scripts->add_data('jquery-migrate', 'group', 1);
	} // bootstrapBasicEnqueueScripts
}
add_action('wp_enqueue_scripts', 'bootstrapBasicEnqueueScripts');


/**
 * admin page displaying help.
 */
if (is_admin()) {
	require get_template_directory() . '/inc/BootstrapBasicAdminHelp.php';
	$bbsc_adminhelp = new BootstrapBasicAdminHelp();
	add_action('admin_menu', array($bbsc_adminhelp, 'themeHelpMenu'));
	unset($bbsc_adminhelp);
}


/**
 * Make WordPress 5 (Gutenberg) editor support Bootstrap CSS.
 */
require_once get_template_directory() . '/inc/BootstrapBasicWp5.php';
$BbWp5 = new BootstrapBasicWp5();
unset($BbWp5);


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';


/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';


/**
 * Custom dropdown menu and navbar in walker class
 */
require get_template_directory() . '/inc/BootstrapBasicMyWalkerNavMenu.php';


/**
 * Template functions
 */
require get_template_directory() . '/inc/template-functions.php';


/**
 * --------------------------------------------------------------
 * Theme widget & widget hooks
 * --------------------------------------------------------------
 */
require get_template_directory() . '/inc/widgets/BootstrapBasicAutoRegisterWidgets.php';
$BootstrapBasicAutoRegisterWidgets = new BootstrapBasicAutoRegisterWidgets();
$BootstrapBasicAutoRegisterWidgets->registerAll();
unset($BootstrapBasicAutoRegisterWidgets);
require get_template_directory() . '/inc/template-widgets-hook.php';

// переместить текст после товаров на странице категорий
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
add_action('woocommerce_after_shop_loop', 'woocommerce_taxonomy_archive_description', 100);

// Добавляем визуальный редактор при СОЗДАНИИ product_cat
add_action('product_cat_add_form_fields', function () {
?>
	<div class="form-field">
		<label for="description"><?php _e('Описание', 'textdomain'); ?></label>
		<?php
		ob_start();
		wp_editor('', 'product_cat_description', [
			'textarea_name' => 'description',
			'quicktags'     => true,
			'media_buttons' => false,
			'textarea_rows' => 8,
		]);
		echo ob_get_clean();
		?>
		<p class="description"><?php _e('Описание категории. Можно использовать HTML.', 'textdomain'); ?></p>
	</div>
<?php
});

// Добавляем визуальный редактор при РЕДАКТИРОВАНИИ product_cat
add_action('product_cat_edit_form_fields', function ($term) {
?>
	<tr class="form-field">
		<th scope="row"><label for="description"><?php _e('Описание', 'textdomain'); ?></label></th>
		<td>
			<?php
			ob_start();
			wp_editor(htmlspecialchars_decode($term->description), 'product_cat_description', [
				'textarea_name' => 'description',
				'quicktags'     => true,
				'media_buttons' => false,
				'textarea_rows' => 8,
			]);
			echo ob_get_clean();
			?>
			<p class="description"><?php _e('Описание категории. Можно использовать HTML.', 'textdomain'); ?></p>
		</td>
	</tr>
<?php
}, 10, 1);

// Отключаем автопараграфы в описании категорий товаров
remove_filter('term_description', 'wpautop');


remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('pre_term_description', 'wp_kses_data');
remove_filter('term_description', 'wp_kses_data');
remove_filter('term_description', 'wpautop');

// Фильтр при сохранении категории — сохраняем HTML как есть
add_filter('wp_insert_term_data', function ($data, $taxonomy) {
	if ($taxonomy === 'product_cat') {
		// Убираем фильтрацию описания при сохранении
		remove_filter('pre_term_description', 'wp_filter_kses');
		remove_filter('pre_term_description', 'wp_kses_data');
	}
	return $data;
}, 10, 2);

add_action('init', function () {
	// Полностью убираем фильтрацию HTML у описания терминов
	remove_filter('pre_term_description', 'wp_filter_kses');
	remove_filter('term_description', 'wp_kses_data');
});

//define('DISALLOW_FILE_MODS', true);
//
//add_filter('file_mod_allowed', '__return_false');
//
//
//add_filter('pre_site_transient_update_core', static function ($value) {
//	$upinfo = new stdClass();
//	$upinfo->updates = [];
//	$upinfo->version_checked = $GLOBALS['wp_version'];
//	$upinfo->last_checked = time();
//
//	return $upinfo;
//});

/**
 * Подключаем поиск по артикулам товаров в дополнение к основному поиску
 */
require get_stylesheet_directory() . '/inc/search/sku-search.php';
/**
 * Подключение изображения  по умолчанию если нет картинки (Fallback изображений)
 */
require_once get_template_directory() . '/inc/image-fallback.php';
