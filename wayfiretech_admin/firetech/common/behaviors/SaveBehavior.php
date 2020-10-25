<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-15 22:50:42
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-24 09:06:20
 */

namespace common\behaviors;

use diandi\admin\models\Bloc;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * @author Skilly
 */
class SaveBehavior extends Behavior
{
    public $createdAttribute = 'create_time';

    public $updatedAttribute = 'update_time';

    public $storeAttribute = 'store_id';

    public $blocAttribute = 'bloc_id';
    
    public $blocPAttribute = 'bloc_pid';//集团或上级公司

    public $attributes = [];

    private $_map;

    public function init()
    {
        global $_GPC;
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAttribute, $this->updatedAttribute, $this->blocAttribute, $this->storeAttribute, $this->blocPAttribute], //准备数据 在插入之前更新created和updated两个字段
                BaseActiveRecord::EVENT_BEFORE_UPDATE => [$this->updatedAttribute, $this->blocAttribute, $this->storeAttribute, $this->blocPAttribute], // 在更新之前更新updated字段
            ];
        }

        $bloc_id = Yii::$app->service->commonGlobalsService->getBloc_id();
        $store_id = Yii::$app->service->commonGlobalsService->getStore_id();

        // 后台用户使用
        if (!empty($_GPC['bloc_id']) && $_GPC['bloc_id'] != $bloc_id) {
            $bloc_id = $_GPC['bloc_id'];
        }

        $blocPid = Bloc::find()->where(['bloc_id'=>$bloc_id])->select('pid')->one();

        // if (Yii::$app->user->identity->store_id) {
        //     $store_id = Yii::$app->user->identity->store_id;
        // }

        $this->_map = [
            $this->createdAttribute => time(), //在这里你可以随意格式化
            $this->updatedAttribute => time(),
            $this->blocAttribute => $bloc_id,
            $this->storeAttribute => $store_id,
            $this->blocPAttribute => $blocPid['pid'],
        ];
    }

    //@see http://www.yiichina.com/doc/api/2.0/yii-base-behavior#events()-detail
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $this->owner->attributes)) {
                    $this->owner->$attribute = $this->getValue($attribute);
                }
            }
        }
    }

    protected function getValue($attribute)
    {
        return $this->_map[$attribute];
    }
}
