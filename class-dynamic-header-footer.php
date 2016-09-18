<?php

/**
 *
 */
class Dynamic_Header_Footer {

	private $supported_themes = array();

	private $templates;
	private $template_dir;
	private $template_file = array();

	function __construct() {

		$this->supported_themes = array(
			'bb-theme',
			'next',
			'generatepress'
		);

		if ( get_template() == 'genesis' ) {

			require DHF_DIR . 'themes/genesis/class-genesis-compat.php';
		} else {
			$status = $this->set_template_path();

			if ( $status == true ) {

				// Check where we want to force the page template
				$this->check_forced_template();
			}
		}

		// Scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	function enqueue_scripts() {
		wp_enqueue_style( 'dhf-style', DHF_URL . 'assets/css/style.css', array(), '1.0' );
	}

	function body_class( $classes ) {

		$header_id = Dynamic_Header_Footer::get_settings( 'dhf_header_id', '' );
		$footer_id = Dynamic_Header_Footer::get_settings( 'dhf_footer_id', '' );

		if ( $header_id !== '' ) {
			$classes[] = 'dhf-header';
		}

		if ( $footer_id !== '' ) {
			$classes[] = 'dhf-footer';
		}

		$classes[] = 'dhf-template-'	. get_template();
		$classes[] = 'dhf-stylesheet-'	. get_stylesheet();

		return $classes;
	}

	function check_forced_template() {
		add_filter( 'page_template', array( $this, 'force_page_template' ) );
		add_filter( 'single_template', array( $this, 'force_page_template' ) );
		add_filter( 'archive_template', array( $this, 'force_page_template' ) );
		add_filter( 'index_template', array( $this, 'force_page_template' ) );
		add_filter( '404_template', array( $this, 'force_page_template' ) );
		add_filter( 'author_template', array( $this, 'force_page_template' ) );
		add_filter( 'category_template', array( $this, 'force_page_template' ) );
		add_filter( 'tag_template', array( $this, 'force_page_template' ) );
		add_filter( 'taxonomy_template', array( $this, 'force_page_template' ) );
		add_filter( 'date_template', array( $this, 'force_page_template' ) );
		add_filter( 'home_template', array( $this, 'force_page_template' ) );
		add_filter( 'front_page_template', array( $this, 'force_page_template' ) );
		add_filter( 'paged_template', array( $this, 'force_page_template' ) );
		add_filter( 'search_template', array( $this, 'force_page_template' ) );
		add_filter( 'attachment_template', array( $this, 'force_page_template' ) );
	}

	function force_page_template( $page_template ) {

		$page_template = $this->template_file;

		return $page_template;
	}

	public function set_template_path() {

		$template = get_template();

		if ( in_array( $template, $this->supported_themes ) ) {

			$this->template_file = DHF_DIR . 'themes/' . $template . '/template-page-builder.php';
			$this->template_dir  = DHF_DIR . 'themes/' . $template . '';

			$this->templates = array(
				'template-page-builder.php' => 'Page Builder Template'
			);

			return true;
		} else {

			return false;
		}

	}

	public function get_header() {

		$header_id = Dynamic_Header_Footer::get_settings( 'dhf_header_id', '' );

		if ( $header_id !== '' ) {
			load_template( $this->template_dir . '/header.php' );
		} else {
			get_header();
		}
	}

	public function get_footer() {

		$footer_id = Dynamic_Header_Footer::get_settings( 'dhf_footer_id', '' );

		if ( $footer_id !== '' ) {
			load_template( $this->template_dir . '/footer.php' );
		} else {
			get_footer();
		}
	}

	public static function get_header_content() {

		$header_id = Dynamic_Header_Footer::get_settings( 'dhf_header_id', '' );
		echo do_shortcode( '[fl_builder_insert_layout id="' . $header_id . '"]' );
	}

	public static function get_footer_content() {

		$footer_id = Dynamic_Header_Footer::get_settings( 'dhf_footer_id', '' );
		echo "<div class='footer-width-fixer'>";
		echo do_shortcode( '[fl_builder_insert_layout id="' . $footer_id . '"]' );
		echo "</div>";
	}

	public static function get_settings( $setting = '', $default = '' ) {

		$options = get_option( 'dhf_settings' );

		if ( isset( $options[ $setting ] ) ) {
			return $options[ $setting ];
		}

		return $default;
	}

}