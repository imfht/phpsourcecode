<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-18
 * Time: 下午8:09
 */
class ProjectTaskStatus extends Eloquent
{
    public static function getIdByName($statusName)
    {
        $targetStatus = static::where('name', $statusName)->first();
        if( $targetStatus ){
            return $targetStatus['id'];
        } else {
            return null;
        }

    }

    public static function getIdByNameOrFail($statusName)
    {
        return static::where('name', $statusName)->firstOrFail()['id'];
    }

    protected $table = 'projectTaskStatus';

    protected $guarded = ['id'];
}