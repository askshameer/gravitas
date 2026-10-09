<?php
/**
 * Journal card.
 *
 * @package Elevora
 */

$post_id   = get_the_ID();
$post_link = get_permalink( $post_id );
$is_video  = elevora_post_is_video();
?>
<article <?php post_class( 'post-card ' . ( $is_video ? 'post-card--video ' : '' ) . ( isset( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '' ) ); ?>>
	<a class="post-card__media" href="<?php echo esc_url( $post_link ); ?>">
		<?php if ( has_post_thumbnail( $post_id ) ) : echo get_the_post_thumbnail( $post_id, 'elevora-card', array( 'loading' => 'lazy' ) ); else : ?><span class="post-card__placeholder"></span><?php endif; ?>
		<?php if ( $is_video ) : ?><span class="post-card__play" aria-hidden="true">&#9654;</span><?php endif; ?>
	</a>
	<div class="post-card__body">
		<div class="post-card__meta"><?php $categories = get_the_category( $post_id ); if ( $categories ) : ?><a href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a><?php endif; ?><?php if ( ! isset( $args['show_date'] ) || $args['show_date'] ) : ?><span><?php echo esc_html( get_the_date( '', $post_id ) ); ?></span><?php endif; ?></div>
		<h3><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 28 ) ); ?></p>
		<div class="post-card__footer"><span><?php echo get_avatar( get_the_author_meta( 'ID', (int) get_post_field( 'post_author', $post_id ) ), 26 ); ?><?php the_author(); ?></span><span><?php echo absint( get_comments_number( $post_id ) ); ?> <?php esc_html_e( 'comments', 'gravitas-coaching' ); ?></span></div>
		<a class="text-link" href="<?php echo esc_url( $post_link ); ?>"><?php echo $is_video ? esc_html__( 'Watch story', 'gravitas-coaching' ) : esc_html__( 'Read article', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</article>
