<?php
/**
 * VgotFaster PHP Framework
 *
 * Form Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2011, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */
if (!function_exists('formRadio'))
{
	function formRadio($name,$radios,$value=NULL) {
		$r = '';
		foreach ($radios as $v => $k) {
			$r .= '<label><input type="radio" name="'.$name.'" value="'.$v.'" '.($v == $value ? 'checked="checked"' : '').' /> '.$k.'</label> ';
		}
		return $r;
	}
}
/**
 * Create Options HTML For Select
 *
 * @param array $options Options data array
 * @param string $selected The selected value, default null
 * @param string $valueFrom key|value|only
 * @return string
 */
if (!function_exists('formOptions'))
{
	function formOptions(array $options,$selected='',$valueFrom='key') {
		$o = '';
		$valueFrom = strtolower($valueFrom);
		$selected = (string)$selected;
		switch ($valueFrom) {
			case 'value':
				foreach ($options as $text => $val) {
					$o .= $selected === (string)$val ? "<option value=\"$val\" selected=\"selected\">$text</option>\n" : "<option value=\"$val\">$text</option>\n";
				}
				break;
			case 'only':
				foreach ($options as $val) {
					$o .= $selected === (string)$val ? "<option value=\"$val\" selected=\"selected\">$val</option>\n" : "<option value=\"$val\">$val</option>\n";
				}
				break;
			case 'key':
			default:
				foreach ($options as $val => $text) {
					$o .= $selected === (string)$val ? "<option value=\"$val\" selected=\"selected\">$text</option>\n" : "<option value=\"$val\">$text</option>\n";
				}
		}
		return $o;
	}
}
