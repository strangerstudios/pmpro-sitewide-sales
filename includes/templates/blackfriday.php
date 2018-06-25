<?php
/**
 * Template for a black friday sale
 *
 * @package pmpro-sitewide-sale/includes/templates
 **/

//get_header(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php if ( is_singular() && pings_open() ) { ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php } ?>

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action('before_page'); ?>
<div id="page" class="hfeed site">
	<div id=""
	<div id="content" class="site-content">
		<div class="row">
			<div id="primary" class="large-8 large-offset-2 columns content-area">
				<?php do_action('before_main'); ?>
				<main id="main" class="site-main" role="main">
					<?php do_action('before_loop'); ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php //get_template_part( 'content', 'page' );
							the_content();
						?>
						<?php
						//If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || '0' != get_comments_number() ) :
							comments_template();
						endif;
						?>
					<?php endwhile; // end of the loop. ?>
					<?php do_action('after_loop'); ?>
				</main><!-- #main -->
				<?php do_action('after_main'); ?>
			</div><!-- #primary -->
		</div><!-- .row -->
	</div><!-- #content -->
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="row site-info">
			<div class="large-12 columns">
				<?php
					global $memberlite_defaults;
					$copyright_textbox = get_theme_mod( 'copyright_textbox',$memberlite_defaults['copyright_textbox'] );
					if ( ! empty( $copyright_textbox ) )
					{
						echo wpautop(memberlite_Customize::sanitize_text_with_links($copyright_textbox));
					}
				?>
			</div>
		</div><!-- .row, .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
