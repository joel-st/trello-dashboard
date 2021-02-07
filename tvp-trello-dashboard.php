<?php
/**
 * TVP Trello Dashboard
 *
 * // TODO: Add description
 *
 * @link    https://dashboard.thevenusproject.com/
 * @since   1.0.0
 * @package TVP_TD
 *
 * @wordpress-plugin
 * Plugin Name:       TVP Trello Dashboard
 * Plugin URI:        https://github.com/thevenusproject/trello-dashboard
 * Description:       Customized Trello Dashboard for The Venus Project
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      8.0
 * Author:            The Venus Project
 * Author URI:        https://github.com/thevenusproject
 * License:           –--
 * License URI:       https://github.com/thevenusproject/trello-dashboard
 * Text Domain:       tvp-trello-dashboard
 * Domain Path:       /languages
 */

/*
 * This lot auto-loads a class or trait just when you need it. You don't need to
 * use require, include or anything to get the class/trait files, as long
 * as they are stored in the correct folder and use the correct namespaces.
 *
 * See http://www.php-fig.org/psr/psr-4/ for an explanation of the file structure
 * and https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md for usage examples.
 */

if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

spl_autoload_register(
    function ($class) {
        // project-specific namespace prefix
        $prefix = 'TVP\\TrelloDashboard\\';

        // base directory for the namespace prefix
        $baseDir = __DIR__ . '/Classes/';

        $relativeClass = str_replace($prefix, '', $class);

        $file = $baseDir . str_replace('\\', '/', str_replace($prefix, '', $relativeClass)) . '.php';
        $classExplode = explode('\\', $relativeClass);

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // if the file exists, require it
        if (file_exists($file)) {
            include $file;
        }
    }
);

/**
 * Returns the Plugin Instance
 *
 * @return Object Plugin Object
 */
if (!function_exists('TVP_TD')) {
    function TVP_TD()
    {
        return TVP\TrelloDashboard\Plugin::getInstance(__FILE__);
    }

    TVP_TD();
    TVP_TD()->run();
}
