# TVP Trello Dashboard

The TVP Trello Dashboard Plugin is a custom Plugin developed to store, manage and visualize Data from the [TVP Trello Organization](https://trello.com/thevenusproject1). You can find further Information of the development Process in the [Skills-Database Card](https://trello.com/c/If7ALFK6/40-skills-database) on Trello or read the below Documentation.

## Setup
Install the Plugin in your WordPress Installation under `Plugins > Install Plugin` and activate it.

## Features

#### Trello Dashboard
A Dashboard from Trello Boards, Cards, Tasks and Members. The Dashboard offers the following possibilities:
* Show recent activities from the Trello Organization
* Show urgent calls from Trello Boards, Cards and Tasks
* Search for Trello Members

#### Skills Database
Three Database Tables will be generated, which store Information related to the Members of the Trello Organization. The Main Database is the `Volunteers-Database`. Each Volunteer refers to a Trello Member. Each Member is related to different Skills and Divisions from the `Skills-Database` and `Divisions-Database`.
* `Volunteers-Database` - Database to manage Trello Members
* `Skills-Database` - Database to manage Skill-Sets (LinkedIn Skill-Set and custom Skills)
* `Divisions-Database` - Database to manage Divisions

#### Serve the TVP intake Process
The Plugin serves a Form for new Volunteers to make the intake process easier, so that all new Volunteers have the ability to let others know what they are able to do.

## Documentation

#### Plugin Files

###### `tvp-trello-dashboard.php`
This File holds the Plugin Information, Requirements to be activation, deactivation and delete hooks. If the Plugin is successfully activated, it runs the main `Plugin` Class.

###### `Classes/class-plugin.php`
This File contains the main `Plugin` Class. It loads the Plugin Translations, registers a Plugin Settings Page for the Plugin Options and loads all further Plugin Classes.

###### `Package/class-trelloapi.php`
This File contains the `TrelloApi` Class to register Settings Fields for the API Credidentials.

###### `Package/class-pages.php`
This File contains the `Pages` Class to register Settings Fields to choose the following Pages from the existing Pages in WordPress.

* The Trello Dashboard
* The Submission Form

###### `ruleset.xml`
Holds the Ruleset for Code-Formatting depending on the `WordPress-Core` Coding-Standards.
