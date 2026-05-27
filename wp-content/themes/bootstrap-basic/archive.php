<?php 
/**
 * Displaying archive page (category, tag, archives post, author's post)
 * 
 * @package bootstrap-basic
 */

get_header(); 

/**
 * determine main column size from actived sidebar
 */
$main_column_size = bootstrapBasicGetMainColumnSize();
?>

<div class="page-content"><div class="container">
	
	<div class="breadcrumb">
<?php
if(function_exists('bcn_display'))
{
	bcn_display();
}
?>
</div>
	<h1 <?php if( is_product_category() ) { ?>class="mm-bb"<?php } ?>> <?php
                                if (is_category()) :
                                    single_cat_title();

                                elseif (is_tag()) :
                                    single_tag_title();

                                elseif (is_author()) :
                                    /* 
                                     * Queue the first post, that way we know
                                     * what author we're dealing with (if that is the case).
                                     */
                                    the_post();
                                    /* translators: %s Author name. */
                                    printf(__('Author: %s', 'bootstrap-basic'), '<span class="vcard">' . get_the_author() . '</span>');
                                    /* 
                                     * Since we called the_post() above, we need to
                                     * rewind the loop back to the beginning that way
                                     * we can run the loop properly, in full.
                                     */
                                    rewind_posts();

                                elseif (is_day()) :
                                    /* translators: %s Date value. */
                                    printf(__('Day: %s', 'bootstrap-basic'), '<span>' . get_the_date() . '</span>');

                                elseif (is_month()) :
                                    /* translators: %s Month value. */
                                    printf(__('Month: %s', 'bootstrap-basic'), '<span>' . get_the_date('F Y') . '</span>');

                                elseif (is_year()) :
                                    /* translators: %s Year value. */
                                    printf(__('Year: %s', 'bootstrap-basic'), '<span>' . get_the_date('Y') . '</span>');

                                elseif (is_tax('post_format', 'post-format-aside')) :
                                    _e('Asides', 'bootstrap-basic');

                                elseif (is_tax('post_format', 'post-format-image')) :
                                    _e('Images', 'bootstrap-basic');

                                elseif (is_tax('post_format', 'post-format-video')) :
                                    _e('Videos', 'bootstrap-basic');

                                elseif (is_tax('post_format', 'post-format-quote')) :
                                    _e('Quotes', 'bootstrap-basic');

                                elseif (is_tax('post_format', 'post-format-link')) :
                                    _e('Links', 'bootstrap-basic');
	                        	 elseif (is_tax('product', '')) :
                                    _e('Links', 'bootstrap-basic');

                                else :
                                    _e('Archives', 'bootstrap-basic');

                                endif;
                                ?> </h1>
<div class="row">	
	
<?php if( is_shop() ) { ?>
<div class="col-md-3"><div class="tit-f">Фильтр <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z" fill="#203f74"/></svg></div><?php echo do_shortcode("[woof sid='generator_677bcd143c3731']");?>
</div>
<?php } ?>
<?php if( is_product_category() ) { ?>
<div class="col-md-3"><div class="tit-f">Фильтр <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z" fill="#203f74"/></svg></div><?php echo do_shortcode('[woof sid="generator_677bcd143c3731"]');?>
</div>
<?php } ?>		
	
	
	
                <div class="<?php if( is_shop() ) { ?>col-md-9<?php } elseif( is_product_category() ) { ?>col-md-9<?php } else { ?>col-md-12<?php } ?> content-area" id="main-column">
                    <main id="main" class="site-main row" role="main">
                        <?php if (have_posts()) { ?> 

                        <header class="page-header">
                            
                            
                            <?php
                            // Show an optional term description.
                            $term_description = term_description();
                            if (!empty($term_description)) {
                                /* translators: %s Description. */
                                printf('<div class="taxonomy-description">%s</div>', $term_description);
                            } //endif;
                            ?>
                        </header><!-- .page-header -->
                        <div class="akcii row">
                        <?php 
                        /* Start the Loop */
                        while (have_posts()) {
                            the_post();?>
<div class="col-md-4">
		<?php echo get_the_post_thumbnail(); ?>
			<p class="act-do"> <?php the_field('акция_продлится_до');?></p>
			<a class="post-title-get-post" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<div class="act-exc"><?php the_excerpt(); ?></div>
			<a class="but-read-more" href="<?php the_permalink(); ?>">Подробнее <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 1L3.85858 3.85858C3.93668 3.93668 3.93668 4.06332 3.85858 4.14142L1 7" stroke="#213F74" stroke-width="1.5" stroke-linecap="round"/>
</svg></a>
		</div>
                            
                       <?php } //endwhile; 
                        ?> 
						
                        <?php bootstrapBasicPagination(); ?> 
</div>
                        <?php } else { ?> 

                        <?php get_template_part('no-results', 'archive'); ?> 

                        <?php } //endif; ?> 
                    </main>
                </div></div>
	
	</div></div>
<div class="block-4"><div class="container"><?php echo do_shortcode('[contact-form-7 id="db6d50f" title="Остались вопросы?"]');?></div></div>
<?php get_footer(); ?> 