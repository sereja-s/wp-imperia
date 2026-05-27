<?php

/**
 * The template for displaying search results.
 *
 * @package bootstrap-basic
 */

get_header();

/**
 * determine main column size from active sidebar
 */
$main_column_size = bootstrapBasicGetMainColumnSize();
?>
<div class="page-content">
	<div class="container">
		<div class="breadcrumb">
			<?php
			if (function_exists('bcn_display')) {
				bcn_display();
			}
			?>
		</div>
		<header class="page-header">
			<h1 class="page-title"><?php
											/* translators: %s Search value. */
											printf(__('Search Results for: %s', 'bootstrap-basic'), '<span>' . get_search_query() . '</span>');
											?></h1>
		</header><!-- .page-header -->
		<div class="row">
			<div class="col-md-9 content-area" id="main-column">
				<main id="main" class="site-main" role="main">
					<?php if (have_posts()) { ?>
						<div class="woocommerce woocommerce-page">
							<ul class="products columns-3">
								<?php
								// start the loop
								while (have_posts()) {
									the_post();

									// Получаем ID текущего поста
									$post_id = get_the_ID();
									$post_type = get_post_type($post_id);

									if ($post_type == 'product') {
										wc_get_template_part('content', 'product');
									} else {
										// Для других типов постов выводим стандартный шаблон
										get_template_part('content', get_post_format());
									}
								}
								?>
							</ul>
						</div>
						<?php
						bootstrapBasicPagination();
						?>
					<?php } else { ?>
						<?php get_template_part('no-results', 'search'); ?>
					<?php } // endif; 
					?>
				</main>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>