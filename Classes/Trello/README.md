# TVP Trello Dashboard Trello Subclasses

All stuff to connect to and fetch data from a Trello Organization.

`TVP_TD()->Trello`

## Contents

The subclasses:
* `./Action.php` => `TVP_TD()->Trello->Action` Class to setup post type Action which holds all the Trello data.
* `./API.php` => `TVP_TD()->Trello->API` Class with functions to fetch data from Trello.
* `./Cron.php` => `TVP_TD()->Trello->Cron` Cron job to fetch data from Trello Organization in time interval.
* `./DataProcessor.php` => `TVP_TD()->Trello->DataProcessor` All the stuff to process the Trello API Date and save it to ether Actions or Members.
