<?php
/*
Template Name: Главная страница
*/
?>
<?php get_header(); ?>
<div class="block-1">
<div class="swiper mySwiper main">
<div class="swiper-wrapper">
<?php if( have_rows('slider') ): while ( have_rows('slider') ) : the_row(); ?>
<div class="swiper-slide" style="background-image:url(<?php the_sub_field('image');?>)"><div class="container">
	<a class="button" href="<?php the_sub_field('link');?>"><?php the_sub_field('link_text');?></a>
	<h2><?php the_sub_field('title');?></h2><div class="sli-text"><?php the_sub_field('text');?></div>
</div></div>
<?php endwhile; else : endif; ?>		  
</div><div class="swiper-pagination"></div>
<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
<script>
var swiper = new Swiper(".main", {
	slidesPerView: 1,
    spaceBetween: 0,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
});
</script>

<style>

@media (max-width: 500px) {
  
    
    .swiper-slide:nth-child(2) {
        background-image: url('https://stroitelnyiexpert.ru/wp-content/uploads/2026/02/1z.jpg') !important;
    }
    
    .swiper-slide:nth-child(3) {
        background-image: url('https://stroitelnyiexpert.ru/wp-content/uploads/2026/02/2z.jpg') !important;
    }
    
    .swiper-slide:nth-child(4) {
        background-image: url('https://stroitelnyiexpert.ru/wp-content/uploads/2026/02/3z.jpg') !important;
    }
    
    .swiper-slide:nth-child(5) {
        background-image: url('https://stroitelnyiexpert.ru/wp-content/uploads/2026/02/4z.jpg') !important;
    }
}
</style>

 <h1 class="main-page-title" style="text-align: center; margin: 0 auto; width: 100%; padding-top: 15px;">Интернет-магазин строительных товаров и оборудования</h1>

</div>
<div class="block-2222"><div class="container"><div class="row-0"><?php echo do_shortcode('[product_categories ids="126,457,31,444,123,211,132,179,468,154,147,466,227" orderby="include" columns="4" hide_empty="0" number="0" parent="0"]');?></div>
	<div class="b-s-but"><a class="button" href="/shop/">Показать больше <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 1L3.85858 3.85858C3.93668 3.93668 3.93668 4.06332 3.85858 4.14142L1 7" stroke="#fff" stroke-width="1.5" stroke-linecap="round"></path>
</svg></a></div>
	</div></div>
    
    
<div class="block-21"><div class="container">
	<div class="flexxi"><h2 class="zg">Новинки</h2></div>
	<div class="akcii-row">
	<div class="akcii row" style="margin:0">
		
		<?php echo do_shortcode('[products limit="4" order="desc" orderby="date"]');?>

	</div>
	</div>
	
	</div></div>      
    
    
    
<div class="block-21"><div class="container">
	<div class="flexxi"><h2 class="zg"><?php the_field('заголовок_акции');?></h2><a style="display:none" class="button" href="/shop/?swoof=1&onsales=salesonly">Все акции</a></div>
	<div class="akcii-row">
	<div class="akcii row" style="margin:0">
		
		<?php echo do_shortcode('[products columns="3" category="aktsii-na-stroitelnyj-instrument" limit="4"]');?>

	</div>
	</div>
	
	</div></div>
    <!--<?php
$args = array( 
'posts_per_page' => 4
);
$iwposts = get_posts( $args );
foreach( $iwposts as $post ){ setup_postdata($post);  ?>
		<div class="col-md-4">
		<?php echo get_the_post_thumbnail(); ?>
			<p class="act-do">Акция действует до <?php the_field('акция_продлится_до');?></p>
			<a class="post-title-get-post" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<div class="act-exc"><?php the_excerpt(); ?></div>
			<a class="but-read-more" href="<?php the_permalink(); ?>">Подробнее <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 1L3.85858 3.85858C3.93668 3.93668 3.93668 4.06332 3.85858 4.14142L1 7" stroke="#e40714" stroke-width="1.5" stroke-linecap="round"/>
</svg></a>
		</div>
<?php   }
wp_reset_postdata();  ?>-->
    
  
    
    
<div class="block-3"><div class="container"><div class="row">
	<div class="col-md-7">
		<h2 class="zg" style="display:none"><?php the_field('заголовок_блока_3');?></h2>
		<div class="block-3-text" style="display:none"><?php the_field('текст_блока_3');?></div>
		
		<div class="b-3abs"><img alt="kart" src="<?php the_field('картинка_блока_3');?>"/></div>
	<div class="b-3noabs"><img alt="kart" src="<?php the_field('картинка-фон');?>"/></div>
	</div>
	<div class="col-md-5"><h2 class="zg"><?php the_field('заголовок_блока_3');?></h2>
		<div class="block-3-text"><?php the_field('текст_блока_3');?></div>
		<a class="button" href="/o-kompanii/">Подробнее <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 1L3.85858 3.85858C3.93668 3.93668 3.93668 4.06332 3.85858 4.14142L1 7" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
</svg></a>
	</div>
	
	</div></div></div>
<div class="block-4"><div class="container"><?php echo do_shortcode('[contact-form-7 id="db6d50f" title="Остались вопросы?"]');?></div></div>
<?php get_footer();