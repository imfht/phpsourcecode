<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Adapter;


interface CacheAdapter
{

	const DEFAULT_COLON = '.';

	public function __construct(string $source, array $config = null);

	public function configure(array $config);

	public function getConfiguration();

	public function exists($key);

	public function set($key, $data, $expire = 0);

	public function get($key);

	public function delete($key);

	public function replace($key, $data, $expire = 0);

	public function increment($key, $value = 1);

	public function decrement($key, $value = 1);

	public function flush();
}