<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-26
 * Time: 上午10:41
 */

use Illuminate\Database\Eloquent\Builder;

/**
 * 分页辅助类
 * Class Paginate
 */
class Paginate
{
    /**
     * 在builder实例上进行分页,分页参数暂时采用laravel默认认可的
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Pagination\Paginator
     *        | \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function paginateBuilder(Builder $builder)
    {
        if ( Input::has('per_page') ) {
            return $builder->paginate(Input::get('per_page'));
        }
        return $builder->get();
    }

    /**
     * 手动分页
     * @param array $items
     * @return \Illuminate\Pagination\Paginator
     */
    public static function paginateArray(Array $items){
        //由于laravel分页时per_page结果不符合期望，暂时手动对data进行切片获取per_page数据
        if ( Input::has('per_page') ) {

            $pagination = Paginator::make($items, count($items), Input::get('per_page'))->toArray();
            $pagination['data'] = array_slice($pagination['data'], ($pagination['current_page'] - 1) * $pagination['per_page'], $pagination['per_page']);

            return $pagination;
        }

        return $items;
    }

    /**
     * 根据前端请求决定是否分页
     * @return bool
     */
    protected static function checkPaginate()
    {
        if ( Input::has('per_page') ) {
            return Input::get('per_page');
        }
        return false;
    }
}
