<?php
/**
 * @Author: Wang chunsheng
 * @Date:   2020-04-29 02:27:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-07 11:20:27
 */

namespace common\widgets\layout;

use diandi\admin\components\Helper;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class Left extends Widget
{
    public $titles = ['列表', '添加', '详情','更新'];

    public $urls = ['index', 'create', 'view','update'];

    public $options = [];

    public $items;
    /**
     * @var string
     */
    public $theme = 'default';

    public $depends = [
        'common\widgets\adminlte\VuemainAsset',
    ];
    
    /**
     * 默认数据.
     *
     * @var array
     */
    public $defaultData = [];

    public function init()
    {
        parent::init();
  
        if (count($this->titles) != count($this->urls)) {
            echo 'tab切换参数配置错误';
            die;
        }
        foreach ($this->titles as $key => $title) {
              // 校验权限
            $isAuth = Helper::checkRoute($this->urls[$key]);
            if(!$isAuth){
                continue;
            }
            
            if (Yii::$app->controller->action->id != 'view' && $this->urls[$key] == 'view') {
                continue;
            }
            if (Yii::$app->controller->action->id != 'update' && $this->urls[$key] == 'update') {
                continue;
            }
            $option = http_build_query($this->options);
            $items[] = [
                'label' => $title,
                'active' => Yii::$app->controller->action->id == $this->urls[$key],
                'url' => Url::to([$this->urls[$key].'?'.$option]),
            ];
        }
        $this->items = json_encode($items);
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->theme, [
            'items' => $this->items,
        ]);
    }
}
