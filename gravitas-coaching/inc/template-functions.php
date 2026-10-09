<?php
/**
 * Theme rendering helpers.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a setting directly from Gravitas Core with a Customizer fallback.
 *
 * @param string $option_key Gravitas Core option key.
 * @param string $theme_mod  Theme modification key.
 * @param mixed  $default    Default value.
 * @return mixed
 */
function elevora_get_setting( $option_key, $theme_mod, $default = '' ) {
	if ( is_customize_preview() && ! empty( $theme_mod ) ) {
		$preview_val = get_theme_mod( $theme_mod, null );
		if ( null !== $preview_val ) {
			return $preview_val;
		}
	}

	if ( function_exists( 'gravitas_core_get_option' ) ) {
		$core_val = gravitas_core_get_option( $option_key, null );
		if ( null !== $core_val && '' !== $core_val ) {
			return $core_val;
		}
	}

	if ( ! empty( $theme_mod ) ) {
		$mod_val = get_theme_mod( $theme_mod, null );
		if ( null !== $mod_val ) {
			return $mod_val;
		}
	}

	return $default;
}

/**
 * Check if a given string contains a WordPress shortcode.
 *
 * @param string $string Input string.
 * @return bool
 */
function elevora_contains_shortcode( $string ) {
	if ( ! is_string( $string ) || empty( $string ) ) {
		return false;
	}
	return (bool) preg_match( '/\[.+\]/', $string );
}

/**
 * Sanitize either a URL or shortcode string.
 *
 * @param string $value Input value.
 * @return string
 */
function elevora_sanitize_url_or_shortcode( $value ) {
	$raw = trim( wp_unslash( (string) $value ) );
	if ( elevora_contains_shortcode( $raw ) ) {
		return wp_kses_post( $raw );
	}
	return esc_url_raw( $raw );
}

/**
 * Register and render action modals for shortcodes.
 */
global $elevora_action_modals;
$elevora_action_modals = array();

function elevora_register_action_modal( $id, $title, $shortcode ) {
	global $elevora_action_modals;
	if ( ! is_array( $elevora_action_modals ) ) {
		$elevora_action_modals = array();
	}
	$elevora_action_modals[ $id ] = array(
		'title'     => $title,
		'shortcode' => $shortcode,
	);
}

