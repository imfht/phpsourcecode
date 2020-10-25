<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/15
 * Time: 上午11:05
 */

namespace App\Models;
use Illuminate\Support\Facades\Cache;

/**
 * 地区
 * Class Areas
 * @package App\Models
 */
class Areas extends BaseModels
{
    protected $table = 'areas';
    protected $guarded = ['id'];

    /**
     * 根据id获取名称
     * @param string $id
     * @return string
     */
    public static function getAreaName($id = '') {
        $name = '';
        if ($id) {
            $area = self::find($id);
            if (isset($area['name'])) {
                $name =  $area['name'];
            }
        }
        return $name;
    }

    /**
     * 根据名称和上级获取id
     * @param string $id
     * @return string
     */
    public static function getAreaId($name, $parent_id = 0) {
        $id = 0;
        if ($name) {
            $area = self::where([['name', $name], ['parent_id', $parent_id]])->first();
            if ($area['id']) {
                $id = $area['id'];
            }
        }
        return $id;
    }

    /**
     * 根据parent_id获取下级
     * @param string $parent_id 上级id
     * @return string
     */
    public static function getArea($parent_id = 0) {
        $area_list = Cache::remember('area_select_' . $parent_id, 120, function() use ($parent_id) {
            $area = array();
            $area_res = self::where('parent_id', $parent_id)->select('id', 'name')->get();
            if (!$area_res->isEmpty()) {
                $area = $area_res->toArray();
            }
            return $area;
        });
        return $area_list;
    }
}
