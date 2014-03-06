<?php
/**
 * Plugin Name: Legal Document Deliveries - Core
 * Plugin URI: https://github.com/michael-cannon/ldd
 * Description: LDD core
 * Version: 1.0.0
 * Author: Michael Cannon
 * Author URI: http://aihr.us/resume/
 * License: GPLv2 or later
 * Text Domain: ldd
 * Domain Path: /languages
 */


/**
 * Copyright 2014 Michael Cannon (email: mc@aihr.us)
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

if ( ! defined( 'LDD_AIHR_VERSION' ) )
	define( 'LDD_AIHR_VERSION', '1.0.3' );

if ( ! defined( 'LDD_BASE' ) )
	define( 'LDD_BASE', plugin_basename( __FILE__ ) );

if ( ! defined( 'LDD_DIR' ) )
	define( 'LDD_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'LDD_DIR_INC' ) )
	define( 'LDD_DIR_INC', LDD_DIR . 'includes/' );

if ( ! defined( 'LDD_DIR_LIB' ) )
	define( 'LDD_DIR_LIB', LDD_DIR_INC . 'libraries/' );

if ( ! defined( 'LDD_NAME' ) )
	define( 'LDD_NAME', 'Legal Document Deliveries - Core' );

if ( ! defined( 'LDD_REQ_BASE' ) )
	define( 'LDD_REQ_BASE', 'easy-digital-downloads/easy-digital-downloads.php' );

if ( ! defined( 'LDD_REQ_NAME' ) )
	define( 'LDD_REQ_NAME', 'Easy Digital Downloads' );

if ( ! defined( 'LDD_REQ_SLUG' ) )
	define( 'LDD_REQ_SLUG', 'easy-digital-downloads' );

if ( ! defined( 'LDD_REQ_VERSION' ) )
	define( 'LDD_REQ_VERSION', '1.9.4' );

if ( ! defined( 'LDD_VERSION' ) )
	define( 'LDD_VERSION', '1.0.0' );

require_once LDD_DIR_INC . 'requirements.php';

global $ldd_activated;

$ldd_activated = true;
if ( ! ldd_requirements_check() ) {
	$ldd_activated = false;

	return false;
}

require_once LDD_DIR_INC . 'class-ldd.php';


add_action( 'plugins_loaded', 'ldd_init', 99 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
if ( ! function_exists( 'ldd_init' ) ) {
	function ldd_init() {
		if ( ! is_admin() )
			return;

		if ( LDD::version_check() ) {
			global $LDD;
			if ( is_null( $LDD ) )
				$LDD = new LDD();

			global $LDD_Settings;
			if ( is_null( $LDD_Settings ) )
				$LDD_Settings = new LDD_Settings();
			
			do_action( 'ldd_init' );
		}
	}
}


register_activation_hook( __FILE__, array( 'LDD', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'LDD', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'LDD', 'uninstall' ) );


if ( ! function_exists( 'ldd_shortcode' ) ) {
	function ldd_shortcode( $atts ) {
		return LDD::ldd_shortcode( $atts );
	}
}

?>