function elevora_render_action_modals() {
	global $elevora_action_modals;
	if ( empty( $elevora_action_modals ) || ! is_array( $elevora_action_modals ) ) {
		return;
	}
	foreach ( $elevora_action_modals as $id => $modal ) {
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="gravitas-action-modal" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $id ); ?>-title" aria-hidden="true" hidden>
			<div class="gravitas-action-modal__backdrop" data-modal-close></div>
			<div class="gravitas-action-modal__dialog">
				<div class="gravitas-action-modal__header">
					<h3 id="<?php echo esc_attr( $id ); ?>-title" class="gravitas-action-modal__title"><?php echo esc_html( $modal['title'] ); ?></h3>
					<button class="icon-button gravitas-action-modal__close" type="button" data-modal-close aria-label="<?php esc_attr_e( 'Close', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'close' ); // phpcs:ignore ?></button>
				</div>
				<div class="gravitas-action-modal__body entry-content">
					<?php echo do_shortcode( $modal['shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
		<?php
	}
	$elevora_action_modals = array();
}
add_action( 'wp_footer', 'elevora_render_action_modals', 99 );

/**
 * Render an action button that supports either a URL or a Shortcode.
 *
 * @param string $target     URL or Shortcode string.
 * @param string $label      Button text label.
 * @param string $classes    CSS classes for the button.
 * @param string $icon       Icon name (e.g. 'arrow' or empty).
 * @param string $modal_id   Optional unique modal ID if target is a shortcode.
 * @return string HTML button or link markup.
 */
function elevora_render_action_button( $target, $label, $classes = 'button button--accent', $icon = 'arrow', $modal_id = '' ) {
	$target = trim( (string) $target );
	if ( empty( $target ) ) {
		$target = '#contact';
	}

	$icon_html = $icon ? elevora_icon( $icon ) : '';

	if ( elevora_contains_shortcode( $target ) ) {
		if ( empty( $modal_id ) ) {
			static $modal_counter = 0;
			$modal_counter++;
			$modal_id = 'gravitas-modal-' . $modal_counter;
		}
		elevora_register_action_modal( $modal_id, $label, $target );

		return sprintf(
			'<button type="button" class="%s gravitas-modal-trigger" data-modal-target="#%s">%s%s</button>',
			esc_attr( $classes ),
			esc_attr( $modal_id ),
			esc_html( $label ),
			$icon_html
		);
	}

	return sprintf(
		'<a class="%s" href="%s">%s%s</a>',
		esc_attr( $classes ),
		esc_url( $target ),
		esc_html( $label ),
		$icon_html
	);
}

function elevora_icon( $name, $class = '' ) {
	$icons = array(
		'microphone' => '<rect x="9" y="2" width="6" height="13" rx="3"/><path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3M8 22h8M9 6h6M9 9h6"/>',
		'arrow'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
		'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'   => '<path d="m6 6 12 12M18 6 6 18"/>',
		'moon'    => '<path d="M20 15.2A8.5 8.5 0 0 1 8.8 4 8.5 8.5 0 1 0 20 15.2Z"/>',
		'sun'     => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/>',
		'star'    => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>',
		'pin'     => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'check'   => '<path d="m5 12 4 4L19 6"/>',
		'play'    => '<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4V8Z"/>',
		'quote'   => '<path d="M9 11H5a4 4 0 0 1 4-4v8a4 4 0 0 1-4 4M19 11h-4a4 4 0 0 1 4-4v8a4 4 0 0 1-4 4"/>',
		'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15 9-2 4-4 2 2-4 4-2Z"/>',
		'spark'   => '<path d="m12 2 1.8 6.2L20 10l-6.2 1.8L12 18l-1.8-6.2L4 10l6.2-1.8L12 2Z"/><path d="m19 16 .7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7L19 16Z"/>',
		'target'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'people'  => '<path d="M16 19v-1.5A3.5 3.5 0 0 0 12.5 14h-5A3.5 3.5 0 0 0 4 17.5V19"/><circle cx="10" cy="8" r="3"/><path d="M18 10a3 3 0 0 1 0 6M17 5.2a3 3 0 0 1 0 5.6"/>',
		'heart'   => '<path d="M20.8 5.8a5 5 0 0 0-7.1 0L12 7.5l-1.7-1.7a5 5 0 0 0-7.1 7.1L12 21l8.8-8.1a5 5 0 0 0 0-7.1Z"/>',
		'chart'   => '<path d="M4 19V9M10 19V5M16 19v-7M22 19V3"/>',
		'mail'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'phone'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>',
		'grid'    => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
		'list'    => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		$name = 'spark';
	}
	return '<svg class="elevora-icon ' . esc_attr( $class ) . '" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $icons[ $name ] . '</svg>';
}

function elevora_brand( $context = 'header' ) {
	$legacy_logo_id = absint( get_theme_mod( 'custom_logo' ) );
	$primary_logo_id = absint( elevora_get_setting( 'logo_id', '', $legacy_logo_id ) );
	$light_logo_id = absint( elevora_get_setting( 'light_logo_id', 'elevora_light_logo', $primary_logo_id ?: $legacy_logo_id ) );
	$dark_logo_id  = absint( elevora_get_setting( 'dark_logo_id', 'elevora_dark_logo', 0 ) );
	$light_logo_id = $light_logo_id ?: $primary_logo_id ?: $legacy_logo_id ?: $dark_logo_id;
	if ( 'footer' === $context && $dark_logo_id ) {
		$light_logo_id = $dark_logo_id;
		$dark_logo_id  = 0;
	}

	if ( $light_logo_id ) {
		$has_dark_logo = $dark_logo_id && $dark_logo_id !== $light_logo_id;
		?>
		<a class="custom-logo-link site-brand--dual-logo<?php echo $has_dark_logo ? ' has-dark-logo' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo wp_get_attachment_image( $light_logo_id, 'full', false, array( 'class' => 'custom-logo site-logo site-logo--light', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $has_dark_logo ) : ?><?php echo wp_get_attachment_image( $dark_logo_id, 'full', false, array( 'class' => 'custom-logo site-logo site-logo--dark', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?>
		</a>
		<?php
		return;
	}
	?>
	<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span class="site-brand-mark" aria-hidden="true"><i></i><i></i></span>
		<span class="site-brand-name"><?php esc_html_e( 'Gravitas Coaching', 'gravitas-coaching' ); ?></span>
	</a>
	<?php
}

/**
 * Replace generic Recent Posts widgets in the main sidebar with the theme's
 * thumbnail-based editorial list rendered by sidebar.php.
 */
function elevora_hide_classic_recent_posts_widget( $instance, $widget, $args ) {
	if ( isset( $args['id'] ) && 'sidebar-main' === $args['id'] && $widget instanceof WP_Widget_Recent_Posts ) {
		return false;
	}
	return $instance;
}
add_filter( 'widget_display_callback', 'elevora_hide_classic_recent_posts_widget', 10, 3 );

function elevora_hide_sidebar_latest_posts_block( $block_content, $block ) {
	if ( ! empty( $GLOBALS['elevora_rendering_main_sidebar'] ) && isset( $block['blockName'] ) && 'core/latest-posts' === $block['blockName'] ) {
		return '';
	}
	return $block_content;
}
add_filter( 'render_block', 'elevora_hide_sidebar_latest_posts_block', 10, 2 );

/**
 * Remove Recent Posts widget instances while the theme's thumbnail list is
 * being rendered. This also covers block widgets, whose outer widget wrapper
 * can otherwise bypass render_block filtering on some WordPress versions.
 *
 * @param array<string, array<int, string>> $sidebars Registered widget IDs.
 * @return array<string, array<int, string>>
 */
function elevora_sidebar_widgets_without_recent_posts( $sidebars ) {
	if ( empty( $GLOBALS['elevora_rendering_main_sidebar'] ) || empty( $sidebars['sidebar-main'] ) ) {
		return $sidebars;
	}

	$block_widgets = get_option( 'widget_block', array() );
	$sidebars['sidebar-main'] = array_values(
		array_filter(
			$sidebars['sidebar-main'],
			static function ( $widget_id ) use ( $block_widgets ) {
				if ( 0 === strpos( $widget_id, 'recent-posts-' ) ) {
					return false;
				}

				if ( 0 !== strpos( $widget_id, 'block-' ) ) {
					return true;
				}

				$number  = absint( substr( $widget_id, 6 ) );
				$content = isset( $block_widgets[ $number ]['content'] ) ? (string) $block_widgets[ $number ]['content'] : '';

				return false === strpos( $content, 'wp:latest-posts' ) && false === strpos( $content, '"idBase":"recent-posts"' );
			}
		)
	);

	return $sidebars;
}

function elevora_fallback_menu() {
	echo '<ul class="primary-menu">';
	wp_list_pages( array( 'title_li' => '', 'depth' => 1 ) );
	echo '</ul>';
}

function elevora_pagination( $query = null ) {
	$query = $query ?: $GLOBALS['wp_query'];
	if ( $query->max_num_pages < 2 ) {
		return;
	}
	$current = max( 1, absint( $query->get( 'paged' ) ) );
	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'gravitas-coaching' ) . '">';
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $query->max_num_pages,
				'current'   => $current,
				'mid_size'  => 1,
				'prev_text' => '&larr; ' . __( 'Previous', 'gravitas-coaching' ),
				'next_text' => __( 'Next', 'gravitas-coaching' ) . ' &rarr;',
			)
		)
	);
	echo '</nav>';
}

