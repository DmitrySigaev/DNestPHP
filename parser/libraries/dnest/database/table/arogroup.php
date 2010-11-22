<?php

// Check to ensure this file is within the rest of the framework
defined('DNEST_PATH_BASE') or die();

/**
 * AroGroup table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableAROGroup extends JTable
{
	/** @var int Primary key */
	var $id			= null;

	var $parent_id	= null;

	var $name		= null;

	var $value		= null;

	var $lft		= null;

	var $rgt		= null;

	function __construct( &$db )
	{
		parent::__construct( '#__core_acl_aro_groups', 'group_id', $db );
	}
}