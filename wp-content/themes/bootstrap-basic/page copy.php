<?php get_header();
$main_column_size = bootstrapBasicGetMainColumnSize(); ?>




<div class="page-content">
	<div class="container">

		<div class="breadcrumb">
			<?php
			if (function_exists('bcn_display')) {
				bcn_display();
			}
			?>
		</div>




		<?php
		while (have_posts()) {
			the_post(); ?>

			<h1 <?php if (is_product_category()) { ?>class="mm-bb" <?php } ?>><?php the_title(); ?></h1>

		<?php


		} //endwhile;
		?>

		<?php if (is_shop()) { ?><!--<div class="cats-main">
<?php
			$terms = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'parent'   => 0
				)


			);
			if ($terms) {

				echo '<ul class="product-cats-m">';

				foreach ($terms as $term) {

					echo '<li class="category">';
					echo '<a href="' .  esc_url(get_term_link($term)) . '" class="' . $term->slug . '">';
					$thumbnail_id = get_woocommerce_term_meta($term->term_id, 'photo', true);
					$image = wp_get_attachment_url($thumbnail_id);
					echo "<img src='{$image}' alt='' />";

					echo '<h2>';

					echo $term->name;

					echo '</h2><p class="count-c">Товаров: ';
					echo $term->count;
					echo '</p>';
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
			} ?>
	</div>--><?php } ?>
		<div class="row">



			<?php if (is_shop()) { ?>
				<div class="col-md-3">
					<div class="tit-f">Фильтр <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z" fill="#203f74" />
						</svg></div><?php echo do_shortcode("[woof sid='generator_677bcd143c3731']"); ?>
				</div>
			<?php } ?>
			<?php if (is_product_category()) { ?>
				<div class="col-md-3">
					<div class="tit-f">Фильтр <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z" fill="#203f74" />
						</svg></div><?php echo do_shortcode('[woof sid="generator_677bcd143c3731"]'); ?>
				</div>
			<?php } ?>


			<div class="<?php if (is_shop()) { ?>col-md-9<?php } elseif (is_product_category()) { ?>col-md-9<?php } else { ?>col-md-12<?php } ?> content-area" id="main-column">






				<main id="main" class="site-main" role="main">
					<?php



					while (have_posts()) {
						the_post();
					?>

						<div class="entry-content"><?php the_content(); ?></div>

					<?php echo "\n\n";
					} //endwhile;
					?>
				</main>
			</div>
		</div>
	</div>
</div>
<div class="block-4">
	<div class="container"><?php echo do_shortcode('[contact-form-7 id="db6d50f" title="Остались вопросы?"]'); ?></div>
</div>
<?php get_footer(); ?>