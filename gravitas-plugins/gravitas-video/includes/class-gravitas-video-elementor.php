<?php
/**
 * Elementor video widgets.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Elementor {
	public function __construct() {
		add_action( 'elementor/widgets/register', 'gravitas_video_register_elementor_widgets' );
	}
}

function gravitas_video_register_elementor_widgets( $manager ) {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return;
	}
	if ( ! class_exists( 'Gravitas_Elementor_Video_Grid' ) ) {
		class Gravitas_Elementor_Video_Grid extends \Elementor\Widget_Base {
			public function get_name() { return 'gravitas-video-grid'; }
			public function get_title() { return esc_html__( 'Gravitas Video Grid', 'gravitas-video' ); }
			public function get_icon() { return 'eicon-video-playlist'; }
			public function get_categories() { return array( 'general' ); }
			protected function register_controls() { $this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-video' ) ) ); $this->add_control( 'count', array( 'label' => esc_html__( 'Videos', 'gravitas-video' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 6, 'min' => 1, 'max' => 48 ) ); $this->add_control( 'topic', array( 'label' => esc_html__( 'Topic slug', 'gravitas-video' ), 'type' => \Elementor\Controls_Manager::TEXT ) ); $this->add_control( 'featured', array( 'label' => esc_html__( 'Featured only', 'gravitas-video' ), 'type' => \Elementor\Controls_Manager::SWITCHER ) ); $this->end_controls_section(); }
			protected function render() { $settings = $this->get_settings_for_display(); echo do_shortcode( '[gravitas_video_grid count="' . absint( $settings['count'] ) . '" topic="' . esc_attr( $settings['topic'] ) . '" featured="' . ( 'yes' === $settings['featured'] ? '1' : '' ) . '"]' ); }
		}
	}
	if ( ! class_exists( 'Gravitas_Elementor_Video_Player' ) ) {
		class Gravitas_Elementor_Video_Player extends \Elementor\Widget_Base {
			public function get_name() { return 'gravitas-video-player'; }
			public function get_title() { return esc_html__( 'Gravitas Video Player', 'gravitas-video' ); }
			public function get_icon() { return 'eicon-play'; }
			public function get_categories() { return array( 'general' ); }
			protected function register_controls() { $this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'gravitas-video' ) ) ); $this->add_control( 'video_id', array( 'label' => esc_html__( 'Video post ID', 'gravitas-video' ), 'type' => \Elementor\Controls_Manager::NUMBER ) ); $this->end_controls_section(); }
			protected function render() { $settings = $this->get_settings_for_display(); echo gravitas_video_render_player( absint( $settings['video_id'] ) ); }
		}
	}
	$manager->register( new Gravitas_Elementor_Video_Grid() );
	$manager->register( new Gravitas_Elementor_Video_Player() );
}
