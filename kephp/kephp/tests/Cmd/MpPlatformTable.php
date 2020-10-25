<?php


namespace tests\Cmd;

use Ke\Adm\Model;

/**
 * Class MpPlatform
 * tableName 'mp_platform'
 *
 * // class properties
 * @property int    $id          主键
 * @property string $access_name 系统访问名
 * @property string $name        平台名称
 * @property int    $parent_id   所属平台
 * @property int    $created_at  创建时间
 * @property int    $updated_at  更新时间
 * // class properties
 */
class MpPlatformTable extends Model
{

	protected static $dbSource = null;

	protected static $pk = 'id';

	protected static $pkAutoInc = true;

	protected static $tableName = 'mp_platform';

	protected static $columns = [];

	public static function dbColumns()
	{
		// database columns
		return [
			'id'          => ['label' => '主键','int' => 1,'pk' => 1,'autoInc' => 1,],
			'access_name' => ['label' => '系统访问名','max' => 32,'default' => '',],
			'name'        => ['label' => '平台名称','max' => 32,'default' => '',],
			'parent_id'   => ['label' => '所属平台','int' => 1,'default' => 0,],
			'created_at'  => ['label' => '创建时间','timestamp' => 1,self::ON_CREATE => 'now',],
			'updated_at'  => ['label' => '更新时间','timestamp' => 1,self::ON_UPDATE => 'now',],
		];
		// database columns
	}
}