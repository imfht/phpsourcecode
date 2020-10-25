<?php

namespace App\Common;

/**
 * 数组转换为树结构数组类.
 */
class Tree
{
    public $pk; //主键字段名
    public $parentKey; //上级id字段名
    public $childrenKey; //用来存储子分类的数组key名
    public $nameKey = 'menuName'; //名称key
    public $iconClassKey = 'iconClass';//样式
    public $optionSelectId;//option设置选中值
    public $jsTreeDisabledIds = [];//jsTree禁用ID集
    public $jsTreeLiAttrField = [];//jsTree li标签属性字段
    public $jsTreeAAttrField = [];//jsTree a标签属性字段
    public $jsTreeIsOpenField = 'isOpen';//jsTree 设置节点是否展开的字段
    private $OriginalList;
    private $treeList;

    public function __construct($pk='id', $parentKey='pid', $childrenKey='children')
    {
        if (!empty($pk) && !empty($parentKey) && !empty($childrenKey)) {
            $this->pk         =$pk;
            $this->parentKey  =$parentKey;
            $this->childrenKey=$childrenKey;
        } else {
            return false;
        }
    }

    //载入初始数组
    public function load($data)
    {
        if (is_array($data)) {
            $this->OriginalList=$data;
        }
    }

    /**
     * 生成嵌套格式的树形数组
     * array(..."children"=>array(..."children"=>array(...))).
     *
     * @param mixed $root
     */
    public function deepTree($root=0)
    {
        if (!$this->OriginalList) {
            return false;
        }
        if ($this->treeList){
            return true;
        }
        $OriginalList=$this->OriginalList;
        $tree        =[]; //最终数组
        $refer       =[]; //存储主键与数组单元的引用关系
        //遍历
        foreach ($OriginalList as $k=>$v) {
            if (!isset($v[$this->pk]) || !isset($v[$this->parentKey]) || isset($v[$this->childrenKey])) {
                unset($OriginalList[$k]);
                continue;
            }
            $refer[$v[$this->pk]]=&$OriginalList[$k]; //为每个数组成员建立引用关系
        }
        //遍历2
        foreach ($OriginalList as $k=>$v) {
            if ($v[$this->parentKey] == $root) {//根分类直接添加引用到tree中
                $tree[]=&$OriginalList[$k];
            } else {
                if (isset($refer[$v[$this->parentKey]])) {
                    $parent                      =&$refer[$v[$this->parentKey]]; //获取父分类的引用
                    $parent[$this->childrenKey][]=&$OriginalList[$k]; //在父分类的children中再添加一个引用成员
                }
            }
        }
        $this->treeList = $tree;
        return $this->treeList;
    }

    /**
     * 添加前缀字符
     * @param $treeList
     * @param string $prestr
     * @return bool
     */
    private function addNamePre($treeList, $prestr=''){
        if (empty($treeList)){
            return false;
        }
        foreach ($treeList as $k => $v) {
            if ($prestr) {
                if ($v == end($this->treeList)) {
                    $v[$this->nameKey] = $prestr . '└─ ' . $v[$this->nameKey];
                } else {
                    $v[$this->nameKey] = $prestr . '├─ ' . $v[$this->nameKey];
                }
            }

            if ($prestr == '') {
                $prestrForChildren = '    ';
            } else {
                if ($v == end($this->OriginalList)) {
                    $prestrForChildren = $prestr . '    ';
                } else {
                    $prestrForChildren = $prestr . '|    ';
                }
            }
            if (isset($v[$this->childrenKey]) && $v[$this->childrenKey]) {
                $v[$this->childrenKey] = $this->addNamePre($v[$this->childrenKey], $prestrForChildren);
            }

            $treeList[$k] = $v;
        }
        return $treeList;
    }

