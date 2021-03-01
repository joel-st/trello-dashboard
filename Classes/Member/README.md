# TVP Trello Dashboard Member Subclasses

All the stuff for the custom member role and its functionality.

`TVP_TD()->Member`

## Contents

The subclasses:
* `./Role.php` => `TVP_TD()->Member->Role` Registration of the endpoint if external apps need to access member data provided by the plugin.
* `./TVPUserList.php` => `this class is not loaded within the plugin, the TVP_TD()->Admin->OptionPages->memberRows() loads this class to show the WP_List_Table on its dedicated options page` The TVPUserList shown under Trello Dashboard > Members based on WP_List_Table.
* `./UserMeta.php` => `TVP_TD()->Member->UserMeta` Registration of custom user meta fields for the TVP Trello Member user role.
