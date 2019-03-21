<?php

namespace TvpTrelloDashboard\Plugin;

class Plugin {
	// instance
	private static $instance;

	// plugin variables
	public $name        = '';
	public $version     = '';
	public $prefix      = '';
	public $text_domain = '';

	// plugin page variables
	private $plugin;
	public $plugin_page;
	public $plugin_page_tabs;
	public $active_tab = false;

	// plugin databases
	public $database_version_option    = '';
	public $database_version           = '0.0';
	public $database_installed_version = '';
	public $database_name              = '';

	public function __construct() {
		$this->plugin = get_file_data(
			plugin_dir_path( __DIR__ ) . 'tvp-trello-dashboard.php',
			[
				'name'        => 'Name',
				'version'     => 'Version',
				'prefix'      => 'Text Domain',
				'text_domain' => 'Text Domain',
			],
			'plugin'
		);
		$this->plugin = (object) $this->plugin;

		$this->plugin_page      = $this->plugin->prefix;
		$this->plugin_page_tabs = [
			(object) [
				'title' => __( 'Trello API', $this->plugin->text_domain ),
				'slug'  => 'trello_api',
			],
			(object) [
				'title' => __( 'Pages', $this->plugin->text_domain ),
				'slug'  => 'pages',
			],
			(object) [
				'title' => __( 'Volunteers', $this->plugin->text_domain ),
				'slug'  => 'volunteer_database',
			],
			(object) [
				'title' => __( 'Divisions', $this->plugin->text_domain ),
				'slug'  => 'divisions_database',
			],
			(object) [
				'title' => __( 'Skills', $this->plugin->text_domain ),
				'slug'  => 'skills_database',
			],
		];

		$this->database_name              = $this->plugin->prefix;
		$this->database_version_option    = $this->plugin->prefix . '_db_version';
		$this->database_installed_version = get_option( $this->database_version_option, '0.0' );
	}


	/* ------------------------------------------------------------------------ *
	 * initialisation
	 * ------------------------------------------------------------------------ */

	public function run() {
		// load plugin classes
		$this->tvp_tdash_load_classes();

		// create database table on plugin activation
		// register_activation_hook( plugin_dir_path( __DIR__ ) . 'tvp-trello-dashboard.php', [ $this, $this->prefix . '_create_database' ] );

		// load translations
		add_action( 'after_setup_theme', [ $this, $this->prefix . '_load_translations' ] );

		// plugin page registration
		add_action( 'admin_menu', [ $this, $this->prefix . '_create_plugin_page' ] );
	}

