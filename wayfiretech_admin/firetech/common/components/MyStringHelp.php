<?php
namespace common\components;//公共方法类库

use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class MyStringHelp extends BaseObject
{
    public $_string;

    public function setString()
    {
        return $this->_string;
    }

    public function getString($string)
    {
        $this->_string =$string;
    }

    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        $this->init();
    }

    public function init()
    {
        parent::init();
    }



    /**
    * 生成随机字符串，并排除特殊字符.
    *
    * @param int|null post
    * @return string
    * @throws NotFoundHttpException
    */

    public function CreateString($str='')
    {
        return md5($this->_string);
    }
}
