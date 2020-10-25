<?php

namespace App\Traits;


use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    protected $cacheKeyType = [
        'smsCode' => [
            'name' => 'smsCode_', // 短信验证码 _[18313852226]
            'expiry_time' => 3600 * 5 // 过期时间[秒]，0 为永久有效
        ],
    ];

    protected function cachePut($cacheKeyType, $key, $value, $expireAt)
    {
        $cacheKey = $this->cacheKeyType[$cacheKeyType] . $key;
        if ($expireAt) {
            Cache::put($cacheKey['name'], $value, $expireAt);
        } else {
            if ($cacheKey['expiry_time'] > 0) {
                Cache::put($cacheKey['name'], $value, $cacheKey['expiry_time']);
            } else {
                Cache::put($cacheKey['name'], $value);
            }
        }

        return $cacheKey;
    }

    protected function cacheGet($cacheKey)
    {
        return Cache::get($cacheKey);
    }

}
