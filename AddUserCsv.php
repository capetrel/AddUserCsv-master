<?php

class AddUserCsv extends PluginAbstract {
    /**
     * @var string Name of plugin
     */
    public $name = 'Add User Csv';

    /**
     * @var string Description of plugin
     */
    public $description = 'Plugin which load and parse a csv file for adding users';

    /**
     * @var string Name of plugin author
     */
    public $author = 'Claude-Alban Petrelluzzi';

    /**
     * @var string URL to plugin's website
     */
    public $url = 'www.capetrel.fr';

    /**
     * @var string Current version of plugin
     */
    public $version = '1.0.0';

    /**
     * The plugin's gateway into core. Place plugin hook attachments here.
     * Can attach plugins to view
     */
    public function load() {

    	// Call settings methode for display form file explore, and treatment of this one.
    	// http://cumulusclips.org/docs/plugin-hooks/
        Plugin::attachEvent('theme.body_style', array(__CLASS__, 'Settings'));

    }

    /**
     * use for install custom needs for plugins in BDD
     */
    public function Install() {

    	// Add a field to the table 'settings' like "key", "value"
    	Settings::Set ("add_users_csv", "");

    	// get the value in the key
    	// Settings::get("choose_file");

    }

    /**
     * remove all custom needs
     */
    public function Uninstall() {

    	// this line remove the entry in database with the key.
    	Settings::remove("add_users_csv");

    }

    /**
     * add "settings" tab under module name
     * Outputs the settings page HTML and handles form posts on the plugin's
     * settings page.
     */

	public function Settings() {

		// dirname returns a parent directory's path. dirname(__FILE__) Get the directory of current included file.
		include(dirname(__FILE__) . '/addCsv.phtml');

    }


}
