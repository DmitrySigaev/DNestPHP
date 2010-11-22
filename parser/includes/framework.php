<?php

// no direct access
defined( '_DNEST_EXEC' ) or die( 'Restricted access' );

/*
 * DESIGNest! system checks
 */

@set_magic_quotes_runtime( 0 );
@ini_set('zend.ze1_compatibility_mode', '0');


/*
 * DESIGNest system startup
 */
require_once(DNEST_PATH_LIBRARIES.DS.'dnest'.DS.'import.php');

jimport( 'dnest.utilities.string' );
//jimport( 'dnest.database.database' );
//jimport( 'dnest.database.table' );

?>
