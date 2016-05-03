<?php
namespace BEA\VC_BD\Admin;
use BEA\VC_BD\Singleton;
use BEA\VC_BD;

/**
 * Basic class for Admin
 *
 * Class Main
 * @package BEA\VC_BD\Admin
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_vc_templates' ), 20 );
	}

	public static function register_vc_templates() {
		if ( ! function_exists( 'vc_map' ) ) {
			return false;
		}

		// Get VC Templates
		$vc_templates = \BEA\VC_BD\Helpers::get_vc_templates();

		if ( empty( $vc_templates ) ) {
			return false;
		}

		foreach ( $vc_templates as $path => $template_name ) {
			$title = sanitize_title( $template_name );

			vc_map( array(
				'name'                    => $template_name,
				'base'                    => $title,
				'class'                   => '',
				'category'                => __( 'Demo Blocks', 'bea-vc-bd' ),
				'icon'                    => 'icon-wpb-' . $title,
				'custom_markup'           => '{{ title }}',
				'show_settings_on_create' => false,
			) );
		}
	}
}
