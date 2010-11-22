<?php

defined('DNEST_PATH_BASE') or die();

/**
 * Joomla Framework Factory class
 *
 * @static
 * @package		DESIGNest.Framework
 * @since	1.5
 */
class JFactory
{

	/**
	 * Get a database object
	 *
	 * Returns a reference to the global {@link JDatabase} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return object JDatabase
	 */
	function &getDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = JFactory::_createDBO();
		}

		return $instance;
	}


	/**
	 * Create an database object
	 *
	 * @access private
	 * @return object JDatabase
	 * @since 1.5
	 */
	function &_createDBO()
	{
		jimport('dnest.database.database');
		jimport( 'dnest.database.table' );

		$host 		= "localhost";
		$user 		= "root";
		$password 	= "base";
		$database	= "base";
		$prefix 	= "jos_";
		$driver 	= "mysql";
		$debug 		= 0;

		$options	= array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

		$db =& JDatabase::getInstance( $options );

		$db->debug( $debug );
		return $db;
	}
	
	/**
	 * Return a reference to the {@link JDate} object
	 *
	 * @access public
	 * @param mixed $time The initial time for the JDate object
	 * @param int $tzOffset The timezone offset.
	 * @return object JDate
	 * @since 1.5
	 */
	function &getDate($time = 'now', $tzOffset = 0)
	{
		jimport('dnest.utilities.date');
		static $instances;
		static $classname;
		static $mainLocale;

		if(!isset($instances)) {
			$instances = array();
		}

		$language =& JFactory::getLanguage();
		$locale = $language->getTag();

		if(!isset($classname) || $locale != $mainLocale) {
			//Store the locale for future reference
			$mainLocale = $locale;
			$localePath = DNEST_PATH_ROOT . DS . 'language' . DS . $mainLocale . DS . $mainLocale . '.date.php';
			if($mainLocale !== false && file_exists($localePath)) {
				$classname = 'JDate'.str_replace('-', '_', $mainLocale);
				JLoader::register( $classname,  $localePath);
				if(!class_exists($classname)) {
					//Something went wrong.  The file exists, but the class does not, default to JDate
					$classname = 'JDate';
				}
			} else {
				//No file, so default to JDate
				$classname = 'JDate';
			}
		}
		$key = $time . '-' . $tzOffset;

		if(!isset($instances[$classname][$key])) {
			$tmp = new $classname($time, $tzOffset);
			//We need to serialize to break the reference
			$instances[$classname][$key] = serialize($tmp);
			unset($tmp);
		}

		$date = unserialize($instances[$classname][$key]);
		return $date;
	}
	
	/**
	 * Get a language object
	 *
	 * Returns a reference to the global {@link JLanguage} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access public
	 * @return object JLanguage
	 */
	function &getLanguage()
	{
		static $instance;

		if (!is_object($instance))
		{
			//get the debug configuration setting
			$conf =& JFactory::getConfig();
			$debug = $conf->getValue('config.debug_lang');

			$instance = JFactory::_createLanguage();
			$instance->setDebug($debug);
		}

		return $instance;
	}
	
	/**
		 * Create a language object
		 *
		 * @access private
		 * @return object JLanguage
		 * @since 1.5
		 */
	function &_createLanguage()
	{
		jimport('dnest.language.language');

		$conf	=& JFactory::getConfig();
		$locale	= $conf->getValue('config.language');
		$lang	=& JLanguage::getInstance($locale);
		$lang->setDebug($conf->getValue('config.debug_lang'));

		return $lang;
	}	
	
	/**
	* Get a configuration object
	*
	* Returns a reference to the global {@link JRegistry} object, only creating it
	* if it doesn't already exist.
	*
	* @access public
	* @param string	The path to the configuration file
	* @param string	The type of the configuration file
	* @return object JRegistry
	*/
	function &getConfig($file = null, $type = 'PHP')
	{
		static $instance;

		if (!is_object($instance))
		{
			if ($file === null) {
				$file = dirname(__FILE__).DS.'config.php';
			}

			$instance = JFactory::_createConfig($file, $type);
		}

		return $instance;
	}

	/**
	 * Create a configuration object
	 *
	 * @access private
	 * @param string	The path to the configuration file
	 * @param string	The type of the configuration file
	 * @return object JRegistry
	 * @since 1.5
	 */
	function &_createConfig($file, $type = 'PHP')
	{
		jimport('dnest.registry.registry');

		require_once $file;

		// Create the registry with a default namespace of config
		$registry = new JRegistry('config');

		// Create the JConfig object
		$config = new JFrameworkConfig();

		// Load the configuration values into the registry
		$registry->loadObject($config);

		return $registry;
	}

}

