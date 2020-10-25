<?php
/**
 * @Author: Wang chunsheng
 * @Date:   2020-04-29 02:27:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-03 09:33:15
 */

namespace common\widgets\adminlte;

use yii\base\Widget;

class Icon extends Widget
{
    public $options = [];

    public $model;

    public $attribute;

    public $theme = '@common/widgets/adminlte/views/icon/icon.php';

    public $items;
    
    public $type = 'icon';

    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->theme, [
            'items' => $this->items,
            'type' => $this->type,
            'model' => $this->model,
            'attribute' => $this->attribute,
        ]);
    }
}
