<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class InstallSimplexmlElement extends SimpleXMLElement
{
	/**
	 * Can add SimpleXMLElement values in XML tree
	 *
	 * @see SimpleXMLElement::addChild()
	 */
	public function addChild($name, $value = null, $namespace = null)
	{
		if ($value instanceof SimplexmlElement)
		{
			$content = trim((string)$value);
			if (strlen($content) > 0)
				$new_element = parent::addChild($name, str_replace('&', '&amp;', $content), $namespace);
			else
			{
				$new_element = parent::addChild($name);
				foreach ($value->attributes() as $k => $v)
					$new_element->addAttribute($k, $v);
			}

			foreach ($value->children() as $child)
				$new_element->addChild($child->getName(), $child);
		}
		else
			return parent::addChild($name, str_replace('&', '&amp;', $value), $namespace);
	}

	/**
	 * Generate nice and sweet XML
	 *
	 * @see SimpleXMLElement::asXML()
	 */
	public function asXML($filename = null)
	{
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML(parent::asXML());

		if ($filename)
			return file_put_contents($filename, $dom->saveXML());
		return $dom->saveXML();
	}
}