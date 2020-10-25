<?php
/**
 * Created by PhpStorm.
 * User: Yixiao Chen
 * Date: 2015/4/30 0030
 * Time: 下午 3:39
 */

namespace Addons\Favorites\Model;
use Think\Model;

class FavoritesModel extends Model
{
    protected $tableName = 'favorites';

    /**取得赞的数量
     * @param $appname
     * @param $table
     * @param $row
     * @return mixed
     */
    public function getFavoritesCount($appname, $table, $row)
    {
        return $this->where(array('appname' => $appname, 'table' => $table, 'row' => $row))->cache($this->getCacheTag($appname, $table, $row))->count();
    }

    public function getFavoritesedUser($app, $table, $row, $user_fields=array('nickname','space_url','avatar128'),$num=10)
    {
        $favoritesed = $this->where(array('appname' => $app, 'table' => $table, 'row' => $row))->findPage($num);
        foreach ($favoritesed['data'] as &$v) {
            $v['user'] = query_user($user_fields, $v['uid']);
        }
        unset($v);
        return $favoritesed;
    }

    /**清除赞缓存
     * @param $appname
     * @param $table
     * @param $row
     */
    public function clearCache($appname, $table, $row)
    {
        S($this->getCacheTag($appname, $table, $row), null);
    }

    private function getCacheTag($appname, $table, $row)
    {
        return 'favorites_count_' . $appname . '_' . $table . '_' . $row;
    }
}