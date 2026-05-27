<?php

/**
 * The theme header
 * 
 * @package bootstrap-basic
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>  <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>     <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>     <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->

<head>
	<link rel="stylesheet" href="https://cdn.envybox.io/widget/cbk.css">
	<script type="text/javascript" src="https://cdn.envybox.io/widget/cbk.js?wcb_code=e7f0c3bc23f1f04e0197c96af28a89a0" charset="UTF-8" async></script>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width">
	<meta name="yandex-verification" content="6ec57d61863f8cb3" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>



	<script>
		$(window).scroll(function() {
			if ($(this).scrollTop() > 150) {
				$('header.header').addClass("glide");
			} else if ($(this).scrollTop() < 100) {
				$('header').removeClass("glide");

			}
		});
	</script>
	<script>
		$(document).ready(function() {
			$(".navbar-header").click(function() {
				$('body').toggleClass("actived-f");
				$(this).toggleClass("actived");
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			$(".serc").click(function() {
				$('form.search-form.form').toggleClass("actived-ff");
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			$(".woof_price3_search_container.woof_container.woof_price_filter.woof_fs_by_price h4").click(function() {
				$(this).toggleClass("actif");
				return false;
			});
		});
	</script>

	<script src="/wp-content/themes/bootstrap-basic/js/jquery.maskedinput.js"></script>
	<script>
		$(document).ready(function() {
			$(".wpcf7-tel").mask("+7(999) 999-9999");
		});
	</script>


	<link rel="stylesheet" href="/wp-content/themes/bootstrap-basic/css/swiper-bundle.min.css">
	<script src="/wp-content/themes/bootstrap-basic/js/swiper-bundle.min.js"></script>
	<!--wordpress head-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php
	if (function_exists('wp_body_open')) {
		wp_body_open();
	} else {
		do_action('wp_body_open');
	}
	?>
	<!--[if lt IE 8]>
            <p class="ancient-browser-alert">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/" target="_blank">upgrade your browser</a>.</p>
        <![endif]-->

	<?php do_action('before'); ?>
	<header class="container-header" role="banner">
		<div class="container">
			<div class="row">
				<div class="row-with-vspace site-branding">

					<div class="col-md-8 site-title">
						<div class="logo"><a href="/"><img alt="logo" src="<?php the_field('logo', 'option'); ?>" /></a></div>
						<form style="width: 100%;" class="search-form form<?php echo $form_classes; ?>" <?php echo $aria_label; ?>role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
							<div id="search" class="navigation__search search"><input id="form-search-input" class="input input--search" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="Поиск по сайту...">
								<button class="search__button" type="submit"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#213F74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M21.0004 21L16.6504 16.65" stroke="#213F74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</button>
							</div>
						</form>

						<div class="mobi" style="display:none">

							<div class="serc"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#e40714" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
									<path d="M21.0004 21L16.6504 16.65" stroke="#e40714" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg></div>

							<!--<a class="lkk" href="/my-account/"><img src="/wp-content/uploads/2025/01/user.png"/></a>-->
							<?php if (wp_is_mobile()) { ?>

								<a class="min-cart" href="/cart/"><img alt="cart" src="/wp-content/uploads/2025/01/korzina.png" />
									<div class="bord-cart"><?php $items_count = WC()->cart->get_cart_contents_count();
																	echo '<div id="mini-cart-count">' . $items_count . '</div>'; ?></div>
								</a><?php } ?>
						</div>


					</div>
					<div class="col-md-4 page-header-top-right">

						<div class="phone-b"><a href="<?php the_field('phone_link', 'option'); ?>"><?php the_field('phone', 'option'); ?></a>
							<p class="rezh"><?php the_field('working_hours', 'option'); ?></p>
						</div>
						<?php if (wp_is_mobile()) { ?><?php } else { ?><div class="zakzv-b"><a class="button" id="pop-up-open">Заказать звонок</a></div><?php } ?>


					</div>
				</div><!--.site-branding-->
			</div>
		</div>
		<div class="main-navigation">
			<div class="container container-navi">
				<div class="row">
					<div class="col-md-12">
						<nav class="navbar navbar-default">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-primary-collapse">
									<span class="sr-only"><?php _e('Toggle navigation', 'bootstrap-basic'); ?></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
							</div>

							<div class="collapse navbar-collapse navbar-primary-collapse">
								<?php if (wp_is_mobile()) { ?>
									<?php
									wp_nav_menu(
										array(
											'menu' => 'Top bar',
											'container' => false,
											'depth' => 2,
											'menu_class' => 'nav navbar-nav',
											'walker' => new BootstrapBasicMyWalkerNavMenu(),
										)
									);
									?>



									<a href="/wishlist/" class="izbr"><i class="yith-wcwl-icon fa fa-heart-o"></i> Избранное</a>
									<a class="min-cart" href="/cart/"><img alt="cart" src="/wp-content/uploads/2025/01/korzina.png" />
										<div class="bord-cart">
											<div id="mini-cart-count1">
												<?php echo WC()->cart->get_cart_contents_count(); ?>
											</div>
										</div>Корзина
									</a>
									<!--<a class="lkk" href="/my-account/"><img src="/wp-content/uploads/2025/01/user.png"/> Личный кабинет</a>-->
									<div class="phone-b"><a href="<?php the_field('phone_link', 'option'); ?>"><?php the_field('phone', 'option'); ?></a>
										<p class="rezh"><?php the_field('working_hours', 'option'); ?></p>
									</div>
									<div class="zakzv-b"><a class="button" id="pop-up-open">Заказать звонок</a></div>

									<div class="whats"><a target="_blank" href="<?php the_field('whatsapp', 'option'); ?>"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip0_1_671)">
													<path d="M10.0011 0.90918C15.022 0.90918 19.092 4.97918 19.092 10.0001C19.092 15.021 15.022 19.091 10.0011 19.091C8.39451 19.0936 6.81622 18.6684 5.42835 17.8592L0.913805 19.091L2.1429 14.5746C1.33298 13.1863 0.907485 11.6074 0.910169 10.0001C0.910169 4.97918 4.98017 0.90918 10.0011 0.90918ZM6.9029 5.72736L6.72108 5.73463C6.60337 5.7418 6.48833 5.77273 6.3829 5.82554C6.28428 5.88139 6.19426 5.9512 6.11562 6.03282C6.00653 6.13554 5.94471 6.22463 5.87835 6.311C5.5421 6.74818 5.36105 7.28492 5.36381 7.83645C5.36562 8.28191 5.48199 8.71554 5.66381 9.121C6.03562 9.941 6.64744 10.8092 7.45471 11.6137C7.64926 11.8074 7.84017 12.0019 8.04562 12.1828C9.0487 13.066 10.244 13.7029 11.5365 14.0428L12.0529 14.1219C12.2211 14.131 12.3893 14.1183 12.5584 14.1101C12.8231 14.0964 13.0816 14.0247 13.3156 13.9001C13.4347 13.8388 13.5509 13.772 13.6638 13.7001C13.6638 13.7001 13.7029 13.6746 13.7774 13.6183C13.9002 13.5274 13.9756 13.4628 14.0774 13.3565C14.1529 13.2783 14.2183 13.1865 14.2683 13.0819C14.3393 12.9337 14.4102 12.651 14.4393 12.4155C14.4611 12.2355 14.4547 12.1374 14.452 12.0765C14.4483 11.9792 14.3674 11.8783 14.2793 11.8355L13.7502 11.5983C13.7502 11.5983 12.9593 11.2537 12.4756 11.0337C12.425 11.0116 12.3708 10.999 12.3156 10.9965C12.2534 10.9901 12.1906 10.9971 12.1313 11.017C12.072 11.0369 12.0177 11.0693 11.972 11.1119C11.9674 11.1101 11.9065 11.1619 11.2493 11.9583C11.2115 12.009 11.1596 12.0473 11.1 12.0683C11.0404 12.0894 10.9759 12.0922 10.9147 12.0765C10.8555 12.0606 10.7975 12.0405 10.7411 12.0165C10.6283 11.9692 10.5893 11.951 10.512 11.9183C9.99023 11.6906 9.50716 11.383 9.08017 11.0065C8.96562 10.9065 8.85926 10.7974 8.75017 10.6919C8.39252 10.3494 8.08083 9.96191 7.8229 9.53918L7.76926 9.45282C7.73073 9.39478 7.69959 9.33218 7.67653 9.26645C7.64199 9.13282 7.73199 9.02554 7.73199 9.02554C7.73199 9.02554 7.9529 8.78372 8.05562 8.65282C8.15562 8.52554 8.24017 8.40191 8.29471 8.31373C8.40199 8.141 8.43562 7.96373 8.37926 7.82645C8.12471 7.20463 7.86108 6.58554 7.59017 5.971C7.53653 5.84918 7.37744 5.76191 7.2329 5.74463C7.18381 5.73918 7.13471 5.73372 7.08562 5.73009C6.96354 5.72402 6.8412 5.72524 6.71926 5.73373L6.90199 5.72645L6.9029 5.72736Z" fill="#00B649" />
												</g>
												<defs>
													<clipPath id="clip0_1_671">
														<rect width="20" height="20" fill="white" />
													</clipPath>
												</defs>
											</svg> Написать в WhatsApp</a></div>

									<div class="whats"><a target="_blank" href="<?php the_field('telegram', 'option'); ?>"><svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip0_34_25)">
													<path d="M17.5 0C12.8598 0 8.40547 1.84488 5.12695 5.12559C1.84508 8.40761 0.000939117 12.8586 0 17.5C0 22.1394 1.8457 26.5937 5.12695 29.8744C8.40547 33.1551 12.8598 35 17.5 35C22.1402 35 26.5945 33.1551 29.873 29.8744C33.1543 26.5937 35 22.1394 35 17.5C35 12.8606 33.1543 8.40629 29.873 5.12559C26.5945 1.84488 22.1402 0 17.5 0Z" fill="url(#paint0_linear_34_252)" />
													<path d="M7.92148 17.3152C13.0238 15.0927 16.4254 13.6274 18.1262 12.9194C22.9879 10.8979 23.9969 10.5468 24.6559 10.5349C24.8008 10.5325 25.1234 10.5684 25.334 10.7386C25.509 10.8821 25.5582 11.0763 25.5828 11.2126C25.6047 11.3487 25.6348 11.6591 25.6102 11.9014C25.3477 14.6686 24.2074 21.3836 23.6277 24.483C23.3844 25.7945 22.9004 26.2341 22.4328 26.2771C21.4156 26.3706 20.6445 25.6055 19.6602 24.9605C18.1207 23.9507 17.2512 23.3223 15.7555 22.3371C14.0273 21.1985 15.1484 20.5726 16.1328 19.55C16.3898 19.2823 20.8687 15.2094 20.9535 14.84C20.9644 14.7938 20.9754 14.6215 20.8715 14.5307C20.7703 14.4397 20.6199 14.4709 20.5105 14.4955C20.3547 14.5305 17.8965 16.1569 13.1277 19.3744C12.4305 19.854 11.7988 20.0878 11.2301 20.0755C10.6066 20.0621 9.40351 19.7222 8.50937 19.4318C7.41562 19.0755 6.54335 18.8871 6.61992 18.282C6.6582 17.967 7.09296 17.6446 7.92148 17.3152Z" fill="white" />
												</g>
												<defs>
													<linearGradient id="paint0_linear_34_252" x1="1750" y1="0" x2="1750" y2="3500" gradientUnits="userSpaceOnUse">
														<stop offset="1" stop-color="#2AABEE" />
														<stop offset="1" stop-color="#229ED9" />
													</linearGradient>
													<clipPath id="clip0_34_25">
														<rect width="35" height="35" fill="white" />
													</clipPath>
												</defs>
											</svg> Написать в Telegram</a></div>


								<?php } else { ?>

									<?php
									wp_nav_menu(
										array(
											'menu' => 'Top bar',
											'container' => false,
											'depth' => 2,
											'menu_class' => 'nav navbar-nav',
											'walker' => new BootstrapBasicMyWalkerNavMenu(),
										)
									);
									?><a href="/wishlist/" class="izbr"><i class="yith-wcwl-icon fa fa-heart-o"></i> Избранное</a>
									<a class="min-cart" href="/cart/"><img alt="cart" src="/wp-content/uploads/2024/11/shopping-cart.svg" />
										<div class="bord-cart"><?php $items_count = WC()->cart->get_cart_contents_count();
																		echo '<div id="mini-cart-count">' . $items_count . '</div>'; ?></div>Корзина
									</a><?php } ?>
								<?php dynamic_sidebar('navbar-right'); ?>
							</div><!--.navbar-collapse-->
						</nav>
					</div>
				</div>
			</div>
		</div><!--.main-navigation-->
	</header>


	<div id="content" class="row-with-vspace site-content">