<?php
/**
 * Template Name: Gravitas Canvas
 * Template Post Type: page
 *
 * @package Elevora
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'gravitas-canvas' ); ?>>
<?php wp_body_open(); ?>
<?php
while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="site-main gravitas-canvas__content">
		<?php the_content(); ?>
	</main>
	<?php
endwhile;
wp_footer();
?>
</body>
</html>
