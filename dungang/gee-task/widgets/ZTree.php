<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\assets\ZTreeAsset;

class ZTree extends Widget
{
    public $parentUserChildIds = true;
    /**
     * 默认不展开节点
     *
     * @var boolean
     */
    public $expandAll = false;

    /**
     * 节点的url
     *
     * @var string|array|null
     */
    public $url = null;

    public $urlTarget = '_blank';

    /**
     * url参数的名称
     *
     * @var string
     */
    public $urlParamName = 'id';

    /**
     * url参数的值是否使用子节点的集合
     *
     * @var boolean
     */
    public $urlParamValueUseChild = false;

    /**
     * ZTree的setting
     *
     * @var array
     */
    public $settings;

    /**
     * 节点
     *
     * @var array
     */
    public $nodes = [];

    public function run()
    {
        ZTreeAsset::register($this->view);
        $id = $this->getId();
        $settings = Json::encode($this->settings ?: new \stdClass());
        if ($this->url || $this->expandAll) {
            $this->refactor($this->nodes, $this->url, $this->expandAll);
        }
        $nodes = Json::encode($this->nodes);
        $this->view->registerJs("$.fn.zTree.init($('#{$id}'),$settings,$nodes);");
    }

    protected function refactor(&$nodes, $url = null, $open = false)
    {
        $idName = $this->urlParamName;
        foreach ($nodes as &$child) {
            $hasChild = isset($child['children']) && !empty($child['children']);
            if ($url && empty($child['url'])) {
                $ids = $this->parentUserChildIds ? $this->getChildIds($child, $hasChild) : null;
                if (is_array($url)) {
                    if (is_array($ids)) {
                        foreach ($ids as $id) {
                            $url[$this->urlParamName][] = $id;
                        }
                    } else if ($child[$idName]) {
                        $url[$this->urlParamName] = $child[$idName];
                    }
                    $child['url'] = Url::to($url);
                } else {
                    if (is_array($ids)) {
                        foreach ($ids as $id) {
                            $url .= '&' . $this->urlParamName . '[]=' . $id;
                        }
                    } else if ($child[$idName]) {
                        $url .= '&' . $this->urlParamName . '=' . $child[$idName];
                    }
                }
            }
            $child['open'] = $open;
            $child['target'] = $this->urlTarget;
            if ($hasChild) {
                $this->refactor($child['children'], $url, $open);
            }
        }
    }

    /**
     * 获取子节点的Id
     *
     * @param array $node
     * @param array $hasChild
     * @return int|string|array
     */
    protected function getChildIds($node, $hasChild)
    {
        if ($hasChild && $this->urlParamValueUseChild) {
            return ArrayHelper::getColumn($node['children'], $idName);
        }
        return $node[$idName];
    }
}
