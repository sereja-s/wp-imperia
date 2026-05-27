<?php

/**
 * Template for displaying search form in bootstrap-basic theme
 *
 * @package bootstrap-basic
 */

// Гарантированная инициализация $args
if (!isset($args)) {
	$args = array();
}

// Безопасное заполнение $aria_label
$aria_label = '';
if (isset($args['aria_label']) && !empty($args['aria_label'])) {
	$aria_label = ' aria-label="' . esc_attr($args['aria_label']) . '"';
}

// Безопасное заполнение $form_classes с экранированием
$form_classes = '';
if (isset($args['bootstrapbasic']['form_classes'])) {
	$form_classes = ' ' . esc_attr($args['bootstrapbasic']['form_classes']);
}

// Безопасное заполнение $display_for
$display_for = '';
if (isset($args['bootstrapbasic']['display_for'])) {
	$display_for = $args['bootstrapbasic']['display_for'];
}
?>
<form style="width: 100%;" class="search-form form<?php echo esc_attr($form_classes); ?>" <?php echo $aria_label; ?> role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
	<?php if ('navbar' === $display_for) { ?>
		<div class="form-group">
			<input class="form-control" type="search" name="s" value="<?php echo get_search_query(); ?>"
				placeholder="<?php echo esc_attr_x('Search &hellip;', 'placeholder', 'bootstrap-basic'); ?>"
				title="<?php echo esc_attr_x('Search for:', 'label', 'bootstrap-basic'); ?>">
		</div>
		<button type="submit" class="btn btn-default"><?php esc_html_e('Search', 'bootstrap-basic'); ?></button>
	<?php } else { ?>
		<div id="search" class="navigation__search search">
			<input id="form-search-input" class="input input--search" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="Поиск по сайту...">
			<button class="search__button" type="submit">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#213F74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M21.0004 21L16.6504 16.65" stroke="#213F74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>
	<?php } // endif; display for. 
	?>
</form>