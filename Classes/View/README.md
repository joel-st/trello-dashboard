# TVP Trello Dashboard View Subclasses

All stuff to provide public (frontend) interfaces e.g. the Volunteer Dashboard

`TVP_TD()->View`

## Contents

The subclasses:
* `./Ajax.php` => `TVP_TD()->View->Ajax` Ajax calls to load stuff via ajax on the dashboard
* `./Dashboard.php` => `TVP_TD()->View->Dashboard` All stuff to display the dashboard in the frontend
* `./NotInOrganization.php` => `TVP_TD()->View->NotInOrganization` All stuff to create the not in organization page
* `./SignUp.php` => `TVP_TD()->View->SignUp` All stuff to create the sign up page for TVP Trello Member users

## Basic setup

A short overview for the current setup/flow:
* If the dashboard url is visited, a default loading markup will be displayed
* If the loading markup is loaded, a bunch of stuff will be triggered. Check out the javascript files in `.build/assets/scripts/public`
* `.build/assets/scripts/public/loading.js` will check if it is the loading markup
* If so, the script will check if the trello authentication token is saved in the local storage (by the trello client.js authentication method in `.build/assets/scripts/public/trello.js`) and the authCookie exists which will be set by the `TVP_TD()->View->Ajax->validateLogin()`.
* If no auth data is found, the signup markup will be loaded to trigger the trello authentication `.build/assets/scripts/public/trello.js`
* If auth data is found, but the authenticated member is not a member of the specified trello organization, the not in organization markup will be loaded
* If auth data is found and member is within the specified trello organization, we will try to find existing WordPress user based on the authenticated trello member and continue the login process `TVP_TD()->View->Ajax->validateLogin()`
