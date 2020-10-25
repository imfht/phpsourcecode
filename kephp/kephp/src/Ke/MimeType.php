<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke;


class MimeType
{

	const BINARY = 'binary';

	const NONE_ENC = 0;

	const SPEC_ENC = 1;

	const APP_ENC = 2;

	const FILE_MIME = 1;

	const FILE_ENC = 2;

	const FILE_ALL = 3;

	// http常见的文本类型
	const TYPE_HTML = 'text/html';
	const TYPE_TXT  = 'text/plain';
	const TYPE_CSS  = 'text/css';
	const TYPE_CSV  = 'text/csv';
	const TYPE_JS   = 'application/javascript';
	const TYPE_JSON = 'application/json';
	// xml族
	const TYPE_XML  = 'application/xml';
	const TYPE_RSS  = 'application/rss+xml';
	const TYPE_ATOM = 'application/atom+xml';
	// 图片类型
	const TYPE_GIF  = 'image/gif';
	const TYPE_JPEG = 'image/jpeg';
	const TYPE_PNG  = 'image/png';
	// 打包、压缩类型，phar, jar
	const TYPE_ZIP  = 'application/zip';
	const TYPE_TAR  = 'application/x-tar';
	const TYPE_RAR  = 'application/x-rar-compressed';
	const TYPE_7Z   = 'application/x-7z-compressed';
	const TYPE_BZ   = 'application/x-bzip';
	const TYPE_BZ2  = 'application/x-bzip2';
	const TYPE_GZIP = 'application/x-gzip';
	// 特定的文档类型
	const TYPE_PDF  = 'application/pdf';
	const TYPE_DOC  = 'application/msword';
	const TYPE_XLS  = 'application/vnd.ms-excel';
	const TYPE_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	const TYPE_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	// stream
	const TYPE_STREAM = 'application/octet-stream';

	protected $types = [
		self::TYPE_HTML   => [self::APP_ENC,],
		self::TYPE_TXT    => [self::APP_ENC,],
		self::TYPE_CSS    => [self::APP_ENC,],
		self::TYPE_CSV    => [self::APP_ENC,],
		self::TYPE_JS     => [self::APP_ENC,],
		self::TYPE_JSON   => [self::APP_ENC,],
		self::TYPE_XML    => [self::APP_ENC,],
		self::TYPE_RSS    => [self::APP_ENC,],
		self::TYPE_ATOM   => [self::APP_ENC,],
		self::TYPE_GIF    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_JPEG   => [self::SPEC_ENC, self::BINARY],
		self::TYPE_PNG    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_ZIP    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_TAR    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_RAR    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_7Z     => [self::SPEC_ENC, self::BINARY],
		self::TYPE_BZ     => [self::SPEC_ENC, self::BINARY],
		self::TYPE_BZ2    => [self::SPEC_ENC, self::BINARY],
		self::TYPE_GZIP   => [self::SPEC_ENC, self::BINARY],
		self::TYPE_PDF    => [self::NONE_ENC,],
		self::TYPE_DOC    => [self::NONE_ENC,],
		self::TYPE_XLS    => [self::NONE_ENC,],
		self::TYPE_DOCX   => [self::NONE_ENC,],
		self::TYPE_XLSX   => [self::NONE_ENC,],
		self::TYPE_STREAM => [self::SPEC_ENC, self::BINARY],
	];

	protected $aliases = [
		'application/xhtml+xml'    => self::TYPE_HTML,
		'application/x-javascript' => self::TYPE_JS,
		'text/javascript'          => self::TYPE_JS,
		'text/xml'                 => self::TYPE_XML,
		'application/x-xml'        => self::TYPE_XML,
		'text/x-json'              => self::TYPE_JSON,
		'application/jsonrequest'  => self::TYPE_JSON,
	];

	protected $extensions = [
		'html' => self::TYPE_HTML,
		'txt'  => self::TYPE_TXT,
		'css'  => self::TYPE_CSS,
		'csv'  => self::TYPE_CSV,
		'js'   => self::TYPE_JS,
		'json' => self::TYPE_JSON,
		'xml'  => self::TYPE_XML,
		'rss'  => self::TYPE_RSS,
		'atom' => self::TYPE_ATOM,
		'gif'  => self::TYPE_GIF,
		'jpg'  => self::TYPE_JPEG,
		'jpeg' => self::TYPE_JPEG,
		'png'  => self::TYPE_PNG,
		'zip'  => self::TYPE_ZIP,
		'tar'  => self::TYPE_TAR,
		'rar'  => self::TYPE_RAR,
		'7z'   => self::TYPE_7Z,
		'bz'   => self::TYPE_BZ,
		'bz2'  => self::TYPE_BZ2,
		'gz'   => self::TYPE_GZIP,
		'pdf'  => self::TYPE_PDF,
		'doc'  => self::TYPE_DOC,
		'docx' => self::TYPE_DOCX,
		'xls'  => self::TYPE_XLS,
		'xlsx' => self::TYPE_XLSX,
		'bin'  => self::TYPE_STREAM,
	];

