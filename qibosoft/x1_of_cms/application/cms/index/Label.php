<?php
namespace app\cms\index;

use app\common\controller\index\Label AS _Label;

//标签设置
class Label extends _Label
{
    
    /**
     * 通用标签设置
     * {@inheritDoc}
     * @see \app\common\controller\index\Label::tag_set()
     */
    public function tag_set(){
        return parent::tag_set();
    }
    
    /**
     * 内容页设置标签模板
     * {@inheritDoc}
     * @see \app\common\controller\index\Label::showpage_set()
     */
    public function showpage_set(){
        return parent::showpage_set();
    }
    
    /**
     *  列表页标签设置
     * {@inheritDoc}
     * @see \app\common\controller\index\Label::listpage_set()
     */
    public function listpage_set(){
        return parent::listpage_set();
    }

    
}













