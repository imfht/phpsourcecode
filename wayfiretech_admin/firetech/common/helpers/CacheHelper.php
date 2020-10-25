<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-08-13 00:35:45
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-13 19:30:58
 */
 

namespace common\helpers;

use Yii;
use yii\redis\Cache as RedisCache;

class CacheHelper extends RedisCache
{
    public $duration;

    public function init()
    {
        parent::init();
        $this->duration = Yii::$app->params['cache']['duration'];
    }

    
    public  function set($key, $value, $duration = null, $dependency = null)
    {
        if($duration == null){
            $duration = $this->duration;
        }
        
        return parent::set($key, $value, $duration, $dependency);
    }

  
}
