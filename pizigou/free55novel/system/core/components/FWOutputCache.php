<?php
/**
 * Class FWOutputCache
 */
class FWOutputCache extends COutputCache {

    public $dependCacheKey = "";

    /**
     * @param $obj FWOutputCache
     * @return null|mixed
     */
    public static function getExpression($obj)
    {
        if ($obj->dependCacheKey != "") {
            $key = "__FWOutputCache" . $obj->dependCacheKey;

            $cache = Yii::app()->getComponent($obj->cacheID);

            if ($cache) {
                return $cache->get($key);
            }
        }

        return null;
    }
}