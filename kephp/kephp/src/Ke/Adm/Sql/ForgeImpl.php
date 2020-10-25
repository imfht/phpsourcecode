<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Sql;


use Ke\Adm\Model;

interface ForgeImpl
{


	const ON_CREATE = '#ON_CREATE#';
	const ON_UPDATE = '#ON_UPDATE#';
	const ON_DELETE = '#ON_DELETE#';
	const ON_SAVE   = '#ON_SAVE#';

	const PROCESS_MAPS = [
		Model::ON_CREATE => self::ON_CREATE,
		Model::ON_UPDATE => self::ON_UPDATE,
		Model::ON_DELETE => self::ON_DELETE,
		Model::ON_SAVE   => self::ON_SAVE,
	];

	const PROCESS_EXPORT_MAPS = [
		'\'#ON_CREATE#\'' => 'self::ON_CREATE',
		'\'#ON_UPDATE#\'' => 'self::ON_UPDATE',
		'\'#ON_DELETE#\'' => 'self::ON_DELETE',
		'\'#ON_SAVE#\''   => 'self::ON_SAVE',
	];

	public function buildTableProps(string $table, array $columns = []): array;

	public function getDbTables(string $db = null): array;
}