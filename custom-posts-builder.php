<?php
/*
 * Plugin Name: Custom Posts Builder
 * Description: Best custom posts manager.period.
 * Version: 1.0
 * Author: Prince Singh
 * Author URI: http://www.wpdevstudio.com
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Custom_Posts_Builder' ) ) :
	/*
	 * Main Custom_Posts_Builder Class
	 *
	 * @since 1.0
	 */
	final class Custom_Posts_Builder {
		
		/*
		 * @var Custom_Posts_Builder instance
		 * @since 1.0
		 */
		private static $instance;

		/*
		 * @var CPB_ADMIN_SETTINGS instance
		 * @since 1.0
		 */
		public $admin_instance;
	
		/*
		 * Main Custom_Posts_Builder Instance
		 *
		 * Insures that only one instance of Custom_Posts_Builder exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @static var array $instance
		 * @uses Custom_Posts_Builder::includes() Include the required files
		 * @return The one true Custom_Posts_Builder
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Custom_Posts_Builder ) ) {
				self::$instance = new Custom_Posts_Builder;
				self::$instance->hooks();
				self::$instance->setup_constants();
				self::$instance->includes();
			}
			return self::$instance;
		}
		
		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			add_action('init',array($this,'init') );
		}
		
	
		/*
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {		
	
			// Plugin version
			if ( ! defined( 'CPB_VERSION' ) ) {
				define( 'CPB_VERSION', '1.0' );
			}

			// Current Page
			if ( ! defined( 'CPB_PLUGIN_FILE' ) ) {
				define("CPB_PLUGIN_FILE", plugin_basename( __FILE__ ));
			}

			// Plugin text domain
			if ( ! defined( 'CPB_TEXT_DOMAIN' ) ) {
				define( 'CPB_TEXT_DOMAIN', 'custom-posts-builder' );
			}

			// Plugin slug
			if ( ! defined( 'CPB_SLUG' ) ) {
				define( 'CPB_SLUG', 'cpb' );
			}

			// Plugin prefix
			if ( ! defined( 'CPB_PREFIX' ) ) {
				define( 'CPB_PREFIX', 'cpb_' );
			}

			// Plugin url
			if ( ! defined( 'CPB_PLUGIN_URL' ) ) {
				define( 'CPB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			
			// Plugin Folder Path
			if ( ! defined( 'CPB_PLUGIN_PATH' ) ) {
				define( 'CPB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			}
			
		}

		/*
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			require(CPB_PLUGIN_PATH.'/common/install.php');
			require(CPB_PLUGIN_PATH.'/common/functions.php');
			require(CPB_PLUGIN_PATH.'/common/class-shortcode-parser.php');
			require(CPB_PLUGIN_PATH.'/common/class-cpb-public.php');

			if ( is_admin() ) {
				require(CPB_PLUGIN_PATH.'/admin/class-welcome-cpb.php');
				require(CPB_PLUGIN_PATH.'/admin/class-admin-settings.php');
				$this->admin_instance = CPB_ADMIN_SETTINGS::instance();

			} else {

			}
		}

		function init() {

		}

	}
endif; // End if class_exists check

/*
 * Get Custom_Posts_Builder Running
 *
 * @since 1.0
 * @return object The one true Custom_Posts_Builder Instance
 */
function CPB() {
	return Custom_Posts_Builder::instance();
}

CPB();
