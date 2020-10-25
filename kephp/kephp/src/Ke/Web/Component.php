<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Web;


use Ke\DirectoryRegistry;

class Component extends DirectoryRegistry
{

	const VIEW = 'View';

	const WIDGET = 'Widget';

	const LAYOUT = 'Layout';

	protected $defaultScope = self::WIDGET;

	protected $extension = 'phtml';

	protected $scopeRewrites = [
		'Layout' => 'layout',
	];

	protected $scopeAliases = [
		'layout' => self::LAYOUT,
		'view'   => self::VIEW,
		'widget' => self::WIDGET,
		'*'      => self::WIDGET,
	];

	public function getDefaultScopes(): array
	{
		return [self::WIDGET, self::LAYOUT];
	}
}