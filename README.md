# Composer Autoload for WordPress

**Seamlessly integrate Composer into a standard WordPress installation.**  

This plugin automatically loads Composer’s `vendor/autoload.php` (via an automatically generated **must-use (MU) plugin**), so any Composer-installed libraries are available without modifying WordPress core, themes, plugins or restructuring directories.  

**Key features:**  
- Drop-in solution for stock WordPress installs  
- No changes to `wp-config.php` or core files  
- Fully automatic Composer autoloader inclusion  
- Works with themes, plugins, or custom PHP libraries managed via Composer  

**Usage:**  

1. **If Composer is not yet used in the project:**  
   - Initialize Composer in your WordPress root:  
     ```bash
     composer init
     ```  
   - Install any libraries you need, e.g.:  
     ```bash
     composer require monolog/monolog
     ```  
   - Require this plugin:  
     ```bash
     composer require wp-plus/composer-autoload
     ```  
   - Done - all Composer dependencies, including the newly added ones, are automatically available in WordPress.  

2. **If the project is already using Composer:**  
   - Require this plugin via Composer:  
     ```bash
     composer require wp-plus/composer-autoload
     ```  
   - Done - your existing Composer dependencies are now autoloaded in WordPress.  
   - **Optional cleanup:** remove any hardcoded `require 'vendor/autoload.php'` lines from your theme or plugin files, as this plugin handles it automatically.  
