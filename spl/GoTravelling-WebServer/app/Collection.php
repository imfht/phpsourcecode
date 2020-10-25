<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-23
 * Time: 下午9:27
 */
namespace App;


class Collection extends \Eloquent
{
    /**
     * 将内部数据的表现形式，转变为符合数据交互协定的形式
     *
     * @param $mixed array|object 可以传入单一对象，或对象所组成的数组
     * @return array
     */
    public static function changeDataToResp($mixed)
    {
        $single = false;

        if( is_object($mixed) ){
            $mixed = $mixed->toArray();
        }

        if( is_array($mixed) && (array_keys( $mixed ) !== range(0, count($mixed) - 1)) ) {
            $mixed = [$mixed];
            $single = true;
        }

        $resp = [];
        foreach($mixed as $item){
            if ( isset($item['loc']) ) {
                $longitude = $item['loc']['coordinates'][0];
                $latitude = $item['loc']['coordinates'][1];

                unset($item['loc']);
                $item['longitude'] = $longitude;
                $item['latitude'] = $latitude;
            }

            array_push($resp, $item);
        }

        if( $single ){
            return head($resp);
        } else{
            return $resp;
        }
    }

    protected $table = 'collections';

    protected $guarded = ['_id'];
}