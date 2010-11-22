<?php


// no direct access
defined( '_DNEST_EXEC' ) or die( 'Restricted access' );

/**
 * Load the loader class
 */
if (! class_exists('JLoader')) {
    require_once( DNEST_PATH_LIBRARIES.DS.'loader.php');
}

//Base classes
JLoader::import( 'dnest.base.object' 			);

//Factory class and methods
JLoader::import( 'dnest.factory' 				);

//Filters
JLoader::import( 'dnest.filter.filterinput'	);
JLoader::import( 'dnest.filter.filteroutput'	);

