# TVP Trello Dashboard

Object-oriented WordPress Plugins for The Venus Project to connect a Trello Organization to WordPress.

---

## Website Setup
[https://dashboard.thevenusproject.com/](https://dashboard.thevenusproject.com/)

The Site is hosted on Siteground. The Repository is checked out in the WordPress Plugin directory. To update the plugin just pull the changes with `ssh`.

### Active Roles
* **TVP Trello Member** – Synced users from the trello organization has this role attached.
* **TVP Trello Editor** – Access to the Dashboard Manager page.
* **Administrators** – All caps. Can edit Users, Roles and manage WordPress.

### Cron Job
We defined `define( 'DISABLE_WP_CRON', true );` in the  `wp-config.php` and setup a custom cron job with siteground Site Tools to run `wp-cron.php` once per hour.

### Third Party Plugins
* **WP Crontrol** – Just to check out next schedule for trello data fetch
* **UpdraftPlus - Backup/Restore** – Simple backup solution just for safety :D
* **Transients Manager** – Our plugin works with transients, so this plugin is installed to check out the tranisents.
* **HM Multiple Roles** – Adds checkboxes to user profiles to add/remove roles. Only Administrators can access those checkboxes.
* **WP Htaccess Editor** – As with version 1.0 every request will be redirected to the root address https://dashboard.thevenusproject.com/ since only the dashboard needs to be accessible at the moment.

---

## Development

### Dependencies

The plugin needs the following dependencies in order to run properly:
* [Advanced Custom Fields](https://www.advancedcustomfields.com/)

Also check the third party plugins under _Website Setup_. Those are pretty handy for testing and managing purposes.

### Contents

TVP Trello Dashboard base structure:
* `./tvp-trello-dashboard.php` Initializes the main plugin class and specifies spl_autoload_register class loader for this project. If plugin is loaded, plugin data is available via `TVP_TD()`;
* `./Classes` Plugin classes.
* `./.build` Plugin scripts, styles and media
* `./assets` Compiled plugin scripts, styles and media
* `./languages` Plugin internationalization data

### Features

* All classes and functions are documented so that you know what you need to change. Each folder comes with a custom `README.md` file.
* The project includes a `.pot` file as a starting point for internationalization.
* The project includes a `ruleset.xml`, `.editorconfig`, `.prettierrc`, `.jshintrc` and `.jslintrc` file to handle codeformatting.

### Installation

* Clone the repository in your WordPress instance in `wp-content/plugins/`.
* Activate the plugin trough the WordPress admin dashboard

### Usage / Task Manager
Runs with `Node v14.16.1`, `NPM 6.14.12`, `Gulp ^4.0`
* Clone the repository
* Navigate into the repository with terminal
* Run command `npm install` from terminal
* Run `gulp` from terminal.
* Have fun :)

---

# Authors
- [The Venus Project](https://trello.com/thevenusproject1/) – [Software Team](https://trello.com/b/I7DFoCYx/software-team) – [Dedicated Trello Card](https://trello.com/c/If7ALFK6/40-skills-database)
