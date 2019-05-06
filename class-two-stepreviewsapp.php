<?php
/**
 * exit if file is accessed directly
 */
defined('ABSPATH') || exit;

require_once 'vendor/autoload.php';
require_once 'reviews-shortcode.php';

/**
 * Class Two_Step_Reviews_App
 */
class Two_Step_Reviews_App {

	/**
	 * is plugin initiated
	 * @var bool default to false
	 */
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}

        static ::register_page('connect.php');
        static ::register_page('show-reviews.php');

	}

	/**
	 * initialize plugin hooks
	 */
	private static function init_hooks() {
		// set initiated to true
		self::$initiated = true;

		// plugin hooks for scripts and block cateory
		add_action( 'enqueue_block_editor_assets', array( 'Two_Step_Reviews_App', 'eb_editor_scripts' ) );
		add_action( 'enqueue_block_assets', array( 'Two_Step_Reviews_App', 'eb_block_scripts' ) );
		add_filter( 'block_categories', array( 'Two_Step_Reviews_App', 'eb_block_categories' ), 10, 2 );

        // Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
        add_action( 'admin_menu', array( 'Two_Step_Reviews_App', 'add_menu_admin' ) );

        add_action( 'wp_footer', 'showFooter' );

	}

	/**
	 * plugin activation hook
	 */
	public static function eb_plugin_activation() {}

	/**
	 * plugin deactivation hook
	 */
	public static function eb_plugin_deactivation() {}

	/**
	 * enqueue editor scripts
	 */
	public static function eb_editor_scripts() {
		$editor_block_js_file = 'assets/js/editor.blocks.js';

		// Enqueue the bundled block JS file
		wp_enqueue_script(
			'eb-blocks-js',
			TSRAPP_DIR_URI . $editor_block_js_file,
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api', 'wp-editor' ],
			filemtime( TSRAPP_DIR_PATH . $editor_block_js_file )
		);
	}

	/**
	 * enqueue blocks scripts
	 */
	public static function eb_block_scripts() {
		$block_css_file = 'assets/css/styles.blocks.css';

		// Enqueue frontend and editor block styles
		wp_enqueue_style(
			'eb-blocks-css',
			TSRAPP_DIR_URI . $block_css_file,
			null,
			filemtime( TSRAPP_DIR_PATH . $block_css_file )
		);
	}

	/**
	 * Add new block category for 'Easy Blocks'
	 *
	 * @param $categories array list of available categories
	 *
	 * @return array filtered categories
	 */
	public static function eb_block_categories( $categories ) {
		/**
		 * add new block category
		 * in the beginning of array
		 */
		return array_merge(
			array(
				array(
					'slug' => TSRAPP_SLUG,
					'title' => TSRAPP_NAME,
					'icon'  => 'wordpress', // replace with logo in js
				),
			),
			$categories
		);
	}

    public static function get_page_url( $page = 'connect.php' ) {
        return  admin_url( 'admin.php' ) . '?page=TwoStepReviewsApp/'. $page;
    }

    // Add a new top level menu link to the ACP
    function add_menu_admin()
    {
        $page_title = '2step Configuration';
        $menu_title = '2Step';
        $capability = 'manage_options';
        $menu_slug = TSRAPP_DIR_PATH . '/config-page.php';
        $function = '';
        $icon_url = TSRAPP_DIR_URI . '/media/icon.png';
        $position = null;

        add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            $icon_url,
            $position
        );

        add_submenu_page($menu_slug, 'Reviews', 'Reviews', 'manage_options', TSRAPP_DIR_PATH . '/show-reviews.php');

    }


    static function register_page($page){

        global $admin_page_hooks, $_registered_pages;
        $menu_slug = TSRAPP_DIR_PATH . '/' . $page;
        $admin_page_hooks[ plugin_basename( $menu_slug ) ] = sanitize_title( $page );

        $page = preg_replace('/\.php/', '', $page);
        $hookname = 'toplevel_page_TwoStepReviewsApp/'. $page;
        $_registered_pages[ $hookname ] = true;
    }

}