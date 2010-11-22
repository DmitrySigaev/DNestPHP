<?php

// Check to ensure this file is within the rest of the framework
defined('DNEST_PATH_BASE') or die();

/**
 * Aro table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableARO extends JTable
{
	/** @var int Primary key */
	var $id			  	= null;

	var $section_value	= null;

	var $value			= null;

	var $order_value	= null;

	var $name			= null;

	var $hidden			= null;

	function __construct( &$db )
	{
		parent::__construct( '#__core_acl_aro', 'aro_id', $db );
	}
}