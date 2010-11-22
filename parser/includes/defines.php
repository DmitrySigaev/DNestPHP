<?php

// no direct access
defined( '_DNEST_EXEC' ) or die( 'Restricted access' );

/**
* DESIGNest Application define
*/

//Global definitions
//Joomla framework path definitions
$parts = explode( DS, DNEST_PATH_BASE );

//Defines
define( 'DNEST_PATH_ROOT',		implode( DS, $parts ) );

define( 'DNEST_PATH_SITE',		DNEST_PATH_ROOT );
define( 'DNEST_PATH_LIBRARIES',	 	DNEST_PATH_ROOT.DS.'libraries' );
