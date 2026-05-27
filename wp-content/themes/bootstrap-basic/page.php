<?php
get_header();
$main_column_size = bootstrapBasicGetMainColumnSize();

// Включаем буфер вывода
ob_start();
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

		<?php
		while (have_posts()) {
			the_post(); ?>

			<h1 <?php if (is_product_category()) { ?>class="mm-bb" <?php } ?>><?php the_title(); ?></h1>

		<?php } //endwhile; 
		?>

		<!-- Здесь остаются фильтры и колонки -->
		<div class="row">
			<?php if (is_shop()) { ?>
				<div class="col-md-3">
					<?php echo do_shortcode("[su_menu name='menu-tovar']"); ?>
					<?php echo do_shortcode("[woof sid='generator_677bcd143c3731']"); ?>
				</div>
			<?php } ?>

			<div class="<?php if (is_shop() || is_product_category()) { ?>col-md-9<?php } else { ?>col-md-12<?php } ?> content-area" id="main-column">
				<main id="main" class="site-main" role="main">
					<?php
					while (have_posts()) {
						the_post();
					?>

						<div class="entry-content"><?php the_content(); ?></div>

					<?php } //endwhile; 
					?>
				</main>
			</div>

			<?php if (is_product_category()) { ?>
				<div class="col-md-3">
					<!-- <div class="tit-f">Фильтр</div> -->
					<div class="tit-f">Категории товаров</div>
					<?php echo do_shortcode("[su_menu name='menu-tovar']"); ?>
				</div>
			<?php } ?>

		</div>
	</div>
</div>

<div class="block-4">
	<div class="container">
		<?php echo do_shortcode('[contact-form-7 id="db6d50f" title="Остались вопросы?"]'); ?>
	</div>
</div>

<?php
get_footer();

// Получаем весь вывод из буфера
$html = ob_get_clean();

// Убираем блок с классом .term-description (если он есть)
$html = preg_replace('#<div class="term-description">.*?</div>#s', '', $html, 1);


// Выводим обработанный HTML
echo $html;
?>