<?php
/**
 * Plugin Name: Legal Document Deliveries - Core by Aihrus
 * Plugin URI: http://aihr.us
 * Description: LDD Deliveries post type core
 * Version: 1.0.0
 * Author: Michael Cannon
 * Author URI: http://aihr.us/resume/
 * License: GPLv2 or later
 * Text Domain: ldd-deliveries
 * Domain Path: /languages
 */


/**
 * Copyright 2013 Michael Cannon (email: mc@aihr.us)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

if ( ! defined( 'LDD_DELIVERIES_AIHR_VERSION' ) )
	define( 'LDD_DELIVERIES_AIHR_VERSION', '1.0.3' );

if ( ! defined( 'LDD_DELIVERIES_BASE' ) )
	define( 'LDD_DELIVERIES_BASE', plugin_basename( __FILE__ ) );

if ( ! defined( 'LDD_DELIVERIES_DIR' ) )
	define( 'LDD_DELIVERIES_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'LDD_DELIVERIES_DIR_INC' ) )
	define( 'LDD_DELIVERIES_DIR_INC', LDD_DELIVERIES_DIR . 'includes/' );

if ( ! defined( 'LDD_DELIVERIES_DIR_LIB' ) )
	define( 'LDD_DELIVERIES_DIR_LIB', LDD_DELIVERIES_DIR_INC . 'libraries/' );

if ( ! defined( 'LDD_DELIVERIES_NAME' ) )
	define( 'LDD_DELIVERIES_NAME', 'Legal Document Deliveries - Core by Aihrus' );

if ( ! defined( 'LDD_DELIVERIES_REQ_BASE' ) )
	define( 'LDD_DELIVERIES_REQ_BASE', 'easy-digital-downloads/easy-digital-downloads.php' );

if ( ! defined( 'LDD_DELIVERIES_REQ_NAME' ) )
	define( 'LDD_DELIVERIES_REQ_NAME', 'Easy Digital Downloads' );

if ( ! defined( 'LDD_DELIVERIES_REQ_SLUG' ) )
	define( 'LDD_DELIVERIES_REQ_SLUG', 'easy-digital-downloads' );

if ( ! defined( 'LDD_DELIVERIES_REQ_VERSION' ) )
	define( 'LDD_DELIVERIES_REQ_VERSION', '1.9.4' );

if ( ! defined( 'LDD_DELIVERIES_VERSION' ) )
	define( 'LDD_DELIVERIES_VERSION', '1.0.0' );

require_once LDD_DELIVERIES_DIR_INC . 'requirements.php';

global $ldd_deliveries_activated;

$ldd_deliveries_activated = true;
if ( ! ldd_deliveries_requirements_check() ) {
	$ldd_deliveries_activated = false;

	return false;
}

require_once LDD_DELIVERIES_DIR_INC . 'class-ldd-deliveries.php';


add_action( 'plugins_loaded', 'ldd_deliveries_init', 99 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
if ( ! function_exists( 'ldd_deliveries_init' ) ) {
	function ldd_deliveries_init() {
		if ( ! is_admin() )
			return;

		if ( LDD_Deliveries::version_check() ) {
			global $LDD_Deliveries;
			if ( is_null( $LDD_Deliveries ) )
				$LDD_Deliveries = new LDD_Deliveries();

			global $LDD_Deliveries_Settings;
			if ( is_null( $LDD_Deliveries_Settings ) )
				$LDD_Deliveries_Settings = new LDD_Deliveries_Settings();
			
			do_action( 'ldd_deliveries_init' );
		}
	}
}


register_activation_hook( __FILE__, array( 'LDD_Deliveries', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'LDD_Deliveries', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'LDD_Deliveries', 'uninstall' ) );


if ( ! function_exists( 'ldd_deliveries_shortcode' ) ) {
	function ldd_deliveries_shortcode( $atts ) {
		return LDD_Deliveries::ldd_deliveries_shortcode( $atts );
	}
}

?>
