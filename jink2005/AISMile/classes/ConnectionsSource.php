<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class ConnectionsSourceCore extends ObjectModel
{
	public $id_connections;
	public $http_referer;
	public $request_uri;
	public $keywords;
	public $date_add;
	public static $uri_max_size = 255;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'connections_source',
		'primary' => 'id_connections_source',
		'fields' => array(
			'id_connections' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'http_referer' => 	array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
			'request_uri' => 	array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
			'keywords' => 		array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
		),
	);

	public function add($autodate = true, $nullValues = false)
	{
		if ($result = parent::add($autodate, $nullValues))
			Referrer::cacheNewSource($this->id);
		return $result;
	}
	
	public static function logHttpReferer(Cookie $cookie = null)
	{
		if (!$cookie)
			$cookie = Context::getContext()->cookie;
		if (!isset($cookie->id_connections) || !Validate::isUnsignedId($cookie->id_connections))
			return false;
		if (!isset($_SERVER['HTTP_REFERER']) && !Configuration::get('TRACKING_DIRECT_TRAFFIC'))
			return false;
		
		$source = new ConnectionsSource();
		if (isset($_SERVER['HTTP_REFERER']) && Validate::isAbsoluteUrl($_SERVER['HTTP_REFERER']))
		{
			$parsed = parse_url($_SERVER['HTTP_REFERER']);
			$parsed_host = parse_url(Tools::getProtocol().Tools::getHttpHost(false, false).__PS_BASE_URI__);
			if ((preg_replace('/^www./', '', $parsed['host']) == preg_replace('/^www./', '', Tools::getHttpHost(false, false))) 
				&& !strncmp($parsed['path'], $parsed_host['path'], strlen(__PS_BASE_URI__)))
				return false;
			if (Validate::isAbsoluteUrl(strval($_SERVER['HTTP_REFERER'])))
			{
				$source->http_referer = substr(strval($_SERVER['HTTP_REFERER']), 0, ConnectionsSource::$uri_max_size);
				$source->keywords = trim(SearchEngine::getKeywords(strval($_SERVER['HTTP_REFERER'])));
				if (!Validate::isMessage($source->keywords))
					return false;
			}
		}
		
		$source->id_connections = (int)$cookie->id_connections;
		$source->request_uri = Tools::getHttpHost(false, false);
		if (isset($_SERVER['REDIRECT_URL']))
			$source->request_uri .= strval($_SERVER['REDIRECT_URL']);
		elseif (isset($_SERVER['REQUEST_URI']))
			$source->request_uri .= strval($_SERVER['REQUEST_URI']);
		if (!Validate::isUrl($source->request_uri))
			$source->request_uri = '';
		$source->request_uri = substr($source->request_uri, 0, ConnectionsSource::$uri_max_size);
		return $source->add();
	}
	
	public static function getOrderSources($id_order)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cos.http_referer, cos.request_uri, cos.keywords, cos.date_add
		FROM '._DB_PREFIX_.'orders o
		INNER JOIN '._DB_PREFIX_.'guest g ON g.id_customer = o.id_customer
		INNER JOIN '._DB_PREFIX_.'connections co  ON co.id_guest = g.id_guest
		INNER JOIN '._DB_PREFIX_.'connections_source cos ON cos.id_connections = co.id_connections
		WHERE id_order = '.(int)($id_order).'
		ORDER BY cos.date_add DESC');
	}
}