function elevora_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'gravitas-coaching' ) . '"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'gravitas-coaching' ) . '</a><span>/</span>';
	if ( is_singular( 'trainer' ) ) {
		echo '<a href="' . esc_url( get_post_type_archive_link( 'trainer' ) ) . '">' . esc_html__( 'Trainers', 'gravitas-coaching' ) . '</a><span>/</span>';
	} elseif ( is_singular( 'coaching_service' ) ) {
		echo '<a href="' . esc_url( get_post_type_archive_link( 'coaching_service' ) ) . '">' . esc_html__( 'Services', 'gravitas-coaching' ) . '</a><span>/</span>';
	} elseif ( is_singular( 'post' ) ) {
		$journal_url = get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' );
		echo '<a href="' . esc_url( $journal_url ) . '">' . esc_html__( 'Journal', 'gravitas-coaching' ) . '</a><span>/</span>';
	}
	echo '<span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_title() ?: get_the_archive_title() ) ) . '</span></nav>';
}

function elevora_term_links( $post_id, $taxonomy, $limit = 3 ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}
	$terms = array_slice( $terms, 0, $limit );
	foreach ( $terms as $term ) {
		echo '<a class="tag" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
	}
}

function elevora_reading_time( $post_id = 0 ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $words / 220 ) );
}

