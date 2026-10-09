<?php
/**
 * Comments template.
 *
 * @package Elevora
 */
if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?><h2 class="comments-title"><span><?php printf( esc_html( _nx( 'One response', '%1$s responses', get_comments_number(), 'comments title', 'gravitas-coaching' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></span></h2><ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 56 ) ); ?></ol><?php the_comments_navigation(); ?><?php endif; ?>
	<?php if ( ! comments_open() && get_comments_number() ) : ?><p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'gravitas-coaching' ); ?></p><?php endif; ?>
	<?php
	comment_form(
		array(
			'title_reply'          => '<span class="reply-title-text">' . esc_html__( 'LEAVE A COMMENT', 'gravitas-coaching' ) . '</span>',
			'title_reply_to'       => '<span class="reply-title-text">' . esc_html__( 'LEAVE A REPLY TO %s', 'gravitas-coaching' ) . '</span>',
			'comment_notes_before' => '',
			'comment_field'        => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="6" placeholder="' . esc_attr__( 'Your Comment', 'gravitas-coaching' ) . '" required></textarea></p>',
			'label_submit'         => esc_html__( 'SUBMIT', 'gravitas-coaching' ),
		)
	);
	?>
</section>

