<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-09-24 17:36:20
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-24 18:47:03
 */

namespace common\components;

use Yii;
use yii\base\BaseObject;

class Job extends BaseObject implements \yii\queue\JobInterface
{
    public $bloc_id;
    
    public $store_id;
    
    public $addons;
    
    public function init()
    {
        parent::init();
        Yii::$app->params['bloc_id'] = $this->bloc_id;
        Yii::$app->params['store_id'] = $this->store_id;
        Yii::$app->service->commonGlobalsService->initId($this->bloc_id, $this->store_id,$this->addons);
    }
    
     /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue){
     
    }
}