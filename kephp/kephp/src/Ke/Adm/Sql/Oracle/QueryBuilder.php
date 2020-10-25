<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Sql\Oracle;


class QueryBuilder extends \Ke\Adm\Sql\QueryBuilder
{

	public function buildLimitOffset(int $limit = 0, int $offset = -1, string & $sql = null)
	{
		if ($offset <= 0 && $limit > 0) {
			$sql = 'SELECT * FROM (' . $sql . ') WHERE rownum < ' . ($limit + 1);
		}
		elseif ($offset > 0) {
			$sql = 'SELECT * FROM (SELECT a.*, rownum rnum FROM (' .
				$sql .
				') a WHERE rownum < ' . ($limit + 1 + $offset) . ') WHERE rnum > ' . ($offset);
		}
	}
}