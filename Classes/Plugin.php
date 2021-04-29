<?php

namespace TVP\TrelloDashboard;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Plugin
{
	private static $instance;
	public $file = '';
	public $pluginName = '';
	public $version = '';
	public $textDomain = '';
	public $prefix = '';
	public $pluginDirUrl = '';
	public $pluginDirPath = '';
	public $assetsDirUrl = '';
	public $assetsDirPath = '';
	public $menuPositionBase = 80;

	// security stuff
	public $securityCiphering;
	public $securityIvLength;
	public $securityOptions;
	public $securityIv;
	public $securityKey;
	public $ajaxNonceKey;
	public $authCookie;

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
			self::$instance->pluginDirUrl = plugin_dir_url(__DIR__);
			self::$instance->pluginDirPath = plugin_dir_path(__DIR__);
			self::$instance->assetsDirUrl = plugin_dir_url(__DIR__) . 'assets/';
			self::$instance->assetsDirPath = plugin_dir_path(__DIR__) . 'assets/';

			self::$instance->securityCiphering = 'AES-128-CTR';
			self::$instance->securityIvLength = openssl_cipher_iv_length(self::$instance->securityCiphering); // Use OpenSSl Encryption method
			self::$instance->securityOptions = 0;
			self::$instance->securityIv = '4204204204204204'; // Non-NULL Initialization Vector for encryption
			self::$instance->securityKey = 'tvptd'; // Store the encryption key
			self::$instance->ajaxNonceKey = 'tvptdajaxnonce';
			self::$instance->authCookie = self::$instance->prefix . '-dashboard-auth';
		}

		return self::$instance;
	}

	/**
	 * Execution function which is called after the class has been initialized.
	 * This contains hook and filter assignments, etc.
	 */
	public function run()
	{
		// load plugin classes, the order matters :D
		$this->loadClasses(
			[
				Member\Role::class,
				Member\UserMeta::class,
				Trello\Action::class,
				Admin\OptionPages::class,
				Admin\Assets::class,
				Options\TrelloIntegration::class,
				Options\DashboardManager::class,
				// Options\Member::class,
				Trello\API::class,
				Trello\Cron::class,
				Trello\DataProcessor::class,
				API\Member::class,
				API\Action::class,
				View\Dashboard::class,
				View\SignUp::class,
				View\NotInOrganization::class,
				View\Ajax::class,
			]
		);

		// internationalization
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

	/**
	 * Password encryption function
	 */
	public function encryptPassword($password)
	{
		if (!empty($password)) {
			$password = openssl_encrypt($password, $this->securityCiphering, $this->securityKey, $this->securityOptions, $this->securityIv);
		}

		return $password;
	}

	/**
	 * Password decryption function
	 */
	public function decryptPassword($password)
	{
		if (!empty($password)) {
			$password = openssl_decrypt($password, $this->securityCiphering, $this->securityKey, $this->securityOptions, $this->securityIv);
		}

		return $password;
	}

	/**
	 * Pass internationalized strings to use within javascript
	 */
	public function getJavaScriptInternationalization()
	{
		return [
			'notificationLoading' => _x('Loading …', 'Loading message JavaScript notification', 'tvp-trello-dashboard'),
			'trelloAuthenticationFailure' => _x('Failed authentication', 'Frontend JavaScript login with Trello.authorize() failed', 'tvp-trello-dashboard'),
			'trelloAuthenticationSuccess' => _x('Successful authentication', 'Frontend JavaScript login with Trello.authorize() success', 'tvp-trello-dashboard'),
			'trelloIntegrationTestFailed' => _x('Connection failed!', 'Admin JavaScript integration test failed metabox message', 'tvp-trello-dashboard'),
			'trelloIntegrationTestConnectedAs' => _x('Connected as', 'Admin JavaScript integration test "Connected as [username]" metabox message', 'tvp-trello-dashboard'),
		];
	}

	/**
	 * Plugin variables, mainly to use within javascript
	 * TODO: propper setup of nonces for ajax calls
	 */
	public function getTdVars()
	{
		return [
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'i18n' => TVP_TD()->getJavaScriptInternationalization(),
			'authCookie' => $this->authCookie,
			'trelloOrganization' => TVP_TD()->Options->TrelloIntegration->getOrganizationId(),
			'nonces' => [
				'signup' => wp_create_nonce($this->ajaxNonceKey . '-signup'),
				'content' => wp_create_nonce($this->ajaxNonceKey . '-content'),
				'login' => wp_create_nonce($this->ajaxNonceKey . '-login'),
				'logout' => wp_create_nonce($this->ajaxNonceKey . '-logout'),
			],
			'adminOptionsSlug' => TVP_TD()->Admin->OptionPages->slugTrelloIntegration,
			'postTypeAction' => TVP_TD()->Trello->Action->postType,
			'taxonomyBoard' => TVP_TD()->Trello->Action->boardTaxonomy,
			'taxonomyCard' => TVP_TD()->Trello->Action->cardTaxonomy,
			'taxonomyList' => TVP_TD()->Trello->Action->listTaxonomy
		];
	}
}
