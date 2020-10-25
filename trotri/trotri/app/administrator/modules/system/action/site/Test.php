<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\site;

use library\actions;
use tfc\saf\DbProxy;

/**
 * Test class file
 * 测试页
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Test.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.system.action.site
 * @since 1.0
 */
class Test extends actions\View
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$dbProxy = new DbProxy('trotri');

		$sql = 'SELECT `experience` FROM `tr_members` WHERE `member_id1` = ?';
		$opBefore = $dbProxy->fetchColumn($sql, 1, 0);
		var_dump($opBefore);
		
		exit;
		$commands = array(
			array(
				'sql' => 'UPDATE tr_member_portal SET valid_mail = \'y\' WHERE member_id = 1',			
			),
			array(
				'sql' => 'UPDATE tr_member_portal SET valid_mail = \'y\' WHERE member_id = 2',
			),
			array(),
			array(
				'sql' => 'UPDATE tr_member_portal SET valid_mail = ? WHERE member_id = ?',
				'params' => array('valid_mail' => 'y', 'member_id' => 3),
			),
			array(
				'sql' => 'UPDATE tr_member_portal SET valid_mail = ? WHERE member_id = ?',
				'params' => array('valid_mail' => 'y', 'member_id' => 4),
			),
			array(
				'sql' => 'UPDATE tr_member_portal SET valid_mail = \'y\' WHERE member_id = 5',
			),
		);

		$ret = $dbProxy->doTransaction($commands);
		var_dump($ret);

		$ret = $dbProxy->query('UPDATE tr_member_portal SET valid_mail = \'y\' WHERE member_id = 6');
		var_dump($ret);
	}
}
