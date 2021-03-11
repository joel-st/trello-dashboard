# TVP Trello Dashboard Main Class

The `Plugin.php` file is the starting point for the plugin. It returns the plugin main class, loads the subclasses and setup the plugin internationalization.

## Adding new subclasses
If you need to add a new subclass, add the file to the best matching directory. As example you would like to extend the Admin area with a `New` subclass.
* Add file `New.php` to `./Classes/Admin/`.
* Set namespace `namespace TVP\TrelloDashboard\Admin;`
* Define a `public function run() {…}` in your new class.
* Add `Admin\New::class,` to `$this->loadClasses()` in the `run` function in `./Classes/Plugin.php`.
* The new class will be loaded automatically and the `run`
 function will be executed automatically.
Your new class will be autoloaded and available trough `TVP_TD()->Admin->New`;

## Contents

Main Class: `TVP_TD()`

The main class has several subclasses:
* `./Admin` All stuff to provide admin interfaces.
* `./API` All stuff to fetch relevant data provided by the plugin.
* `./Member` All the stuff for the custom member role and its functionality.
* `./Options` Plugin options built with [Advanced Custom Fields](https://www.advancedcustomfields.com/).
* `./Public` All stuff to provide public (frontend) interfaces e.g. the Volunteer Dashboard.
* `./Trello` All stuff to connect to and fetch from a Trello Organization.
