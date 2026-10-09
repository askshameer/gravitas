<?php
/**
 * Elementor widgets for Gravitas Coaching.
 *
 * @package Gravitas_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Connect the plugin to Elementor.
 */
class Gravitas_Core_Elementor {
	/**
	 * Register Elementor hooks.
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', 'gravitas_core_register_elementor_category' );
		add_action( 'elementor/widgets/register', 'gravitas_core_register_elementor_widgets' );
	}
}

/**
 * Register the Gravitas widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor element manager.
 */
function gravitas_core_register_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'gravitas',
		array(
			'title' => esc_html__( 'Gravitas Coaching', 'gravitas-core' ),
			'icon'  => 'fa fa-plug',
		)
	);
}

/**
 * Register Gravitas Elementor widgets.
 *
 * @param \Elementor\Widgets_Manager $manager Elementor widget manager.
 */
function gravitas_core_register_elementor_widgets( $manager ) {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return;
	}

	if ( ! class_exists( 'Gravitas_Elementor_Directory_Widget' ) ) {
		/**
		 * Trainer directory widget.
		 */
		class Gravitas_Elementor_Directory_Widget extends \Elementor\Widget_Base {
			public function get_name() {
				return 'gravitas-directory';
			}

			public function get_title() {
				return esc_html__( 'Gravitas Trainers', 'gravitas-core' );
			}

			public function get_icon() {
				return 'eicon-person';
			}

			public function get_categories() {
				return array( 'gravitas' );
			}

			protected function register_controls() {
				$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-core' ) ) );
				$this->add_control( 'count', array( 'label' => esc_html__( 'Number of trainers', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 6, 'min' => 1, 'max' => 24 ) );
				$this->add_control( 'columns', array( 'label' => esc_html__( 'Columns', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 3, 'options' => array( 2 => '2', 3 => '3', 4 => '4' ) ) );
				$this->add_control( 'specialization', array( 'label' => esc_html__( 'Specialization slug', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'description' => esc_html__( 'Optional. Leave blank to show all trainers.', 'gravitas-core' ) ) );
				$this->end_controls_section();
			}

			protected function render() {
				$settings = $this->get_settings_for_display();
				echo do_shortcode( sprintf( '[elevora_trainers count="%1$d" columns="%2$d" specialization="%3$s"]', absint( $settings['count'] ), absint( $settings['columns'] ), esc_attr( $settings['specialization'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	if ( ! class_exists( 'Gravitas_Elementor_Services_Widget' ) ) {
		/**
		 * Coaching services widget.
		 */
		class Gravitas_Elementor_Services_Widget extends \Elementor\Widget_Base {
			public function get_name() {
				return 'gravitas-services';
			}

			public function get_title() {
				return esc_html__( 'Gravitas Services', 'gravitas-core' );
			}

			public function get_icon() {
				return 'eicon-posts-grid';
			}

			public function get_categories() {
				return array( 'gravitas' );
			}

			protected function register_controls() {
				$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-core' ) ) );
				$this->add_control(
					'count',
					array(
						'label'   => esc_html__( 'Number of services', 'gravitas-core' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'default' => 6,
						'min'     => 1,
						'max'     => 24,
					)
				);
				$this->end_controls_section();
			}

			protected function render() {
				$settings  = $this->get_settings_for_display();
				$shortcode = sprintf( '[elevora_services count="%d"]', absint( $settings['count'] ) );
				echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	if ( ! class_exists( 'Gravitas_Elementor_Posts_Widget' ) ) {
		/**
		 * Blog posts widget.
		 */
		class Gravitas_Elementor_Posts_Widget extends \Elementor\Widget_Base {
			public function get_name() {
				return 'gravitas-posts';
			}

			public function get_title() {
				return esc_html__( 'Gravitas Blog Posts', 'gravitas-core' );
			}

			public function get_icon() {
				return 'eicon-post-list';
			}

			public function get_categories() {
				return array( 'gravitas' );
			}

			protected function register_controls() {
				$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-core' ) ) );
				$this->add_control(
					'count',
					array(
						'label'   => esc_html__( 'Number of posts', 'gravitas-core' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'default' => 6,
						'min'     => 1,
						'max'     => 24,
					)
				);
				$this->add_control(
					'layout',
					array(
						'label'   => esc_html__( 'Layout', 'gravitas-core' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'grid',
						'options' => array(
							'editorial' => esc_html__( 'Editorial Feature', 'gravitas-core' ),
							'magazine'  => esc_html__( 'Modern Magazine', 'gravitas-core' ),
							'masonry'   => esc_html__( 'Creative Masonry', 'gravitas-core' ),
							'grid'      => esc_html__( 'Classic Grid', 'gravitas-core' ),
							'list'      => esc_html__( 'Elegant List', 'gravitas-core' ),
							'minimal'   => esc_html__( 'Minimal Journal', 'gravitas-core' ),
						),
					)
				);
				$this->end_controls_section();
			}

			protected function render() {
				$settings  = $this->get_settings_for_display();
				$shortcode = sprintf(
					'[elevora_posts count="%1$d" layout="%2$s"]',
					absint( $settings['count'] ),
					sanitize_key( $settings['layout'] )
				);
				echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	if ( ! class_exists( 'Gravitas_Elementor_Finder_Widget' ) ) {
		class Gravitas_Elementor_Finder_Widget extends \Elementor\Widget_Base {
			public function get_name() { return 'gravitas-finder'; }
			public function get_title() { return esc_html__( 'Gravitas Coach Finder', 'gravitas-core' ); }
			public function get_icon() { return 'eicon-search'; }
			public function get_categories() { return array( 'gravitas' ); }

			protected function register_controls() {
				$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-core' ) ) );
				$this->add_control( 'eyebrow', array( 'label' => esc_html__( 'Eyebrow', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Start here', 'gravitas-core' ) ) );
				$this->add_control( 'title', array( 'label' => esc_html__( 'Question', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'What would you like to strengthen?', 'gravitas-core' ) ) );
				$this->add_control( 'placeholder', array( 'label' => esc_html__( 'Search placeholder', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Skill, goal, or consultant', 'gravitas-core' ) ) );
				$this->add_control( 'button', array( 'label' => esc_html__( 'Button label', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Find a match', 'gravitas-core' ) ) );
				$this->end_controls_section();
			}

			protected function render() {
				$settings = $this->get_settings_for_display();
				if ( function_exists( 'elevora_home_finder_shortcode' ) ) {
					echo elevora_home_finder_shortcode( array( 'eyebrow' => sanitize_text_field( $settings['eyebrow'] ), 'title' => sanitize_text_field( $settings['title'] ), 'placeholder' => sanitize_text_field( $settings['placeholder'] ), 'button' => sanitize_text_field( $settings['button'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	if ( ! class_exists( 'Gravitas_Elementor_Testimonials_Widget' ) ) {
		class Gravitas_Elementor_Testimonials_Widget extends \Elementor\Widget_Base {
			public function get_name() { return 'gravitas-testimonials'; }
			public function get_title() { return esc_html__( 'Gravitas Testimonials', 'gravitas-core' ); }
			public function get_icon() { return 'eicon-testimonial'; }
			public function get_categories() { return array( 'gravitas' ); }

			protected function register_controls() {
				$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-core' ) ) );
				$this->add_control( 'count', array( 'label' => esc_html__( 'Number of testimonials', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 12 ) );
				$this->end_controls_section();
			}

			protected function render() {
				$settings = $this->get_settings_for_display();
				if ( function_exists( 'elevora_testimonials_shortcode' ) ) {
					echo elevora_testimonials_shortcode( array( 'count' => absint( $settings['count'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	if ( ! class_exists( 'Gravitas_Elementor_Homepage_Widget' ) ) {
		/**
		 * Original Gravitas homepage, backed by the same renderer as front-page.php.
		 */
		class Gravitas_Elementor_Homepage_Widget extends \Elementor\Widget_Base {
			public function get_name() { return 'gravitas-homepage'; }
			public function get_title() { return esc_html__( 'Gravitas Original Homepage', 'gravitas-core' ); }
			public function get_icon() { return 'eicon-site-identity'; }
			public function get_categories() { return array( 'gravitas' ); }

			protected function register_controls() {
				$defaults = function_exists( 'elevora_homepage_defaults' ) ? elevora_homepage_defaults() : array();
				$this->start_controls_section( 'hero_section', array( 'label' => esc_html__( 'Hero', 'gravitas-core' ) ) );
				$this->add_control( 'hero_image', array( 'label' => esc_html__( 'Background image', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => isset( $defaults['hero_image'] ) ? $defaults['hero_image'] : array() ) );
				foreach ( array( 'hero_eyebrow' => 'Eyebrow', 'hero_title' => 'Title', 'hero_highlight' => 'Highlighted line', 'hero_primary_text' => 'Primary button', 'hero_primary_url' => 'Primary URL', 'hero_secondary_text' => 'Secondary button', 'hero_secondary_url' => 'Secondary URL' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' ) );
				}
				$this->add_control( 'hero_text', array( 'label' => esc_html__( 'Description', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => isset( $defaults['hero_text'] ) ? $defaults['hero_text'] : '' ) );
				$this->end_controls_section();

				$this->start_controls_section( 'intro_section', array( 'label' => esc_html__( 'Introduction', 'gravitas-core' ) ) );
				foreach ( array( 'intro_eyebrow' => 'Eyebrow', 'intro_title' => 'Title', 'intro_link_text' => 'Link label', 'intro_bullet_1' => 'Bullet 1', 'intro_bullet_2' => 'Bullet 2', 'intro_bullet_3' => 'Bullet 3' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' ) );
				}
				foreach ( array( 'intro_text' => 'Introduction text', 'intro_detail' => 'Detail text' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' ) );
				}
				$this->end_controls_section();

				$this->start_controls_section( 'process_section', array( 'label' => esc_html__( 'Four-step process', 'gravitas-core' ) ) );
				$this->add_control( 'process_eyebrow', array( 'label' => esc_html__( 'Eyebrow', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults['process_eyebrow'] ) ? $defaults['process_eyebrow'] : '' ) );
				$this->add_control( 'process_title', array( 'label' => esc_html__( 'Title', 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults['process_title'] ) ? $defaults['process_title'] : '' ) );
				for ( $step = 1; $step <= 4; $step++ ) {
					$this->add_control( 'process_' . $step . '_title', array( 'label' => sprintf( esc_html__( 'Step %d title', 'gravitas-core' ), $step ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults[ 'process_' . $step . '_title' ] ) ? $defaults[ 'process_' . $step . '_title' ] : '' ) );
					$this->add_control( 'process_' . $step . '_text', array( 'label' => sprintf( esc_html__( 'Step %d description', 'gravitas-core' ), $step ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => isset( $defaults[ 'process_' . $step . '_text' ] ) ? $defaults[ 'process_' . $step . '_text' ] : '' ) );
				}
				$this->end_controls_section();

				$this->start_controls_section( 'finder_section', array( 'label' => esc_html__( 'Coach finder', 'gravitas-core' ) ) );
				foreach ( array( 'finder_eyebrow' => 'Eyebrow', 'finder_title' => 'Question', 'finder_placeholder' => 'Search placeholder', 'finder_button' => 'Button label' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' ) );
				}
				$this->end_controls_section();

				$this->start_controls_section( 'content_sections', array( 'label' => esc_html__( 'Services, trainers and journal', 'gravitas-core' ) ) );
				foreach ( array( 'services_eyebrow' => 'Services eyebrow', 'services_title' => 'Services title', 'services_link_text' => 'Services link', 'trainers_eyebrow' => 'Trainers eyebrow', 'trainers_title' => 'Trainers title', 'trainers_link_text' => 'Trainers link', 'testimonials_eyebrow' => 'Testimonials eyebrow', 'testimonials_title' => 'Testimonials title', 'journal_eyebrow' => 'Journal eyebrow', 'journal_title' => 'Journal title' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' ) );
				}
				foreach ( array( 'focus_count' => 'Top service cards', 'services_count' => 'Program cards', 'trainers_count' => 'Trainer cards', 'testimonials_count' => 'Testimonials', 'journal_count' => 'Journal posts' ) as $key => $label ) {
					$this->add_control( $key, array( 'label' => esc_html__( $label, 'gravitas-core' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : 3, 'min' => 1, 'max' => 24 ) );
				}
				$this->end_controls_section();
			}

			protected function render() {
				if ( function_exists( 'elevora_render_homepage' ) ) {
					elevora_render_homepage( $this->get_settings_for_display() );
				}
			}
		}
	}

	$manager->register( new Gravitas_Elementor_Directory_Widget() );
	$manager->register( new Gravitas_Elementor_Services_Widget() );
	$manager->register( new Gravitas_Elementor_Posts_Widget() );
	$manager->register( new Gravitas_Elementor_Finder_Widget() );
	$manager->register( new Gravitas_Elementor_Testimonials_Widget() );
	$manager->register( new Gravitas_Elementor_Homepage_Widget() );
}