    /**
     * 拼装select option代码
     * @param $arr 树形数组
     * @param $depth 显示深度
     * @param int $recursionCount 递归次数
     * @param string $ancestorIds 祖先ID集
     * @return string
     */
    private function makeOptions($arr, $depth = 0, $recursionCount=0, $ancestorIds='')
    {
        $recursionCount++;
        $str = '';
        if ($arr){
            foreach ($arr as $v) {
                $str .= "<option value='" . $v[$this->pk] . "' data-depth='{$recursionCount}' data-ancestorIds='" . ltrim($ancestorIds, ',') . "'".($this->optionSelectId == $v[$this->pk] ? ' selected' : '').">" . $v[$this->nameKey] . "</option>\r\n";
                if ($v[$this->parentKey] == 0) {
                    $recursionCount = 1;
                }
                if (isset($v[$this->childrenKey]) && $v[$this->childrenKey] && ($depth == 0 || $recursionCount < $depth )) {
                    $str .= $this->makeOptions($v[$this->childrenKey], $depth, $recursionCount, $ancestorIds . ',' . $v[$this->pk]);
                }
            }
        }

        return $str;
    }
    /**
     * 构建树形select的option的html
     * @param $arr
     * @param int $depth，当$depth为0的时候表示不限制深度
     *
     * @return string
     */
    public function buildOptions($depth=0)
    {
        //组装成
        $this->deepTree();
        //添加名称前缀
        $this->treeList = $this->addNamePre($this->treeList);
        //组装option字符
        $optionHtml = $this->makeOptions($this->treeList, $depth);
        //$optionHtml = '';
        return $optionHtml;
    }

    /**
     * 拼装可嵌套列表html
     * @param $treeList
     * @return string
     */
    private function makeNestableTree($treeList, $addhtml = ''){
        $html = '<ol class="dd-list">';
        if ($treeList) {
            foreach ($treeList as $menuData) {
                $html .= '<li class="dd-item dd-nodrag" data-id="' . $menuData[$this->pk] . '">';
                $html .= '<div class="dd-handle">';
                $html .= '<span class="label label-info">';
                if (isset($menuData[$this->iconClassKey]) && $menuData[$this->iconClassKey]) {
                    $html .= '<i class="' . $menuData[$this->iconClassKey] . '"></i>';
                }
                $html .= '</span>' . $menuData[$this->nameKey];
                $html .= $addhtml;
                $html .= '</div>';
                if (isset($menuData[$this->childrenKey]) && $menuData[$this->childrenKey]) {
                    $html .= $this->makeNestableTree($menuData[$this->childrenKey], $addhtml);
                }
            }
        }
        $html .= '</ol>';
        return $html;
    }
    /**
     * 构建嵌套列表html
     * @param $treeList
     * @return string
     */
    public function buildNestableTree($addHtml = '')
    {
        //组装成
        $this->deepTree();
        $html = $this->makeNestableTree($this->treeList, $addHtml);
        return $html;
    }

    /**
     * 格式化为jsTree需要json格式
     * @param array $secIds
     * @param bool $allOpen
     * @param int $root
     */
    public function makeJsTreeFormat($secIds = [], $root = 0)
    {
        if ($this->OriginalList)
        {
            $newList = [];
            foreach ($this->OriginalList as $value){
                $id = $value[$this->pk] ?? 0;
                $data = [
                    'id' => $id,
                    'text' => $value[$this->nameKey] ?? '',
                    'icon' => $value[$this->iconClassKey] ?? '',
                    'parentId' => $value[$this->parentKey] ?? 0,
                    'state' => [
                        'opened' => (bool)($value[$this->jsTreeIsOpenField] ?? 1),
                        'disabled' => in_array($id, $this->jsTreeDisabledIds) ? true : false,
                        'selected' => in_array($id, $secIds) ? true : false,
                    ],
                ];
                //li标签属性字段
                if ($this->jsTreeLiAttrField){
                    foreach ($this->jsTreeLiAttrField as $attr){
                        isset($value[$attr]) && $data['li_attr'][$attr] = $value[$attr];
                    }
                }
                //a标签属性字段
                if ($this->jsTreeAAttrField){
                    foreach ($this->jsTreeAAttrField as $attr){
                        isset($value[$attr]) && $data['a_attr'][$attr] = $value[$attr];
                    }
                }
                $newList[] = $data;
            }
            $this->OriginalList = $newList;
        }
        $this->pk = 'id';
        $this->parentKey = 'parentId';
        $this->childrenKey = 'children';
        $this->nameKey = 'text';
        return $this->deepTree($root);
    }
}
