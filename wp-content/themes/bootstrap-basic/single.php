<?php

/**
 * Template for displaying single post (read full post page).
 * 
 * @package bootstrap-basic
 */

get_header();

/**
 * determine main column size from actived sidebar
 */
$main_column_size = bootstrapBasicGetMainColumnSize();
?>
<div class="page-content">
	<div class="container">
		<div class="row">
			<div class="col-md-12 content-area" id="main-column">
				<div class="breadcrumb">
					<?php
					if (function_exists('bcn_display')) {
						bcn_display();
					}
					?>
				</div>
				<main id="main" class="site-main" role="main">
					<?php
					while (have_posts()) {
						the_post(); ?>
						<?php if (is_singular('post')) { ?><h1><?php the_title(); ?></h1>
							<div class="entry-content-post row">

								<div class="entry-content row">

									<div class="col-md-4"><?php echo get_the_post_thumbnail(); ?></div>
									<div class="col-md-8">
										<p class="act-do"> <?php the_field('акция_продлится_до'); ?></p><?php the_content(); ?>
									</div>

								</div>
							</div>
						<?php } else { ?>
							<?php get_template_part('content', get_post_format()); ?>
						<?php } ?>
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