function elevora_posted_meta() {
	$items = array();
	if ( elevora_get_setting( 'show_post_date', '', 1 ) ) {
		$items[] = get_the_date();
	}
	if ( elevora_get_setting( 'show_post_author', '', 1 ) ) {
		$items[] = get_the_author();
	}
	if ( elevora_get_setting( 'show_reading_time', '', 1 ) ) {
		$items[] = sprintf( _n( '%s min read', '%s min read', elevora_reading_time(), 'gravitas-coaching' ), elevora_reading_time() );
	}
	if ( elevora_get_setting( 'show_modified_date', '', 0 ) ) {
		$items[] = sprintf( __( 'Updated %s', 'gravitas-coaching' ), get_the_modified_date() );
	}
	if ( elevora_get_setting( 'show_comment_count', '', 0 ) ) {
		$items[] = sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'gravitas-coaching' ), number_format_i18n( get_comments_number() ) );
	}
	foreach ( $items as $item ) {
		echo '<span>' . esc_html( $item ) . '</span>';
	}
}

function elevora_social_share() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	$links = array(
		'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		'X'        => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
		'Email'    => 'mailto:?subject=' . $title . '&body=' . $url,
	);
	echo '<div class="share-links"><span>' . esc_html__( 'Share', 'gravitas-coaching' ) . '</span>';
	foreach ( $links as $label => $link ) {
		echo '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a>';
	}
	echo '</div>';
}

function elevora_related_posts( $post_type = 'post', $count = 3 ) {
	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $count,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => elevora_get_setting( 'related_orderby', '', 'date' ),
		'order'          => elevora_get_setting( 'related_order', '', 'DESC' ),
	);
	if ( 'post' === $post_type ) {
		$match = elevora_get_setting( 'related_posts_by', '', 'category' );
		if ( 'category' === $match ) {
			$args['category__in'] = wp_get_post_categories( get_the_ID() );
		} elseif ( 'tag' === $match ) {
			$args['tag__in'] = wp_get_post_tags( get_the_ID(), array( 'fields' => 'ids' ) );
		} elseif ( 'author' === $match ) {
			$args['author'] = get_the_author_meta( 'ID' );
		}
	}
	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<section class="section related-section">
		<div class="container">
			<div class="section-heading"><div><span class="eyebrow"><?php esc_html_e( 'Keep exploring', 'gravitas-coaching' ); ?></span><h2><?php echo esc_html( elevora_get_setting( 'related_posts_title', '', __( 'You may also like', 'gravitas-coaching' ) ) ); ?></h2></div></div>
			<div class="card-grid card-grid--three">
				<?php while ( $query->have_posts() ) : $query->the_post();
					if ( 'trainer' === $post_type ) {
						get_template_part( 'template-parts/card', 'trainer' );
					} elseif ( 'coaching_service' === $post_type ) {
						get_template_part( 'template-parts/card', 'service' );
					} else {
						get_template_part( 'template-parts/card', 'post', array( 'show_date' => (bool) elevora_get_setting( 'related_show_date', '', 1 ) ) );
					}
				endwhile; ?>
			</div>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}

