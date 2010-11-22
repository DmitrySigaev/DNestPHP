<?php

// Check to ensure this file is within the rest of the framework
defined('DNEST_PATH_BASE') or die();

/**
 * PHP class format handler for JRegistry
 *
 * @package 	Joomla.Framework
 * @subpackage		Registry
 * @since		1.5
 */
class JRegistryFormatPHP extends JRegistryFormat {

	/**
	 * Converts an object into a php class string.
	 * 	- NOTE: Only one depth level is supported.
	 *
	 * @access public
	 * @param object $object Data Source Object
	 * @param array  $param  Parameters used by the formatter
	 * @return string Config class formatted string
	 * @since 1.5
	 */
	function objectToString( &$object, $params ) {

		// Build the object variables string
		$vars = '';
		foreach (get_object_vars( $object ) as $k => $v)
		{
			if (is_scalar($v)) {
				$vars .= "\tvar $". $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			} elseif (is_array($v)) {
				$vars .= "\tvar $". $k . " = " . $this->_getArrayString($v) . ";\n";
			}
		}

		$str = "<?php\nclass ".$params['class']." {\n";
		$str .= $vars;
		$str .= "}\n?>";

		return $str;
	}

	/**
	 * Placeholder method
	 *
	 * @access public
	 * @return boolean True
	 * @since 1.5
	 */
	function stringToObject() {
		return true;
	}

	function _getArrayString($a)
	{
		$s = 'array(';
		$i = 0;
		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"'.$k.'" => ';
			if (is_array($v)) {
				$s .= $this->_getArrayString($v);
			} else {
				$s .= '"'.addslashes($v).'"';
			}
			$i++;
		}
		$s .= ')';
		return $s;
	}
}