	/**
	 * @param string $mime
	 * @param int    $enc
	 * @return bool|int
	 */
	public function define($mime, $enc = self::NONE_ENC)
	{
		if (empty($mime))
			return $this;
		if (is_string($mime)) {
			if (isset($this->aliases[$mime]))
				$mime = $this->aliases[$mime];
			if (!isset($this->types[$mime]))
				$mime = strtolower($mime);
			$setting = null;
			if ($enc === self::APP_ENC || $enc === self::NONE_ENC) {
				$setting = [$enc];
			}
			elseif (is_string($enc) && !empty(($enc = trim($enc)))) {
				$setting = [self::SPEC_ENC, $enc];
			}
			else {
				$setting = [self::NONE_ENC];
			}
			$this->types[$mime] = $setting;
		}
		elseif (is_array($mime)) {
			$total = 0;
			foreach ($mime as $args)
				$total += $this->define(...(array)$args);
		}
		return $this;
	}

	/**
	 * 取得已经定义的全部Mime
	 *
	 * @param bool $asMap
	 * @return array
	 */
	public function getTypes($asMap = true): array
	{
		$types = $this->types + $this->aliases;
		if ($asMap)
			return $types;
		return array_keys($types);
	}

	public function typeOf($type)
	{
		if (empty($type))
			return false;
		$type = strtolower(trim($type, '.'));
		// 先检查是否在extension中有定义
		if (isset($this->extensions[$type]))
			$type = $this->extensions[$type];
		elseif (isset($this->aliases[$type]))
			$type = $this->aliases[$type];
		return isset($this->types[$type]) ? $type : false;
	}

	public function has($type)
	{
		return $this->typeOf($type) !== false;
	}

	public function remove($type)
	{
		$type = $this->typeOf($type);
		if ($type === false)
			return $this;
		unset($this->types[$type]);
		return $this;
	}

	public function getExtensions()
	{
		return $this->extensions;
	}

	public function setExtension($mime, $ext = null)
	{
		$total = 0;
		$type = gettype($mime);
		if ($type === KE_STR) {
			$mime = $this->typeOf($mime);
			if ($mime === false || empty($ext))
				return $this;
			$type = gettype($ext);
			if ($type === KE_STR) {
				if (strpos($ext, ',') !== false) {
					$type = KE_ARY;
					$ext = explode(',', $ext);
				}
				else {
					$type = KE_ARY;
					$ext = [$ext];
				}
			}
			if ($type === KE_ARY) {
				foreach ($ext as $item) {
					$item = strtolower(trim($item, '. '));
					if (empty($item) || isset($this->extensions[$item]))
						continue;
					$this->extensions[$item] = $mime;
					$total++;
				}
			}
		}
		elseif ($type === KE_ARY) {
			foreach ($mime as $key => $value) {
				$total += $this->setExtension($key, $value);
			}
		}
		return $this;
	}

	public function makeContentType(string $type): string
	{
		$type = $this->typeOf($type);
		if ($type === false)
			return '';
		$result = $type;
		$setting = $this->types[$type];
		if ($setting[0] === self::APP_ENC)
			$result .= '; charset=' . KE_APP_ENCODING;
		elseif ($setting[0] === self::SPEC_ENC && isset($setting[1]))
			$result .= '; charset=' . $setting[1];
		return $result;
	}

	public static function detectFile(string $file, $return = self::FILE_MIME)
	{
		if (!extension_loaded('fileinfo'))
			return false;
		if (is_file($file) && is_readable($file)) {
			$finfo = finfo_open(FILEINFO_MIME);
			$info = finfo_file($finfo, $file);
			$type = null;
			$encoding = null;
			if (strpos($info, '; ')) {
				list($type, $encoding) = explode('; ', $info);
				if ($return > self::FILE_MIME && !empty($encoding) && strpos($encoding, '=')) {
					list(, $encoding) = explode('=', $encoding);
				}
			} else {
				$type = $info;
			}
			finfo_close($finfo);
			if ($return === self::FILE_MIME)
				return $type;
			elseif ($return === self::FILE_ENC)
				return $encoding;
			else
				return [$type, $encoding];
		}
		return false;
	}
}