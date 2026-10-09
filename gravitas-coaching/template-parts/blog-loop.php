<?php
/**
 * Flexible blog loop.
 *
 * @package Elevora
 */

$layout       = isset( $args['layout'] ) ? elevora_sanitize_blog_layout( $args['layout'] ) : elevora_get_blog_layout();
$loop_classes = 'blog-loop blog-loop--' . $layout;
$post_index   = 0;
?>
<div class="blog-results" data-blog-results data-pagination-mode="<?php echo esc_attr( elevora_get_setting( 'blog_pagination', '', 'numbers' ) ); ?>">
<div class="<?php echo esc_attr( $loop_classes ); ?>">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php
			the_post();
			++$post_index;
			$card_class = '';
			if ( 'editorial' === $layout && 1 === $post_index ) {
				$card_class = 'post-card--featured';
			} elseif ( 'magazine' === $layout && in_array( $post_index, array( 1, 6 ), true ) ) {
				$card_class = 'post-card--featured';
			}
			get_template_part(
				'template-parts/card',
				'post',
				array(
					'class' => $card_class,
					'layout' => $layout,
				)
			);
			?>
		<?php endwhile; ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php elevora_pagination(); ?>
<p class="blog-load-status" role="status" aria-live="polite"></p>
</div>
