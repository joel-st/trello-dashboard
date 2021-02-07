<?php

namespace TVP\TrelloDashboard;

class Plugin
{
	private static $instance;
	public $file = '';
	public $pluginName = '';
	public $version = '';
	public $textDomain = '';
	public $prefix = '';

	/**
	 * Creates an instance if one isn't already available,
	 * then return the current instance.
	 */
	public static function getInstance($file)
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Plugin)) {
			if (!function_exists('get_plugin_data')) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			self::$instance = new Plugin;
			$data = get_plugin_data($file);
			self::$instance->file = $file;
			self::$instance->version = $data['Version'];
			self::$instance->textDomain = $data['TextDomain'];
			self::$instance->pluginName = $data['Name'];
			self::$instance->prefix = 'tvptd';
		}

		return self::$instance;
	}

	/**
	 * Execution function which is called after the class has been initialized.
	 * This contains hook and filter assignments, etc.
	 */
	public function run()
	{
		// load plugin classes
		$this->loadClasses(
			[
				Member\Role::class,
				Admin\OptionPages::class,
				Admin\Assets::class,
				Options\TrelloIntegration::class,
				Options\InformationManager::class,
				Options\Member::class,
				API\Member::class,
				Public\Hub::class,
				Public\Assets::class,
			]
		);

		// internalization
		add_action('plugins_loaded', [$this, 'loadPluginTextdomain']);
	}

	/**
	 * Loads and initializes the provided classes.
	 */
	private function loadClasses($classes)
	{
		foreach ($classes as $class) {
			$classParts = explode('\\', $class);
			$classShort = end($classParts);
			$classSet   = $classParts[count($classParts) - 2];

			if (!isset(TVP_TD()->{$classSet}) || !is_object(TVP_TD()->{$classSet})) {
				TVP_TD()->{$classSet} = new \stdClass();
			}

			if (property_exists(TVP_TD()->{$classSet}, $classShort)) {
				wp_die(sprintf(_x('A problem occured in %1$s. Only one PHP class «%2$s» can be registered on «%3$s».', 'Duplicate PHP class assignmment in Plugin', $this->textDomain), $this->pluginName, $classShort, $classSet), 500);
			}

			TVP_TD()->{$classSet}->{$classShort} = new $class();

			if (method_exists(TVP_TD()->{$classSet}->{$classShort}, 'run')) {
				TVP_TD()->{$classSet}->{$classShort}->run();
			}
		}
	}

	/**
	 * Load translation files from the indicated directory.
	 */
	public function loadPluginTextdomain()
	{
		load_plugin_textdomain($this->textDomain, false, dirname(plugin_basename($this->file)) . '/languages');
	}
}
