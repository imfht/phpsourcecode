<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'name',
        'value',
        'description',
        'status',
    ];

    public $timestamps = false;

    /**
     * 获取全部配置或者某个配置.
     * @param null $key 空获取全部配置，有值获取值
     * @return object
     * @throws \Exception
     */
    public static function getAllConfig($key = null)
    {
        $returnConfig = [];
        $cachedConfig = cache('CONFIG');

        if (empty($cachedConfig)) {
            $config = self::all();
            foreach ($config as $value) {
                $returnConfig[$value->name] = $value->value;
            }
            cache(['CONFIG' => json_encode($returnConfig)], env('CACHE_DURATION'));
        } else {
            $returnConfig = json_decode($cachedConfig, true);
        }

        if (empty($key)) {
            return (object) $returnConfig;
        } elseif ($key === 'SITE_DEFAULT_IMAGE') {
            return env('SITE_DEFAULT_IMAGE');
        } else {
            return isset($returnConfig[$key]) ? $returnConfig[$key] : null;
        }
    }

    /**
     * 设置站点标题.
     * @param $siteTitle
     * @return Model
     * @throws \Exception
     */
    public static function setSiteTitle($siteTitle)
    {
        $config = json_decode(cache('CONFIG'));
        if (empty($config)) {
            $config = (object) [];
        }
        $config->SITE_TITLE = $siteTitle;
        cache(['CONFIG' => json_encode($config)], env('CACHE_DURATION'));

        return self::updateOrCreate([
            'name' => 'SITE_TITLE',
            'value' => $siteTitle,
        ]);
    }

    /**
     * 设置站点副标题.
     * @param $siteSubTitle
     * @return mixed
     * @throws \Exception
     */
    public static function setSiteSubTitle($siteSubTitle)
    {
        $config = json_decode(cache('CONFIG'));
        if (empty($config)) {
            $config = (object) [];
        }
        $config->SITE_SUB_TITLE = $siteSubTitle;
        cache(['CONFIG' => json_encode($config)], env('CACHE_DURATION'));

        return self::updateOrCreate([
            'name' => 'SITE_SUB_TITLE',
            'value' => $siteSubTitle,
        ]);
    }

    /**
     * 设置站点关键字.
     * @param $siteKeywords
     * @return mixed
     * @throws \Exception
     */
    public static function setSiteKeywords($siteKeywords)
    {
        $config = json_decode(cache('CONFIG'));
        if (empty($config)) {
            $config = (object) [];
        }
        $config->SITE_KEYWORDS = $siteKeywords;
        cache(['CONFIG' => json_encode($config)], env('CACHE_DURATION'));

        return self::updateOrCreate([
            'name' => 'SITE_KEYWORDS',
            'value' => $siteKeywords,
        ]);
    }

    /**
     * 设置站点描述.
     * @param $siteDescription
     * @return mixed
     * @throws \Exception
     */
    public static function setSiteDescription($siteDescription)
    {
        $config = json_decode(cache('CONFIG'));
        if (empty($config)) {
            $config = (object) [];
        }
        $config->SITE_DESCRIPTION = $siteDescription;
        cache(['CONFIG' => json_encode($config)], env('CACHE_DURATION'));

        return self::updateOrCreate([
            'name' => 'SITE_DESCRIPTION',
            'value' => $siteDescription,
        ]);
    }

    /**
     * 设置站点默认图片.
     * @param $siteDefaultImage
     * @return mixed
     * @throws \Exception
     */
    public static function setSiteDefaultImage($siteDefaultImage)
    {
        $config = json_decode(cache('CONFIG'));
        if (empty($config)) {
            $config = (object) [];
        }
        $config->SITE_DEFAULT_IMAGE = $siteDefaultImage;
        cache(['CONFIG' => json_encode($config)], env('CACHE_DURATION'));

        return self::updateOrCreate([
            'name' => 'SITE_DEFAULT_IMAGE',
            'value' => $siteDefaultImage,
        ]);
    }
}
