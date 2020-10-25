<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfigMany extends Model
{
    protected $fillable = [
        'group',
        'group_name',
        'config',
        'config_name',
        'config_value',
        'status',
        'description',
    ];

    /**
     * @param null $key 空获取全部配置，有值获取值
     * @return mixed|object
     * @throws \Exception
     */
    public static function getAllConfig($key = null)
    {
        $returnConfig = [];
        $cachedConfig = json_decode(cache('CONFIG_MANY'), true);

        if (empty($cachedConfig)) {
            $configMany = self::all();
            foreach ($configMany as $value) {
                if ($value) {
                    $returnConfig[$value->group][$value->config] = $value->config_value;
                }
            }
            cache(['CONFIG_MANY' => json_encode($returnConfig)], env('CACHE_DURATION'));
        } else {
            $returnConfig = $cachedConfig;
        }

        if (empty($key)) {
            return (object) $returnConfig;
        } else {
            return $returnConfig[$key];
        }
    }
}
