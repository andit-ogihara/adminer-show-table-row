# adminer-show-table-row
Adds posibility to show table row from table select page

# Install
[Detailed Information](https://www.adminer.org/en/plugins/)

Download show-table-row.php file to plugins folder in your server.

Example folder construction:
```
adminer-folder/
 - adminer.php
 - index.php
 - plugins/
     - plugin.php
     - show-table-row.php
```

Example of index.php:
```php
function adminer_object() {
    // required to run any plugin
    include_once "./plugins/plugin.php";
    
    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        // specify enabled plugins here
        // other plugins
        new AdminerShowTableRow(),
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
}
// include original Adminer or Adminer Editor
include "./adminer.php";
