<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class InstallLanguage
{
	/**
	 * @var string Current language folder
	 */
	protected $path;

	/**
	 * @var string Current language iso
	 */
	protected $iso;

	/**
	 * @var array Cache list of installer translations for this language
	 */
	protected $data;

	protected $fixtures_data;

	/**
	 * @var array Cache list of informations in language.xml file
	 */
	protected $meta;

	/**
	 * @var array Cache list of countries for this language
	 */
	protected $countries;

	public function __construct($iso)
	{
		$this->path = _PS_INSTALL_LANGS_PATH_.$iso.'/';
		$this->iso = $iso;
	}

	/**
	 * Get iso for current language
	 *
	 * @return string
	 */
	public function getIso()
	{
		return $this->iso;
	}

	/**
	 * Get an information from language.xml file (E.g. $this->getMetaInformation('name'))
	 *
	 * @param string $key
	 * @return string
	 */
	public function getMetaInformation($key)
	{
		if (!is_array($this->meta))
		{
			$this->meta = array();
			$xml = simplexml_load_file($this->path.'language.xml');
			foreach ($xml->children() as $node)
				$this->meta[$node->getName()] = (string)$node;
		}

		return isset($this->meta[$key]) ? $this->meta[$key] : null;
	}

	public function getTranslation($key, $type = 'translations')
	{
		if (!is_array($this->data))
			$this->data = file_exists($this->path.'install.php') ? include($this->path.'install.php') : array();

		return isset($this->data[$type][$key]) ? $this->data[$type][$key] : null;
	}

	public function getCountries()
	{
		if (!is_array($this->countries))
		{
			$this->countries = array();
			if (file_exists($this->path.'data/country.xml'))
			{
				if ($xml = simplexml_load_file($this->path.'data/country.xml'))
					foreach ($xml->country as $country)
						$this->countries[strtolower((string)$country['id'])] = (string)$country->name;
			}
		}
		return $this->countries;
	}
}
