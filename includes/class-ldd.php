<?php
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

require_once AIHR_DIR_INC . 'class-aihrus-common.php';
require_once LDD_DIR_INC . 'class-ldd-settings.php';
require_once LDD_DIR_INC . 'class-ldd-widget.php';
require_once LDD_DIR_INC . 'class-post-status-assigned.php';
require_once LDD_DIR_INC . 'class-post-status-prepare.php';
require_once LDD_DIR_INC . 'class-post-status-enroute.php';

if ( class_exists( 'LDD' ) )
	return;


class LDD extends Aihrus_Common {
	const BASE    = LDD_BASE;
	const ID      = 'ldd';
	const SLUG    = 'ldd_';
	const VERSION = LDD_VERSION;

	const PT = 'ldd';

	public static $class        = __CLASS__;
	public static $cpt_category = '';
	public static $cpt_tags     = '';
	public static $menu_id;
	public static $notice_key;
	public static $plugin_assets;
	public static $scripts = array();
	public static $settings_link;
	public static $styles        = array();
	public static $styles_called = false;


	public function __construct() {
		parent::__construct();

		self::$plugin_assets = plugins_url( '/assets/', dirname( __FILE__ ) );
		self::$plugin_assets = self::strip_protocol( self::$plugin_assets );

		self::actions();
		self::filters();

		add_shortcode( 'ldd_shortcode', array( __CLASS__, 'ldd_shortcode' ) );
	}


	public static function admin_init() {
		// fixme self::support_thumbnails();
		// fixme self::update();

		self::$settings_link = '<a href="' . get_admin_url() . 'edit.php?post_type=' . self::PT . '&page=' . LDD_Settings::ID . '">' . __( 'Settings', 'ldd' ) . '</a>';
	}


	public static function admin_menu() {
		self::remove_meta_box();

		// fixme self::$menu_id = add_management_page( esc_html__( 'Legal Document Deliveries - Core Processor', 'ldd' ), esc_html__( 'Legal Document Deliveries - Core Processor', 'ldd' ), 'manage_options', self::ID, array( __CLASS__, 'user_interface' ) );

		// fixme add_action( 'admin_print_scripts-' . self::$menu_id, array( __CLASS__, 'scripts' ) );
		// fixme add_action( 'admin_print_styles-' . self::$menu_id, array( __CLASS__, 'styles' ) );
	}


	public static function init() {
		load_plugin_textdomain( self::ID, false, 'ldd/languages' );

		self::$cpt_category = self::PT . '-category';
		self::$cpt_tags     = self::PT . '-post_tag';

		self::init_post_type();
	}


	public static function plugin_action_links( $links, $file ) {
		if ( self::BASE == $file ) {
			array_unshift( $links, self::$settings_link );

			// fixme $link = '<a href="' . get_admin_url() . 'tools.php?page=' . self::ID . '">' . esc_html__( 'Process', 'ldd' ) . '</a>';
			// fixme array_unshift( $links, $link );
		}

		return $links;
	}


	public static function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public static function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		global $wpdb;
		
		require_once LDD_DIR_INC . 'class-ldd-settings.php';

