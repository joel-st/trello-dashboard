<?php
/**
 * @package TVP Trello Dashboard
 */
/*
Plugin Name: TVP Trello Dashboard
Plugin URI: https://github.com/thevenusproject/trello-dashboard
Description: Custom TVP Trello Dashboard for The Venus Project Website
Version: 1.0.0
Author: Website Team of The Venus Project
Author URI: http://thevenusproject.com
Text Domain: tvp_tdash
Domain Path: /languages
License: GPL2

TVP Trello Dashboard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

TVP Trello Dashboard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with TVP Trello Dashboard. If not, see {License URI}.
*/

global $wp_version;

if ( version_compare( $wp_version, '4.7', '<' ) || version_compare( PHP_VERSION, '5.4', '<' ) ) {


	function tvp_tdash_compatability_warning() {

		$plugin = get_file_data(
			plugin_dir_path( __FILE__ ) . 'tvp-trello-dashboard.php',
			[
				'name'        => 'Plugin Name',
				'version'     => 'Version',
				'prefix'      => 'Text Domain',
				'text_domain' => 'Text Domain',
			],
			'plugin'
		);
		$plugin = (object) $plugin;

		echo '<div class="error"><p>';
		// translators: Dependency waring
		echo sprintf( __( '%1$s requires PHP %2$s (or newer) and WordPress %3$s (or newer) to function properly. Your site is using PHP %4$s and WordPress %5$s. Please upgrade. The plugin has been automatically deactivated.', $plugin->text_domain ), '<strong>' . $plugin->name . '</strong>', '5.3', '4.7', PHP_VERSION, $GLOBALS['wp_version'] );
		echo '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	add_action( 'admin_notices', 'tvp_tdash_compatability_warning' );

	function tvp_tdash_deactivate_self() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_init', 'tvp_tdash_deactivate_self' );

	return;

} else {

	require_once 'Classes/class-plugin.php';

	/**
	 * returns the plugin instance
	 *
	 * @return Object plugin object
	 */
	if ( ! function_exists( 'tvp_tdash_plugin' ) ) {
		function tvp_tdash_plugin() {
			return TvpTrelloDashboard\Plugin\Plugin::tvp_tdash_get_instance( __FILE__ );
		}
	}

	tvp_tdash_plugin();
	tvp_tdash_plugin()->run();
}
