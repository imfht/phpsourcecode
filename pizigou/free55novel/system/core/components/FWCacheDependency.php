<?php

/**
 * 实现依赖 cache 的 ICacheDependency
 * 原理是通过小 cache 管理大 cache
 * Class FWCacheDependency
 */
class FWCacheDependency extends CCacheDependency {

    public $dependCacheKey = null;

    public $cacheId = 'cache';

    /**
     * @return mixed|null
     */
    public function generateDependentData()
    {
        if ($this->dependCacheKey) {

            $cache = Yii::app()->getComponent($this->cacheId);

            $key = "__FWCacheDependency" . $this->dependCacheKey;

            if ($cache) {
                return $cache->get($key);
            }
        }

        return null;
    }

}