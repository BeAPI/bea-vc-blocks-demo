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
		add_action( 'init', array( $this, 'init_translations' ) );
	}

	/**
	 * Load the plugin translation
	 */
	public static function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-vc-bd', false, BEA_VC_BD_DIR . 'languages' );
	}

}