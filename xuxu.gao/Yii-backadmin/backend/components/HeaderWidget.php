<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 14:20
 */

namespace backend\components;


use yii\base\Widget;
use Yii;
class HeaderWidget extends Widget{


    public function run(){



        return $this->render('@app/views/layouts/mylayouts/headers');
    }

}