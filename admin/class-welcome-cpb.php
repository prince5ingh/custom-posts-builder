<?php
/**
 * Welcome Page Class
 *
 * @package     CPB
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2016, Prince Singh
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CPB_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0
 */
class CPB_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'edit_published_posts';

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'cpb_welcome' ) );
	}

	/**
	 * Get pro item link
	 */
	public function get_pro_link() {
		$url = 'https://codecanyon.net/item/custom-posts-builder-pro/17966160?ref=wpdevstudio';
		return '<span class="cpb-pro-only"><a target="_blank" href="'.$url.'">'.__('Pro Only Feature',CPB_TEXT_DOMAIN).'</a></span>';
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menus() {

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with Custom Posts Builder', CPB_TEXT_DOMAIN  ),
			__( 'Getting started with Custom Posts Builder', CPB_TEXT_DOMAIN  ),
			$this->minimum_capability,
			'cpb-getting-started',
			array( $this, 'getting_started_screen' )
		);

	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'cpb-getting-started' );

		// Badge for welcome page
		$badge_url = CPB_PLUGIN_URL . 'assets/cpb.gif';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.cpb-badge {
			color: #666666;
		    font-size: 14px;
		    font-weight: bold;
		    margin: 145px -20px;
		    padding-top: 50px;
		    text-align: center;
		    vertical-align: sub;
		    width: 350px;
			background: url('<?php echo $badge_url; ?>') no-repeat scroll 0 0 / 333px auto;
		}

		.cpb-about-wrap .cpb-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.cpb-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'cpb-about';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'cpb-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cpb-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', CPB_TEXT_DOMAIN  ); ?>
			</a>
		</h2>
		<?php
	}


	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function getting_started_screen() {
		list( $display_version ) = explode( '-', CPB_VERSION );
		?>
		<div class="wrap about-wrap cpb-about-wrap">

			<h1><?php printf( __( 'Welcome to Custom Posts Builder %s', CPB_TEXT_DOMAIN  ), $display_version ); ?></h1>

			<div class="about-text">
				<?php printf( __( 'Featherlight fully ajaxified, feature rich & beautiful ui to manage your custom posts & taxonomies with ease.'), $display_version ); ?>
			</div>

			<div class="cpb-badge">
				<?php printf( __( 'Version %s', CPB_TEXT_DOMAIN  ), $display_version ); ?>
			</div>

			<?php $this->tabs(); ?>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Best custom posts manager. period.', CPB_TEXT_DOMAIN  );?></h2>

				<div class="featured-image">
					<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/custom-posts-builder-unlimited-posts.png'; ?>" class="cpb-welcome-featured-image"/>
				</div>
			</div>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Quick Start Guide', CPB_TEXT_DOMAIN  );?></h2>

				<h3 class="about-description" style="text-align: center;"><?php _e( 'follow the steps below and you will be up and running with custom posts builder in no time !', CPB_TEXT_DOMAIN  ); ?></h3>

				<div class="feature-section">
					<ul style="text-align: center;">
						<li><a href="#guide-create-cpt"><?php _e( 'Create & Manage Post Types', CPB_TEXT_DOMAIN  ); ?></a></li>
						<li><a href="#guide-create-tax"><?php _e( 'Create & Manage Taxonomies', CPB_TEXT_DOMAIN  ); ?></a></li>
						<li><a href="#guide-admin-cols"><?php _e( 'Admin Columns', CPB_TEXT_DOMAIN  ); ?></a></li>
						<li><a href="#guide-col-filters"><?php _e( 'Column Filters', CPB_TEXT_DOMAIN  ); ?></a></li>
						<li><a href="#guide-import-export"><?php _e( 'Import / Export', CPB_TEXT_DOMAIN  ); ?></a></li>
						<li><a href="#guide-support"><?php _e( 'Visit Support', CPB_TEXT_DOMAIN  ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2 id="guide-create-cpt"><?php _e( 'Create & Manage Custom Post Types', CPB_TEXT_DOMAIN  );?></h2>

				<div class="feature-section">

					<div class="featured-image">
						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/create-manage.jpg'; ?>" class="cpb-welcome-featured-image"/>
					</div>
				</div>
			</div>
			<div class="changelog headline-feature">

				<h2 id="guide-create-tax"><?php _e( 'Create & Manage Taxonomies', CPB_TEXT_DOMAIN  );?></h2>

				<div class="feature-section">

					<div class="featured-image">
						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/manage-tax.jpg'; ?>" class="cpb-welcome-featured-image"/>
					</div>
				</div>
			</div>

			<div class="changelog headline-feature admin-cols">

				<h2 id="guide-admin-cols"><?php _e( 'Create unlimited admin columns & show the details you need', CPB_TEXT_DOMAIN  );?><?php echo $this->get_pro_link(); ?></h2>

				<div class="feature-section">

						<p><?php _e( 'Disable / enable the default admin columns for your custom post type, only show details you need', CPB_TEXT_DOMAIN  );?></p>
						
						<p><?php _e( 'Add unlimited admin columns, show more information at glance !', CPB_TEXT_DOMAIN  );?></p>

						<p><?php _e( 'Pre built most desired columns like featured image ', CPB_TEXT_DOMAIN  );?></p>

						<p><?php _e( 'Drag & drop reorder columns based on the priority', CPB_TEXT_DOMAIN  );?></p>

						<p><?php _e( 'Temporary disable / delete the custom columns with a click', CPB_TEXT_DOMAIN  );?></p>

						<p><?php _e( 'Total command on what you need to show in custom fields with powerful built-in shortcodes', CPB_TEXT_DOMAIN  );?></p>
						<p><?php _e( 'Meta data, taxonomy data or even a function result, everthing can be shown just using shortcode. no need to touch code !', CPB_TEXT_DOMAIN  );?></p>


						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/admin-cols.jpg'; ?>" class="cpb-welcome-featured-image"/>
				</div>

			</div>

			<div class="changelog headline-feature cpb-filters">

				<h2 id="guide-col-filters"><?php _e( 'Create custom filters for admin columns', CPB_TEXT_DOMAIN  );?><span class="cpb-pro-only"><?php echo $this->get_pro_link(); ?></span></h2>

				<div class="feature-section">

						<p><?php _e( 'Add filters using ajax enabled autocomplete field', CPB_TEXT_DOMAIN  );?></p>
						

						<p><?php _e( 'Auto filters for every taxonomy created', CPB_TEXT_DOMAIN  );?></p>

						<p><?php _e( 'Search using filters to refine your view', CPB_TEXT_DOMAIN  );?></p>


						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/filter-ui.jpg'; ?>" class="cpb-welcome-featured-image"/>
						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/filter-admin.jpg'; ?>" class="cpb-welcome-featured-image"/>
				</div>

			</div>

			<div class="changelog headline-feature cpb-filters">

				<h2 id="guide-import-export"><?php _e( 'Take custom posts wherever you go', CPB_TEXT_DOMAIN  );?></h2>

				<div class="feature-section">

						<p><?php _e( 'Option to import & export custom posts & taxonomies', CPB_TEXT_DOMAIN  );?></p>
						
						<img src="<?php echo CPB_PLUGIN_URL . 'assets/ss/export.jpg'; ?>" class="cpb-welcome-featured-image"/>
				</div>

			</div>

			<div class="changelog headline-feature cpb-filters">

				<h2 id="guide-import-export"><?php _e( 'Need Help ?', CPB_TEXT_DOMAIN  );?></h2>

				<div class="feature-section">

						<p><?php echo $link = sprintf( __( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="%s">support forums</a>.', CPB_TEXT_DOMAIN  ), esc_url( 'http://support.wpdevstudio.com/s2-custom-posts-builder' ) );?></p>
				</div>

			</div>

			
		</div> <?php
	}


	/**
	 * Sends user to the Welcome page on first activation of CPB as well as each
	 * time CPB is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function cpb_welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_cpb_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_cpb_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		wp_safe_redirect( admin_url( 'index.php?page=cpb-getting-started' ) ); exit;
	}
}
new CPB_Welcome();
