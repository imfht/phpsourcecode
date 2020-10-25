<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-09 10:07:49
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-09 10:08:30
 */
 

namespace common\components;

use diandi\admin\components\Helper;
use Yii;
use yii\helpers\Html;

//公共方法类库

class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = '{view} {update} {delete}';

    /**
     * Initializes the default button rendering callback for single button.
     *
     * @param string $name              Button name as it's written in template
     * @param string $iconName          The part of Bootstrap glyphicon class that makes it unique
     * @param array  $additionalOptions Array of additional options
     *
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        $template = Helper::filterActionColumn($this->template);
        if (!isset($this->buttons[$name]) && strpos($template, '{'.$name.'}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'class' => 'btn btn-default btn-ac',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => " glyphicon glyphicon-$iconName"]);

                return Html::a($icon, $url, $options);
            };
        }
    }
}