		$delete_data = ldd_get_option( 'delete_data', false );
		if ( $delete_data ) {
			delete_option( LDD_Settings::ID );
			$wpdb->query( 'OPTIMIZE TABLE `' . $wpdb->options . '`' );
		}
	}


	public static function plugin_row_meta( $input, $file ) {
		if ( self::BASE != $file )
			return $input;

		$disable_donate = ldd_get_option( 'disable_donate', true );
		if ( $disable_donate )
			return $input;

		$links = array(
			self::$donate_link,
		);

		$input = array_merge( $input, $links );

		return $input;
	}


	public static function notice_0_0_1() {
		$text = sprintf( __( 'If your Legal Document Deliveries - Core display has gone to funky town, please <a href="%s">read the FAQ</a> about possible CSS fixes.', 'ldd' ), 'https://aihrus.zendesk.com/entries/23722573-Major-Changes-Since-2-10-0' );

		aihr_notice_updated( $text );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function notice_donate( $disable_donate = null, $item_name = null ) {
		$disable_donate = ldd_get_option( 'disable_donate', true );

		parent::notice_donate( $disable_donate, LDD_NAME );
	}


	public static function update() {
		$prior_version = ldd_get_option( 'admin_notices' );
		if ( $prior_version ) {
			if ( $prior_version < '0.0.1' )
				add_action( 'admin_notices', array( __CLASS__, 'notice_0_0_1' ) );

			if ( $prior_version < self::VERSION )
				do_action( 'ldd_update' );

			ldd_set_option( 'admin_notices' );
		}

		// display donate on major/minor version release
		$donate_version = ldd_get_option( 'donate_version' );
		if ( ! $donate_version || ( $donate_version != self::VERSION && preg_match( '#\.0$#', self::VERSION ) ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_donate' ) );
			ldd_set_option( 'donate_version', self::VERSION );
		}
	}


	public static function scripts( $atts = array() ) {
		if ( is_admin() ) {
			wp_enqueue_script( 'jquery' );

			// fixme wp_register_script( 'jquery-ui-progressbar', self::$plugin_assets . 'js/jquery.ui.progressbar.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ), '1.10.3', true );
			// fixme wp_enqueue_script( 'jquery-ui-progressbar' );

			add_action( 'admin_footer', array( 'LDD', 'get_scripts' ) );
		} else {
			add_action( 'wp_footer', array( 'LDD', 'get_scripts' ) );
		}

		do_action( 'ldd_scripts', $atts );
	}


	public static function styles( $hook = false ) {
		if ( is_admin() ) {
			if ( ! empty( $hook ) && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
				wp_register_style( __CLASS__, self::$plugin_assets . 'css/ldd.css' );
				wp_enqueue_style( __CLASS__ );
			}
			// fixme wp_register_style( 'jquery-ui-progressbar', self::$plugin_assets . 'css/redmond/jquery-ui-1.10.3.custom.min.css', false, '1.10.3', true );
			// fixme wp_enqueue_style( 'jquery-ui-progressbar' );

			add_action( 'admin_footer', array( 'LDD', 'get_styles' ) );
		} else {
			wp_register_style( __CLASS__, self::$plugin_assets . 'css/ldd.css' );
			wp_enqueue_style( __CLASS__ );

			add_action( 'wp_footer', array( 'LDD', 'get_styles' ) );
		}

		do_action( 'ldd_styles' );
	}


	public static function ldd_shortcode( $atts ) {
		self::call_scripts_styles( $atts );

		return __CLASS__ . ' shortcode';
	}


	public static function version_check() {
		$good_version = true;

		return $good_version;
	}


	public static function call_scripts_styles( $atts ) {
		self::scripts( $atts );
	}


	public static function get_scripts() {
		if ( empty( self::$scripts ) )
			return;

		foreach ( self::$scripts as $script )
			echo $script;
	}


	public static function get_styles() {
		if ( empty( self::$styles ) )
			return;

		if ( empty( self::$styles_called ) ) {
			echo '<style>';

			foreach ( self::$styles as $style )
				echo $style;

			echo '</style>';

			self::$styles_called = true;
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function do_load() {
		$do_load = false;
		if ( ! empty( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'edit.php', 'options.php', 'plugins.php' ) ) ) {
			$do_load = true;
		} elseif ( ! empty( $_REQUEST['page'] ) && LDD_Settings::ID == $_REQUEST['page'] ) {
			$do_load = true;
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$do_load = true;
		}

		return $do_load;
	}


	public static function widgets_init() {
		register_widget( 'LDD_Widget' );
	}


	public static function get_defaults( $single_view = false ) {
		if ( empty( $single_view ) )
			return apply_filters( 'ldd_defaults', ldd_get_options() );
		else
			return apply_filters( 'ldd_defaults_single', ldd_get_options() );
	}


	public static function init_post_type() {
		$labels = array(
			'add_new' => esc_html__( 'Add New' ),
			'add_new_item' => esc_html__( 'Add New Delivery' ),
			'edit_item' => esc_html__( 'Edit Delivery' ),
			'name' => esc_html__( 'Deliveries' ),
			'new_item' => esc_html__( 'Add New Delivery' ),
			'not_found' => esc_html__( 'No deliveries found' ),
			'not_found_in_trash' => esc_html__( 'No deliveries found in Trash' ),
			'parent_item_colon' => null,
			'search_items' => esc_html__( 'Search Deliveries' ),
			'singular_name' => esc_html__( 'Delivery' ),
			'view_item' => esc_html__( 'View Delivery' ),
		);

		$supports = array(
			'title',
			'comments',
		);

		// editor's and up
		if ( current_user_can( 'edit_others_posts' ) )
			$supports[] = 'author';

		$taxonomies = array(
			self::$cpt_category,
			self::$cpt_tags,
		);

		// fixme self::register_taxonomies();

		$args = array(
			'label' => esc_html__( 'LDD Deliveries' ),
			'capability_type' => 'post',
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'supports' => $supports,
			// fixme 'taxonomies' => $taxonomies,
		);

		register_post_type( self::PT, $args );

		// fixme register_taxonomy_for_object_type( self::$cpt_category, self::PT );
		// fixme register_taxonomy_for_object_type( self::$cpt_tags, self::PT );
	}


	public static function register_taxonomies() {
		$args = array(
			'hierarchical' => true,
			'show_admin_column' => true,
		);
		register_taxonomy( self::$cpt_category, self::PT, $args );

		$args = array(
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
		);
		register_taxonomy( self::$cpt_tags, self::PT, $args );
	}


	public static function support_thumbnails() {
		$feature       = 'post-thumbnails';
		$feature_level = get_theme_support( $feature );

		if ( true === $feature_level ) {
			// already enabled for all post types
			return;
		} elseif ( false === $feature_level ) {
			// none allowed, only enable for our own
			add_theme_support( $feature, array( self::PT ) );
		} else {
			// add our own to list of supported
			$feature_level[0][] = self::PT;
			add_theme_support( $feature, $feature_level[0] );
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function pre_update_option_active_plugins( $plugins, $old_value ) {
		if ( ! is_array( $plugins ) )
			return $plugins;

		if ( defined( 'LDDP_BASE' ) )
			return $plugins;

		$index = array_search( self::BASE, $plugins );
		if ( false !== $index && ! empty( $index ) ) {
			unset( $plugins[ $index ] );
			array_unshift( $plugins, self::BASE );
		}

		return $plugins;
	}


	public static function actions() {
		// fixme add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'styles' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'after_setup_theme', array( 'post_status_assigned', 'init' ) );
		add_action( 'after_setup_theme', array( 'post_status_enroute', 'init' ) );
		add_action( 'after_setup_theme', array( 'post_status_prepare', 'init' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'styles' ) );
        add_action( 'admin_head-post-new.php', array ( __CLASS__, 'hide_publish_button' ) );
        add_action( 'admin_head-post-new.php', array ( __CLASS__, 'load_gettext_filters' ) );
        add_action( 'admin_head-post.php', array ( __CLASS__, 'hide_publish_button' ) );
        add_action( 'admin_head-post.php', array ( __CLASS__, 'load_gettext_filters' ) );
	}


	public static function filters() {
		add_filter( 'gettext', array( __CLASS__, 'gettext_global' ) );
		add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'pre_update_option_active_plugins', array( __CLASS__, 'pre_update_option_active_plugins' ), 10, 2 );
	}


	public static function remove_meta_box() {
		remove_meta_box( 'commentstatusdiv', LDD::PT, 'normal' );
		remove_meta_box( 'slugdiv', LDD::PT, 'normal' );
	}


	public static function load_gettext_filters() {
		global $post_type, $wp_scripts;

		if ( ! in_array( $post_type, array( LDD::PT ) ) )
			return;

		$extra = $wp_scripts->registered['post']->extra['data'];
		$extra = self::gettext_status( $extra );

		$wp_scripts->registered['post']->extra['data'] = $extra;
		
		add_filter( 'gettext', array( __CLASS__, 'gettext_status' ) );
	}


	public static function gettext_status( $translated ) {
		$translated = preg_replace( '#\bSave as Pending\b#', 'Save Update', $translated );
		$translated = preg_replace( '#\bPending Review\b#', 'Awaiting Assignment', $translated );
		$translated = preg_replace( '#\bSave Draft\b#', 'Save Update', $translated );
		$translated = preg_replace( '#\bPublish\b#', 'Delivered', $translated );
		$translated = preg_replace( '#\bPublished\b#', 'Delivered', $translated );

		return $translated;
	}


	public static function gettext_global( $translated ) {
		static $do_it;

		if ( is_null( $do_it ) ) {
			// why is this so dificult to figure what post_type we're currently on?
			$post_type = isset( $_GET['post_type'] ) ? esc_attr( $_GET['post_type'] ) : false;
			if ( ! $post_type ) {
				global $post;
				if ( ! is_null( $post ) )
					$post_type = get_the_ID() ? get_post_type( get_the_ID() ) : false;
			}

			if ( $post_type )
				$do_it = in_array( $post_type, array( LDD::PT ) );
		}

		if ( ! $do_it )
			return $translated;

		$translated = preg_replace( '#\bAuthor\b#', 'Client', $translated );

		return $translated;
	}


	public static function hide_publish_button() {
		global $post_type;

		if ( ! in_array( $post_type, array( LDD::PT ) ) )
			return;

		self::$scripts[] = "
			<script type='text/javascript'>
				jQuery(document).ready( function($){
					$('#publishing-action #publish').hide();
					$('#post_status').change( function(){
						post_status = jQuery('#post_status').val();
						if ( 'publish' == post_status ) {
							jQuery('#publishing-action #publish').show();
						} else {
							jQuery('#publishing-action #publish').hide();
						}
					});
				});
			</script>
		";
	}


	/**
	 * Add a note to a delivery
	 *
	 * @see edd_insert_payment_note
	 *
	 * @param int $delivery_id The delivery ID to store a note for
	 * @param string $note The note to store
	 * @return int The new note ID
	 */
	public static function insert_delivery_note( $delivery_id = 0, $note = '' ) {
		if ( empty( $delivery_id ) )
			return false;

		do_action( 'ldd_pre_insert_delivery_note', $delivery_id, $note );

		$note_id = wp_insert_comment(
			wp_filter_comment(
				array(
					'comment_post_ID' => $delivery_id,
					'comment_content' => $note,
					'user_id' => is_admin() ? get_current_user_id() : 0,
					'comment_date' => current_time( 'mysql' ),
					'comment_date_gmt' => current_time( 'mysql', 1 ),
					'comment_approved' => 1,
					'comment_parent' => 0,
					'comment_author' => '',
					'comment_author_IP' => '',
					'comment_author_url' => '',
					'comment_author_email' => '',
					'comment_type' => 'ldd_delivery_note',
				)
			)
		);

		do_action( 'ldd_insert_delivery_note', $note_id, $delivery_id, $note );

		return $note_id;
	}

}

?>
