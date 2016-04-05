<?php
/*
 Plugin Name: BEA VC Blocks Demonstration
 Version: 1.0.0
 Version Boilerplate: 2.1.2
 Plugin URI: http://www.beapi.fr
 Description: Your plugin description
 Author: BE API Technical team
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Text Domain: bea-vc-bd

 ----

 Copyright 2015 BE API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_VC_BD_VERSION', '1.0.0' );
define( 'BEA_VC_BD_MIN_PHP_VERSION', '5.4' );
define( 'BEA_VC_BD_VIEWS_FOLDER_NAME', 'bea-vc-bd' );

// Plugin URL and PATH
define( 'BEA_VC_BD_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_VC_BD_DIR', plugin_dir_path( __FILE__ ) );

// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_VC_BD_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_VC_BD_DIR . 'compat.php' );

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\VC_BD\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

/**
 * Autoload all the things \o/
 */
require_once BEA_VC_BD_DIR . 'autoload.php';

// Plugin activate/deactive hooks
register_activation_hook( __FILE__, array( '\BEA\VC_BD\Plugin', 'activate' ) );

add_action( 'plugins_loaded', 'init_bea_vc_bd_plugin' );
/**
 * Init the plugin
 */
function init_bea_vc_bd_plugin() {

	// Client
	\BEA\VC_BD\Main::get_instance();

	// Admin
	if ( is_admin() ) {
		\BEA\VC_BD\Admin\Main::get_instance();
	}
}
