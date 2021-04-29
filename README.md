# TVP Trello Dashboard

Object-oriented WordPress Plugins for The Venus Project to connect a Trello Organization to WordPress.

## Dependencies

The plugin needs the following dependencies in order to run properly:
* [Advanced Custom Fields](https://www.advancedcustomfields.com/)

## Contents

TVP Trello Dashboard base structure:
* `./tvp-trello-dashboard.php` Initializes the main plugin class and specifies spl_autoload_register class loader for this project. If plugin is loaded, plugin data is available via `TVP_TD()`;
* `./Classes` Plugin classes.
* `./.build` Plugin scripts, styles and media
* `./assets` Compiled plugin scripts, styles and media
* `./languages` Plugin internationalization data

## Features

* All classes and functions are documented so that you know what you need to change. Each folder comes with a custom `README.md` file.
* The project includes a `.pot` file as a starting point for internationalization.
* The project includes a `ruleset.xml`, `.editorconfig`, `.prettierrc`, `.jshintrc` and `.jslintrc` file to handle codeformatting.

## Installation

* Clone the repository in your WordPress instance in `wp-content/plugins/`.
* Activate the plugin trough the WordPress admin dashboard

### Development
Runs with `Node v14.16.1`, `NPM 6.14.12`, `Gulp ^4.0`
* Clone the repository
* Navigate into the repository within terminal
* Run command `npm install` from terminal
* Run `gulp` from terminal.
