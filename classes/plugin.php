<?php
namespace BEA\VC_BD;
/**
 * The purpose of the plugin class is to have the methods for
 *  - activation actions
 *  - deactivation actions
 *  - uninstall actions
 *
 * Class Plugin
 * @package BEA\PB
 */
class Plugin {
	/**
	 * Use the trait
	 */
	use Singleton;

	public static function activate() {
		if ( ! class_exists( 'Vc_Manager' ) ) {
			deactivate_plugins( plugin_basename( BEA_VC_BD_DIR ) );
			wp_die( esc_html__( 'This plugin requires Visual Composer plugin !', 'bea-vc-bd' ) );
		}
	}
}
