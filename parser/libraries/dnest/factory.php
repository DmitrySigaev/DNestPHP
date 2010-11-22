<?php

defined('DNEST_PATH_BASE') or die();

/**
 * Joomla Framework Factory class
 *
 * @static
 * @package		Joomla.Framework
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

}
