<?php
namespace OT\Builder\Driver;

class SortBuilder extends \OT\Builder\Builder {

    private $_savePostUrl;

    public function savePostUrl($url) {
        $this->_savePostUrl = $url;
    }

    public function display($template) {
        //编译按钮的属性
        foreach($this->_buttonList as &$e) {
            $e['attr'] = $this->compileHtmlAttr($e['attr']);
        }
        unset($e);

        //显示页面
        $this->assign('title', $this->_title);
        $this->assign('list', $this->_data);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('savePostUrl', $this->_savePostUrl);
        parent::display($template);
    }

    public function doSort($table, $ids) {
        $ids = explode(',', $ids);
        $res = 0;
        foreach ($ids as $key=>$value){
            $res += M($table)->where(array('id'=>$value))->setField('sort', $key+1);
        }
        if(!$res) {
            $this->error('未修改排序或排序错误。');
        } else {
            $this->success('排序成功。', cookie('__SELF__'));
        }
    }
}