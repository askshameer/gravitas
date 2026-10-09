<?php
/**
 * Front-end theme settings.
 *
 * @package Gravitas_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Core_Settings {
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'upgrade_compact_titles' ) );
		add_action( 'admin_init', array( $this, 'upgrade_typography' ) );
		add_action( 'admin_init', array( $this, 'upgrade_typography_v2' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 5 );
		add_action( 'wp_head', array( $this, 'output_css' ), 30 );
		add_filter( 'theme_mod_custom_logo', array( $this, 'filter_logo' ) );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'upload_mimes', array( $this, 'allow_font_mimes' ) );
		add_action( 'wp_footer', array( $this, 'footer_tools' ), 50 );
		add_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10, 3 );
		add_action( 'add_option_gravitas_core_options', array( $this, 'options_added' ), 10, 2 );
	}

	public static function defaults() {
		return array(
			'logo_id'             => 0,
			'light_logo_id'       => 0,
			'dark_logo_id'        => 0,
			'accent_color'        => '#c6a454',
			'secondary_color'     => '#d8a65b',
			'ink_color'           => '#0c1d30',
			'paper_color'         => '#f7f3eb',
			'dark_color'          => '#0c1d30',
			'body_font'           => 'professional',
			'heading_font'        => 'professional',
			'nav_font'            => 'professional',
			'custom_font_url'     => '',
			'body_size'           => 16,
			'container_width'     => 1200,
			'border_radius'       => 18,
			'header_layout'       => 'standard',
			'sticky_header'       => 1,
			'show_announcement'   => 1,
			'show_search'         => 1,
			'show_dark_toggle'    => 1,
			'header_cta_text'     => __( 'Book a Consultation', 'gravitas-core' ),
			'header_cta_url'      => '#contact',
			'default_scheme'      => 'light',
			'hero_image_id'       => 0,
			'hero_eyebrow'        => __( 'Soft skills. Stronger presence.', 'gravitas-core' ),
			'hero_title'          => __( 'Unlock Your Next Level', 'gravitas-core' ),
			'hero_highlight'      => __( 'of Growth and Leadership', 'gravitas-core' ),
			'hero_text'           => __( 'Build your image, presence, and essential soft skills for a more confident professional future.', 'gravitas-core' ),
			'hero_primary_text'   => __( 'Find your consultant', 'gravitas-core' ),
			'hero_primary_url'    => '',
			'hero_secondary_text' => __( 'Explore programs', 'gravitas-core' ),
			'hero_secondary_url'  => '',
			'hero_min_height'     => 510,
			'hero_overlay'        => 100,
			'hero_alignment'      => 'left',
			'page_header_height'  => 160,
			'page_title_size'     => 44,
			'post_header_height'  => 160,
			'post_title_size'     => 48,
			'post_title_alignment'=> 'left',
			'post_title_position' => 'before',
			'single_content_width'=> 780,
			'single_sidebar'      => 'right',
			'sidebar_ad_image_id' => 0,
			'sidebar_ad_url'      => '',
			'sidebar_ad_new_tab'  => 1,
			'post_content_size'   => 18,
			'post_meta_size'      => 14,
			'post_h2_size'        => 38,
			'featured_radius'     => 18,
			'blockquote_style'    => 'accent',
			'show_post_breadcrumbs' => 1,
			'show_post_categories'=> 1,
			'show_post_date'      => 1,
			'show_post_author'    => 1,
			'show_reading_time'   => 1,
			'show_modified_date'  => 0,
			'show_comment_count'  => 0,
			'show_post_featured'  => 1,
			'show_featured_caption' => 0,
			'show_post_tags'      => 1,
			'show_author_box'     => 1,
			'show_post_share'     => 1,
			'show_post_navigation'=> 1,
			'show_post_comments'  => 1,
			'comments_remove_website' => 0,
			'comments_remove_cookies' => 0,
			'comments_gdpr'       => 0,
			'comments_gdpr_text'  => __( 'By using this form you agree with the storage and handling of your data by this website.', 'gravitas-core' ),
			'show_related_posts'  => 1,
			'related_posts_count' => 3,
			'related_posts_title' => __( 'You may also like', 'gravitas-core' ),
			'related_posts_by'    => 'category',
			'related_orderby'     => 'date',
			'related_order'       => 'DESC',
			'related_show_date'   => 1,
			'hide_related_mobile' => 0,
			'enable_reading_progress' => 0,
			'reading_progress_position' => 'top',
			'reading_progress_height' => 4,
			'reading_progress_color' => '#c6a454',
			'show_page_breadcrumbs' => 1,
			'show_page_featured'  => 0,
			'blog_layout'         => 'editorial',
			'blog_pagination'     => 'numbers',
			'sidebar_position'    => 'none',
			'footer_description'  => __( 'Soft skills coaching and image consulting for professionals who want their presence to match their potential.', 'gravitas-core' ),
			'copyright_text'      => '',
			'phone'               => '+1 (800) 555-0144',
			'email'               => 'hello@example.com',
			'instagram_url'       => '',
			'linkedin_url'        => '',
			'youtube_url'         => '',
			'facebook_url'        => '',
			'custom_css'          => '',
			'enable_schema'       => 1,
			'enable_back_to_top'  => 1,
			'enable_lazy_loading' => 1,
		);
	}

	/** Migrate previous default sizes once, retaining custom values. */
	public function upgrade_compact_titles() {
		if ( ! current_user_can( 'manage_options' ) || get_option( 'gravitas_compact_titles_v1' ) ) {
			return;
		}
		$options = (array) get_option( 'gravitas_core_options', array() );
		foreach ( array( 'page_header_height' => array( 300, 160 ), 'post_header_height' => array( 270, 160 ), 'page_title_size' => array( 72, 44 ), 'post_title_size' => array( 74, 48 ) ) as $key => $values ) {
			if ( ! isset( $options[ $key ] ) || (int) $options[ $key ] === $values[0] ) {
				$options[ $key ] = $values[1];
			}
		}
		// Updating options would also overwrite independently edited Elementor hero text.
		remove_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10 );
		update_option( 'gravitas_core_options', $options, false );
		add_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10, 3 );
		update_option( 'gravitas_compact_titles_v1', 1, false );
	}

	/** Refresh the old generic defaults while preserving deliberate font choices. */
	public function upgrade_typography() {
		if ( ! current_user_can( 'manage_options' ) || get_option( 'gravitas_typography_refresh_v1' ) ) {
			return;
		}
		$options = (array) get_option( 'gravitas_core_options', array() );
		$map     = array( 'body_font' => array( 'system', 'editorial_sans' ), 'heading_font' => array( 'serif', 'literary' ), 'nav_font' => array( 'system', 'editorial_sans' ) );
		foreach ( $map as $key => $values ) {
			if ( ! isset( $options[ $key ] ) || $values[0] === $options[ $key ] ) {
				$options[ $key ] = $values[1];
			}
		}
		remove_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10 );
		update_option( 'gravitas_core_options', $options, false );
		add_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10, 3 );
		update_option( 'gravitas_typography_refresh_v1', 1, false );
	}

	/** Replace the generated editorial pairing with a restrained professional sans family. */
	public function upgrade_typography_v2() {
		if ( ! current_user_can( 'manage_options' ) || get_option( 'gravitas_typography_refresh_v2' ) ) {
			return;
		}
		$options = (array) get_option( 'gravitas_core_options', array() );
		$map     = array( 'body_font' => 'editorial_sans', 'heading_font' => 'literary', 'nav_font' => 'editorial_sans' );
		foreach ( $map as $key => $previous_default ) {
			if ( ! isset( $options[ $key ] ) || $previous_default === $options[ $key ] ) {
				$options[ $key ] = 'professional';
			}
		}
		remove_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10 );
		update_option( 'gravitas_core_options', $options, false );
		add_action( 'update_option_gravitas_core_options', array( $this, 'options_updated' ), 10, 3 );
		update_option( 'gravitas_typography_refresh_v2', 1, false );
	}

	public function register() {
		register_setting(
			'gravitas_core_group',
			'gravitas_core_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	public function get( $key, $default = null ) {
		$defaults = self::defaults();
		$fallback = null === $default && isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default;
		$options  = wp_parse_args( get_option( 'gravitas_core_options', array() ), $defaults );
		return isset( $options[ $key ] ) ? $options[ $key ] : $fallback;
	}

	public function sanitize( $input ) {
		$defaults = self::defaults();
		$output   = array();
		$colors   = array( 'accent_color', 'secondary_color', 'ink_color', 'paper_color', 'dark_color', 'reading_progress_color' );
		$urls     = array( 'hero_primary_url', 'hero_secondary_url', 'sidebar_ad_url', 'custom_font_url', 'instagram_url', 'linkedin_url', 'youtube_url', 'facebook_url' );
		$checks   = array( 'sticky_header', 'show_announcement', 'show_search', 'show_dark_toggle', 'sidebar_ad_new_tab', 'show_post_breadcrumbs', 'show_post_categories', 'show_post_date', 'show_post_author', 'show_reading_time', 'show_modified_date', 'show_comment_count', 'show_post_featured', 'show_featured_caption', 'show_post_tags', 'show_author_box', 'show_post_share', 'show_post_navigation', 'show_post_comments', 'comments_remove_website', 'comments_remove_cookies', 'comments_gdpr', 'show_related_posts', 'related_show_date', 'hide_related_mobile', 'enable_reading_progress', 'show_page_breadcrumbs', 'show_page_featured', 'enable_schema', 'enable_back_to_top', 'enable_lazy_loading' );

		foreach ( $defaults as $key => $default ) {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : ( in_array( $key, $checks, true ) ? 0 : $default );
			if ( in_array( $key, $colors, true ) ) {
				$output[ $key ] = sanitize_hex_color( $value ) ?: $default;
			} elseif ( 'header_cta_url' === $key ) {
				$raw = trim( wp_unslash( (string) $value ) );
				$output[ $key ] = preg_match( '/\[.+\]/', $raw ) ? wp_kses_post( $raw ) : esc_url_raw( $raw );
			} elseif ( in_array( $key, $urls, true ) ) {
				$output[ $key ] = esc_url_raw( $value );
			} elseif ( in_array( $key, $checks, true ) ) {
				$output[ $key ] = empty( $value ) ? 0 : 1;
			} elseif ( in_array( $key, array( 'logo_id', 'light_logo_id', 'dark_logo_id', 'hero_image_id', 'sidebar_ad_image_id' ), true ) ) {
				$output[ $key ] = absint( $value );
			} elseif ( 'body_size' === $key ) {
				$output[ $key ] = min( 24, max( 12, absint( $value ) ) );
			} elseif ( 'container_width' === $key ) {
				$output[ $key ] = min( 1600, max( 960, absint( $value ) ) );
			} elseif ( 'border_radius' === $key ) {
				$output[ $key ] = min( 48, max( 0, absint( $value ) ) );
			} elseif ( 'hero_min_height' === $key ) {
				$output[ $key ] = min( 900, max( 360, absint( $value ) ) );
			} elseif ( 'hero_overlay' === $key ) {
				$output[ $key ] = min( 100, max( 0, absint( $value ) ) );
			} elseif ( in_array( $key, array( 'page_header_height', 'post_header_height' ), true ) ) {
				$output[ $key ] = min( 720, max( 120, absint( $value ) ) );
			} elseif ( in_array( $key, array( 'page_title_size', 'post_title_size' ), true ) ) {
				$output[ $key ] = min( 120, max( 24, absint( $value ) ) );
			} elseif ( 'single_content_width' === $key ) {
				$output[ $key ] = min( 1200, max( 560, absint( $value ) ) );
			} elseif ( 'related_posts_count' === $key ) {
				$output[ $key ] = min( 6, max( 1, absint( $value ) ) );
			} elseif ( in_array( $key, array( 'post_content_size', 'post_meta_size' ), true ) ) {
				$output[ $key ] = min( 28, max( 10, absint( $value ) ) );
			} elseif ( 'post_h2_size' === $key ) {
				$output[ $key ] = min( 72, max( 20, absint( $value ) ) );
			} elseif ( 'featured_radius' === $key ) {
				$output[ $key ] = min( 80, max( 0, absint( $value ) ) );
			} elseif ( 'reading_progress_height' === $key ) {
				$output[ $key ] = min( 12, max( 2, absint( $value ) ) );
			} elseif ( 'single_sidebar' === $key ) {
				$output[ $key ] = in_array( $value, array( 'right', 'left', 'none' ), true ) ? $value : 'right';
			} elseif ( 'blog_pagination' === $key ) {
				$output[ $key ] = in_array( $value, array( 'numbers', 'load_more' ), true ) ? $value : 'numbers';
			} elseif ( 'email' === $key ) {
				$output[ $key ] = sanitize_email( $value );
			} elseif ( in_array( $key, array( 'footer_description', 'hero_text', 'comments_gdpr_text' ), true ) ) {
				$output[ $key ] = sanitize_textarea_field( $value );
			} elseif ( 'custom_css' === $key ) {
				$output[ $key ] = wp_strip_all_tags( $value );
			} else {
				$output[ $key ] = sanitize_text_field( $value );
			}
		}

		$this->sync_theme_mods( $output );

		return $output;
	}

	/**
	 * Mirror options used by the theme's Customizer-compatible fallbacks.
	 *
	 * @param array<string, mixed> $options Sanitized settings.
	 */
	private function sync_theme_mods( $options ) {
		$map = array(
			'light_logo_id'       => 'elevora_light_logo',
			'dark_logo_id'        => 'elevora_dark_logo',
			'accent_color'        => 'elevora_accent',
			'header_cta_text'     => 'elevora_header_cta',
			'header_cta_url'      => 'elevora_header_url',
			'footer_description'  => 'elevora_footer_text',
			'phone'               => 'elevora_phone',
			'email'               => 'elevora_email',
			'hero_image_id'       => 'elevora_hero_image',
			'hero_eyebrow'        => 'elevora_hero_eyebrow',
			'hero_title'          => 'elevora_hero_title',
			'hero_highlight'      => 'elevora_hero_highlight',
			'hero_text'           => 'elevora_hero_text',
			'hero_primary_text'   => 'elevora_hero_button',
			'hero_primary_url'    => 'elevora_hero_url',
			'hero_secondary_text' => 'elevora_hero_alt_text',
			'hero_secondary_url'  => 'elevora_hero_alt_url',
			'blog_layout'         => 'elevora_blog_layout',
			'single_sidebar'      => 'elevora_single_sidebar',
		);

		foreach ( $map as $option_key => $theme_mod ) {
			if ( array_key_exists( $option_key, $options ) ) {
				set_theme_mod( $theme_mod, $options[ $option_key ] );
			}
		}
		if ( isset( $options['sidebar_position'] ) ) {
			set_theme_mod( 'elevora_blog_sidebar', 'none' !== $options['sidebar_position'] );
		}
	}

	/**
	 * Apply saved settings to generated front-end assets and demo Elementor data.
	 *
	 * @param mixed  $old_value Previous option value.
	 * @param mixed  $value     New option value.
	 * @param string $option    Option name.
	 */
	public function options_updated( $old_value, $value, $option ) {
		unset( $old_value, $option );
		$this->after_options_saved( (array) $value );
	}

	/**
	 * Apply settings when the option is saved for the first time.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  New option value.
	 */
	public function options_added( $option, $value ) {
		unset( $option );
		$this->after_options_saved( (array) $value );
	}

	/**
	 * Refresh all consumers of Theme Options after a successful save.
	 *
	 * @param array<string, mixed> $options Saved settings.
	 */
	private function after_options_saved( $options ) {
		$options = wp_parse_args( $options, self::defaults() );
		$this->sync_theme_mods( $options );
		$this->sync_elementor_home( $options );

		if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) && method_exists( \Elementor\Plugin::$instance->files_manager, 'clear_cache' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * Update only the known demo-home Elementor elements controlled by Theme Options.
	 *
	 * User-created elements and all other Elementor settings remain untouched.
	 *
	 * @param array<string, mixed> $options Saved settings.
	 */
	private function sync_elementor_home( $options ) {
		$home_id = absint( get_option( 'page_on_front' ) );
		if ( ! $home_id ) {
			return;
		}

		$raw_data = get_post_meta( $home_id, '_elementor_data', true );
		$elements = json_decode( $raw_data, true );
		if ( ! is_array( $elements ) ) {
			return;
		}

		$image_id  = absint( $options['hero_image_id'] );
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$alignment = 'center' === $options['hero_alignment'] ? 'center' : 'left';
		$opacity   = min( 100, max( 0, absint( $options['hero_overlay'] ) ) ) / 100;
		$changed   = $this->update_elementor_elements(
			$elements,
			array(
				'gvhero01' => array( 'hero_image' => array( 'id' => $image_id, 'url' => $image_url ), 'hero_eyebrow' => $options['hero_eyebrow'], 'hero_title' => $options['hero_title'], 'hero_highlight' => $options['hero_highlight'], 'hero_text' => $options['hero_text'], 'hero_primary_text' => $options['hero_primary_text'], 'hero_primary_url' => $options['hero_primary_url'], 'hero_secondary_text' => $options['hero_secondary_text'], 'hero_secondary_url' => $options['hero_secondary_url'] ),
				'gvheroey' => array( 'title' => $options['hero_eyebrow'], 'title_color' => $options['accent_color'], 'align' => $alignment ),
				'gvherohd' => array( 'title' => trim( $options['hero_title'] . ' ' . $options['hero_highlight'] ), 'align' => $alignment ),
				'gvherotx' => array( 'editor' => '<p>' . esc_html( $options['hero_text'] ) . '</p>', 'align' => $alignment ),
				'gvherobt' => array( 'text' => $options['hero_primary_text'], 'link' => array( 'url' => $options['hero_primary_url'] ), 'background_color' => $options['accent_color'], 'align' => $alignment ),
				'gvhero2b' => array( 'text' => $options['hero_secondary_text'], 'link' => array( 'url' => $options['hero_secondary_url'] ), 'align' => $alignment ),
				'gvblog01' => array( 'background_color' => $options['dark_color'] ),
			)
		);

		if ( $changed ) {
			update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
			clean_post_cache( $home_id );
		}
	}

	/**
	 * Recursively update Elementor elements by their stable demo IDs.
	 *
	 * @param array<int, array<string, mixed>> $elements Elementor elements.
	 * @param array<string, array<string, mixed>> $updates Settings keyed by element ID.
	 * @return bool
	 */
	private function update_elementor_elements( &$elements, $updates ) {
		$changed = false;
		foreach ( $elements as &$element ) {
			$id = isset( $element['id'] ) ? $element['id'] : '';
			if ( isset( $updates[ $id ] ) ) {
				$element['settings'] = array_merge( isset( $element['settings'] ) ? (array) $element['settings'] : array(), $updates[ $id ] );
				$changed             = true;
			}
			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) && $this->update_elementor_elements( $element['elements'], $updates ) ) {
				$changed = true;
			}
		}
		unset( $element );

		return $changed;
	}

	public function filter_logo( $logo_id ) {
		$custom_logo = absint( $this->get( 'light_logo_id' ) ) ?: absint( $this->get( 'logo_id' ) );
		return $custom_logo ?: $logo_id;
	}

	public function body_classes( $classes ) {
		$classes[] = 'gravitas-header-' . sanitize_html_class( $this->get( 'header_layout' ) );
		$classes[] = 'gravitas-blog-' . sanitize_html_class( $this->get( 'blog_layout' ) );
		$classes[] = 'gravitas-sidebar-' . sanitize_html_class( $this->get( 'sidebar_position' ) );
		if ( is_singular( 'post' ) ) {
			$classes[] = 'gravitas-single-sidebar-' . sanitize_html_class( $this->get( 'single_sidebar', 'right' ) );
			$classes[] = 'gravitas-post-title-' . sanitize_html_class( $this->get( 'post_title_alignment' ) );
			$classes[] = 'gravitas-post-title-' . sanitize_html_class( $this->get( 'post_title_position' ) );
			$classes[] = 'gravitas-blockquote-' . sanitize_html_class( $this->get( 'blockquote_style' ) );
		}
		$classes[] = 'gravitas-hero-' . sanitize_html_class( $this->get( 'hero_alignment' ) );
		if ( $this->get( 'sticky_header' ) ) {
			$classes[] = 'gravitas-sticky-header';
		}
		if ( 'dark' === $this->get( 'default_scheme' ) ) {
			$classes[] = 'dark-scheme';
		}
		return $classes;
	}

	public function enqueue_fonts() {
		$url = $this->get( 'custom_font_url' );
		if ( $url ) {
			wp_enqueue_style( 'gravitas-custom-font', $url, array(), null );
		}
	}

	private function font_stack( $choice ) {
		$stacks = array(
			'professional' => '"Helvetica Neue",Helvetica,Arial,"Nimbus Sans L",sans-serif',
			'system'    => '-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
			'serif'     => 'Georgia,"Times New Roman",serif',
			'editorial_sans' => 'Aptos,"Segoe UI Variable","Segoe UI",Candara,sans-serif',
			'literary'  => '"Iowan Old Style","Palatino Linotype","Book Antiqua",Palatino,Georgia,serif',
			'humanist'  => '"Trebuchet MS",Verdana,sans-serif',
			'geometric' => 'Montserrat,Poppins,"Segoe UI",sans-serif',
			'elegant'   => 'Cormorant Garamond,Georgia,serif',
		);
		return isset( $stacks[ $choice ] ) ? $stacks[ $choice ] : $stacks['system'];
	}

	public function output_css() {
		$css = ':root{' .
			'--color-accent:' . $this->get( 'accent_color' ) . ';' .
			'--color-gold:' . $this->get( 'secondary_color' ) . ';' .
			'--color-ink:' . $this->get( 'ink_color' ) . ';' .
			'--color-paper:' . $this->get( 'paper_color' ) . ';' .
			'--color-dark:' . $this->get( 'dark_color' ) . ';' .
			'--font-body:' . $this->font_stack( $this->get( 'body_font' ) ) . ';' .
			'--font-heading:' . $this->font_stack( $this->get( 'heading_font' ) ) . ';' .
			'--font-nav:' . $this->font_stack( $this->get( 'nav_font' ) ) . ';' .
			'--container:' . absint( $this->get( 'container_width' ) ) . 'px;' .
			'--radius:' . absint( $this->get( 'border_radius' ) ) . 'px;' .
			'--page-header-height:' . absint( $this->get( 'page_header_height' ) ) . 'px;' .
			'--page-title-size:' . absint( $this->get( 'page_title_size' ) ) . 'px;' .
			'--post-header-height:' . absint( $this->get( 'post_header_height' ) ) . 'px;' .
			'--post-title-size:' . absint( $this->get( 'post_title_size' ) ) . 'px;' .
			'--single-content-width:' . absint( $this->get( 'single_content_width' ) ) . 'px;' .
			'--post-content-size:' . absint( $this->get( 'post_content_size' ) ) . 'px;' .
			'--post-meta-size:' . absint( $this->get( 'post_meta_size' ) ) . 'px;' .
			'--post-h2-size:' . absint( $this->get( 'post_h2_size' ) ) . 'px;' .
			'--featured-radius:' . absint( $this->get( 'featured_radius' ) ) . 'px;' .
			'--reading-progress-color:' . $this->get( 'reading_progress_color' ) . ';' .
			'--reading-progress-height:' . absint( $this->get( 'reading_progress_height' ) ) . 'px;' .
			'}body{font-family:var(--font-body);font-size:' . absint( $this->get( 'body_size' ) ) . 'px;}h1,h2,h3,h4,h5,h6{font-family:var(--font-heading);}.primary-navigation{font-family:var(--font-nav);}.page-main>.page-hero{min-height:var(--page-header-height);display:flex;align-items:center}.page-main>.page-hero h1{font-size:clamp(1.5rem,6vw,var(--page-title-size))}.single-post-main .article-header{min-height:0;display:block}.single-post-main .article-header h1{font-size:clamp(1.5rem,6vw,var(--post-title-size))}.hero{min-height:' . absint( $this->get( 'hero_min_height' ) ) . 'px}.hero__wash{opacity:' . ( absint( $this->get( 'hero_overlay' ) ) / 100 ) . '}.gravitas-hero-center .hero__content{margin-inline:auto;text-align:center}.gravitas-hero-center .hero__actions{justify-content:center}.gravitas-hero-center .hero__content>p{margin-inline:auto}';
		if ( ! $this->get( 'show_announcement' ) ) {
			$css .= '.announcement-bar{display:none!important;}';
		}
		if ( ! $this->get( 'show_search' ) ) {
			$css .= '[data-search-open]{display:none!important;}';
		}
		if ( ! $this->get( 'show_dark_toggle' ) ) {
			$css .= '[data-theme-toggle]{display:none!important;}';
		}
		if ( ! $this->get( 'sticky_header' ) ) {
			$css .= '.site-header.is-sticky{position:relative!important;top:auto!important;box-shadow:none!important}.header-placeholder{display:none!important;}';
		}
		$css .= '.gravitas-header-centered .header-inner{flex-wrap:wrap;justify-content:center}.gravitas-header-centered .brand-wrap{flex-basis:100%;text-align:center}.gravitas-header-minimal .announcement-bar,.gravitas-header-minimal .header-cta{display:none}body.gravitas-sidebar-none:not(.single-post) .content-with-sidebar,body.gravitas-sidebar-none:not(.single-post) .article-layout{grid-template-columns:1fr!important}body.gravitas-sidebar-none:not(.single-post) .site-sidebar{display:none}.single-post-main.gravitas-single-sidebar-none .site-sidebar{display:none!important}.single-post-main.gravitas-single-sidebar-none .article-layout{grid-template-columns:minmax(0,var(--single-content-width))!important;justify-content:center}.single-post-main.gravitas-single-sidebar-left .article-layout{grid-template-columns:minmax(260px,320px) minmax(0,1fr)}.single-post-main.gravitas-single-sidebar-left .article-layout>div:first-child{grid-column:2;grid-row:1}.single-post-main.gravitas-single-sidebar-left .site-sidebar{grid-column:1;grid-row:1}.gravitas-post-title-center .single-post-main .article-header{text-align:center}.gravitas-post-title-center .single-post-main .breadcrumbs,.gravitas-post-title-center .single-post-main .tag-list,.gravitas-post-title-center .single-post-main .article-meta{justify-content:center}.gravitas-blog-list .card-grid{grid-template-columns:1fr!important}.gravitas-back-to-top{position:fixed;right:22px;bottom:22px;z-index:90;width:44px;height:44px;border:0;border-radius:50%;background:var(--color-accent);color:#fff;box-shadow:0 8px 25px rgba(0,0,0,.18);cursor:pointer}.gravitas-footer-social{display:flex;gap:10px;margin-top:16px}.gravitas-footer-social a{color:#fff;text-decoration:none;font-size:12px;text-transform:uppercase;letter-spacing:.05em}@media(max-width:760px){.single-post-main.gravitas-single-sidebar-left .article-layout{grid-template-columns:1fr}.single-post-main.gravitas-single-sidebar-left .article-layout>div:first-child{grid-column:1;grid-row:1}.single-post-main.gravitas-single-sidebar-left .site-sidebar{grid-column:1;grid-row:auto}}';
		$css .= '.single-post-main .entry-content{font-size:var(--post-content-size)}.single-post-main .entry-content h2{font-size:var(--post-h2-size)}.single-post-main .article-meta{font-size:var(--post-meta-size)}.single-post-main .article-featured img{border-radius:var(--featured-radius)}.gravitas-blockquote-plain .single-post-main blockquote{border-left:0;background:transparent}.gravitas-blockquote-boxed .single-post-main blockquote{border:1px solid rgba(12,29,48,.15);border-left-width:1px;border-radius:var(--radius);background:var(--color-paper);padding:28px}.gravitas-reading-progress{position:fixed;left:0;right:0;z-index:99999;height:var(--reading-progress-height);background:rgba(0,0,0,.08)}.gravitas-reading-progress.is-top{top:0}.admin-bar .gravitas-reading-progress.is-top{top:32px}.gravitas-reading-progress.is-bottom{bottom:0}.gravitas-reading-progress span{display:block;width:0;height:100%;background:var(--reading-progress-color)}.article-featured-caption{margin-top:8px;color:#6d6d65;font-size:13px;text-align:center}.article-tags,.article-author-box{margin-top:36px}.article-author-box{display:flex;gap:20px;padding:24px;border:1px solid rgba(12,29,48,.12);border-radius:var(--radius)}.article-author-box .avatar{border-radius:50%}.article-title-below{padding-top:38px;padding-bottom:10px}.article-title-below h1{font-size:clamp(2rem,6vw,var(--post-title-size))}';
		if ( $this->get( 'hide_related_mobile' ) ) {
			$css .= '@media(max-width:700px){.related-section{display:none!important}}';
		}
		$css .= '.gravitas-post-title-after .single-post-main .article-header{min-height:auto;padding:24px 0}' . $this->get( 'custom_css' );
		echo '<style id="gravitas-core-css">' . wp_strip_all_tags( $css ) . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( 'dark' === $this->get( 'default_scheme' ) ) {
			echo '<script>if(!localStorage.getItem("elevora-theme")){document.documentElement.dataset.theme="dark";}</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function footer_tools() {
		if ( ! $this->get( 'enable_back_to_top' ) ) {
			return;
		}
		echo '<button type="button" class="gravitas-back-to-top" data-gravitas-top aria-label="' . esc_attr__( 'Back to top', 'gravitas-core' ) . '">&#8593;</button><script>document.querySelector("[data-gravitas-top]").addEventListener("click",function(){window.scrollTo({top:0,behavior:"smooth"});});</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function allow_font_mimes( $mimes ) {
		$mimes['woff']  = 'font/woff';
		$mimes['woff2'] = 'font/woff2';
		return $mimes;
	}
}