	/**
	 * creates an instance if one isn't already available,
	 * then return the current instance.
	 * @return object The class instance.
	 */
	public static function tvp_tdash_get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Plugin ) ) {

			self::$instance = new Plugin;

			self::$instance->name        = self::$instance->plugin->name;
			self::$instance->version     = self::$instance->plugin->version;
			self::$instance->prefix      = self::$instance->plugin->prefix;
			self::$instance->text_domain = self::$instance->plugin->text_domain;

		}

		return self::$instance;
	}

	/**
	 * loads and initializes the provided classes.
	 *
	 * @param $classes
	 */
	private function tvp_tdash_load_classes() {

		// class to provide data for the trello api
		include_once( plugin_dir_path( __FILE__ ) . 'Package/class-trelloapi.php' );
		tvp_tdash_plugin()->TrelloAPI = new \TvpTrelloDashboard\Plugin\Package\TrelloAPI();
		if ( method_exists( tvp_tdash_plugin()->TrelloAPI, 'run' ) ) {
			tvp_tdash_plugin()->TrelloAPI->run();
		}

		// class to define pages like the dashboard itself, submission form etc.
		include_once( plugin_dir_path( __FILE__ ) . 'Package/class-pages.php' );
		tvp_tdash_plugin()->Pages = new \TvpTrelloDashboard\Plugin\Package\Pages();
		if ( method_exists( tvp_tdash_plugin()->Pages, 'run' ) ) {
			tvp_tdash_plugin()->Pages->run();
		}
	}


	/* ------------------------------------------------------------------------ *
	 * create database table
	 * ------------------------------------------------------------------------ */

	public function tvp_tdash_create_database() {
		global $wpdb;
		$installed_version = $this->database_installed_version;
		$version           = $this->database_version;
		$charset_collate   = $wpdb->get_charset_collate();
		$table_name        = $wpdb->prefix . $this->database_name;

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			type varchar(20) NOT NULL,
			value LONGTEXT NOT NULL,
			created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// update the option which holds the installed db version
		// we will use this option, if we need to update the database later
		update_option( $this->database_version_option, $version );
	}


	/* ------------------------------------------------------------------------ *
	 * internalisation
	 * ------------------------------------------------------------------------ */

	public function tvp_tdash_load_translations() {
		load_theme_textdomain( $this->text_domain, plugin_dir_path( __DIR__ ) . '/languages' );
	}



	/* ------------------------------------------------------------------------ *
	 * plugin settings page
	 * ------------------------------------------------------------------------ */

	/**
	 * register the plugin page on which all the settings/data will appear.
	 */
	public function tvp_tdash_create_plugin_page() {
		add_plugins_page(
			__( 'TVP Trello Dashboard', tvp_tdash_plugin()->text_domain ),  // the page title
			__( 'TVP Trello Dashboard', tvp_tdash_plugin()->text_domain ),  // the menu title
			'manage_options',                                               // the capability required for this menu to be displayed to the user.
			$this->plugin_page,                                             // the slug name to refer to this menu
			[ $this, $this->prefix . '_render_plugin_page' ]                // the function to be called to output the content for this page.
		);
	}

	/**
	 * this function provides the content for the tvp dashboard page
	 *
	 * it is called from the 'tvp_tdash_create_plugin_page' function by being passed as a parameter
	 * in the add_menu_page function.
	 */
	public function tvp_tdash_render_plugin_page() {

		// if a tab is selected
		if ( isset( $_GET['tab'] ) ) {
			foreach ( $this->plugin_page_tabs as $key => $tab ) {
				if ( $tab->slug == $_GET['tab'] ) {
					$this->active_tab = $tab;
				}
			}
		}

		echo '<div class="wrap">';
		echo '<h2>' . __( 'TVP Trello Dashboard', $this->text_domain ) . '</h2>';

		settings_errors();

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_page_tabs as $key => $tab ) {
			$class = 'nav-tab';
			if ( $this->active_tab ) {
				if ( $this->active_tab->slug == $tab->slug ) {
					$class = $class . ' nav-tab-active';
				}
			}
			echo '<a href="?page=' . $this->plugin_page . '&tab=' . $tab->slug . '" class="' . $class . '">' . $tab->title . '</a>';
		}
		echo '</h2>';

		// if no tab is active, render introduction
		// else run the render function for each tab section
		if ( ! $this->active_tab ) {
			echo '<h3>' . __( 'Introduction to the TVP Trello Dashboard', $this->text_domain ) . '</h3>';
			echo '<p>Etiam porta sem malesuada magna mollis euismod. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla. Aenean lacinia bibendum nulla sed consectetur. Donec ullamcorper nulla non metus auctor fringilla. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean lacinia bibendum nulla sed consectetur. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>';
			echo '<p>Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Sed posuere consectetur est at lobortis. Maecenas sed diam eget risus varius blandit sit amet non magna. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>';

			echo '<ul>';
			echo '<h4>' . __( 'Related Links / Further Reading', $this->text_domain ) . '</h4>';
			echo '<li><a href="#">Read this</a></li>';
			echo '<li><a href="#">or this</a></li>';
			echo '</ul>';
		} elseif ( 'trello_api' == $this->active_tab->slug ) {
			tvp_tdash_plugin()->TrelloAPI->tvp_tdash_render_trello_api_settings();
		} elseif ( 'pages' == $this->active_tab->slug ) {
			tvp_tdash_plugin()->Pages->tvp_tdash_render_pages_settings();
		}

		echo '</div>';
	}
}
