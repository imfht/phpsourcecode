<?php
/**
 * @Author: Wang chunsheng
 * @Date:   2020-04-29 02:27:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-01 11:05:33
 */

namespace common\widgets\tab;

use diandi\admin\components\Helper;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class Tab extends Widget
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
        global $_GPC;
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
            $active = false;
            $option = '';
            if($this->options){
                if(count($this->options)==1){
                    $option = http_build_query($this->options);
                }elseif(!empty($this->options[$key])){
                    $option = http_build_query($this->options[$key]);
                }
                $active = (Yii::$app->controller->action->id == $this->urls[$key] && http_build_query($_GPC) == $option)?true:false;

            }else{
                $active = (Yii::$app->controller->action->id == $this->urls[$key])?true:false;

            }
           

            
            $items[] = [
                'label' => $title,
                'active' => $active,
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
