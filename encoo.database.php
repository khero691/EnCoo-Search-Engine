<?

/**
* @author enCoo Developments © Vladyslav Halimskyi 2018
* @package enCoo/SearchEngine/DataBaseConnect
*/

class DatabaseSettings {
	var $settings;
	function getSettings()
	{
		// Database variables
		// Host name
		$settings['dbhost'] = 'localhost';
		// Database name
		$settings['dbname'] = 'db_workbench';
		// Username
		$settings['dbusername'] = 'root';
		// Password
		$settings['dbpassword'] = '';
		
		return $settings;
	}
}