/** Render the category, title, and metadata group used above or below media. */
function elevora_single_post_title_block( $below = false ) {
	?>
	<div class="<?php echo $below ? 'article-title-below' : ''; ?>">
		<?php if ( elevora_get_setting( 'show_post_categories', '', 1 ) ) : ?><div class="tag-list"><?php foreach ( get_the_category() as $category ) : ?><a class="tag" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endforeach; ?></div><?php endif; ?>
		<h1><?php the_title(); ?></h1>
		<?php if ( elevora_get_setting( 'show_post_date', '', 1 ) || elevora_get_setting( 'show_post_author', '', 1 ) || elevora_get_setting( 'show_reading_time', '', 1 ) || elevora_get_setting( 'show_modified_date', '', 0 ) || elevora_get_setting( 'show_comment_count', '', 0 ) ) : ?><div class="article-meta"><?php elevora_posted_meta(); ?></div><?php endif; ?>
	</div>
	<?php
}

/** Render the optional single-post author biography. */
function elevora_post_author_box() {
	$description = get_the_author_meta( 'description' );
	?>
	<div class="article-author-box"><?php echo get_avatar( get_the_author_meta( 'ID' ), 72 ); ?><div><span class="eyebrow"><?php esc_html_e( 'Written by', 'gravitas-coaching' ); ?></span><h3><?php the_author(); ?></h3><?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?></div></div>
	<?php
}

/** Apply the configured single-post comment fields with Soledad-style placeholders. */
function elevora_comment_form_fields( $fields ) {
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );

	$fields['author'] = '<p class="comment-form-author"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . esc_attr__( 'Name*', 'gravitas-coaching' ) . '" size="30"' . $aria_req . ' required /></p>';
	$fields['email']  = '<p class="comment-form-email"><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . esc_attr__( 'Email*', 'gravitas-coaching' ) . '" size="30"' . $aria_req . ' required /></p>';
	$fields['url']    = '<p class="comment-form-url"><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . esc_attr__( 'Website', 'gravitas-coaching' ) . '" size="30" /></p>';

	if ( elevora_get_setting( 'comments_remove_website', '', 0 ) ) {
		unset( $fields['url'] );
	}
	if ( elevora_get_setting( 'comments_remove_cookies', '', 0 ) ) {
		unset( $fields['cookies'] );
	}
	return $fields;
}
add_filter( 'comment_form_default_fields', 'elevora_comment_form_fields' );

/** Add an optional privacy notice to the comment form. */
function elevora_comment_form_defaults( $defaults ) {
	if ( elevora_get_setting( 'comments_gdpr', '', 0 ) ) {
		$notice = elevora_get_setting( 'comments_gdpr_text', '', __( 'By using this form you agree with the storage and handling of your data by this website.', 'gravitas-coaching' ) );
		$defaults['comment_notes_after'] = ( isset( $defaults['comment_notes_after'] ) ? $defaults['comment_notes_after'] : '' ) . '<p class="comment-gdpr-notice">' . esc_html( $notice ) . '</p>';
	}
	return $defaults;
}
add_filter( 'comment_form_defaults', 'elevora_comment_form_defaults' );

function elevora_output_schema() {
	if ( is_singular( 'trainer' ) ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => get_the_title(),
			'url'         => get_permalink(),
			'image'       => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
			'jobTitle'    => elevora_get_field_value( 'elevora_role' ),
			'description' => get_the_excerpt(),
			'email'       => elevora_get_field_value( 'elevora_email' ),
			'telephone'   => elevora_get_field_value( 'elevora_phone' ),
		);
	} elseif ( is_singular( 'coaching_service' ) ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => get_the_title(),
			'url'         => get_permalink(),
			'description' => get_the_excerpt(),
			'provider'    => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ) ),
			'offers'      => array( '@type' => 'Offer', 'price' => elevora_get_field_value( 'elevora_service_price' ) ),
		);
	} else {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $schema ), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>';
}
add_action( 'wp_head', 'elevora_output_schema', 30 );

function elevora_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'elevora_excerpt_more' );

function elevora_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'elevora_excerpt_length', 99 );
