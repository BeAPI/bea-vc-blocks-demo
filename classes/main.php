<?php
namespace BEA\VC_BD;

/**
 * The purpose of the main class is to init all the plugin base code like :
 *  - Taxonomies
 *  - Post types
 *  - Shortcodes
 *  - Posts to posts relations etc.
 *  - Loading the text domain
 *
 * Class Main
 * @package BEA\VC_BD
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		add_action( 'init', array( __CLASS__, 'init_translations' ) );

		// init the shortcode
		add_action( 'init', array( __CLASS__, 'init_shortcode' ) );
	}

	/**
	 * Load the plugin translation
	 */
	public static function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-vc-bd', false, BEA_VC_BD_DIR . 'languages' );
	}

	/**
	 * Init Shortcode and call anonymous function for include each template
	 */
	public static function init_shortcode() {
		// Get VC Templates
		$vc_templates = Helpers::get_vc_templates();

		if ( empty( $vc_templates ) ) {
			return false;
		}

		foreach ( $vc_templates as $path => $template_name ) {
			add_shortcode( sanitize_title( $template_name ), function() use ( $path ) {
				if ( ! is_file( $path ) ) {
					return false;
				}

				//include template and display it
				ob_start();
				include( $path );
				return ob_get_clean();
			} );
		}
	}
}
