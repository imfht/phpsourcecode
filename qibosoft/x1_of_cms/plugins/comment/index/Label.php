<?php
namespace plugins\comment\index;
use plugins\comment\model\Content AS contentModel;
use app\common\controller\IndexBase;
use app\common\traits\LabelEdit;

class Label extends IndexBase
{
    use LabelEdit;
    protected $tab_ext ;
    protected $form_items;

    
    protected function _initialize()
    {
        parent::_initialize();
    }
    
    private function get_cache_tpl(){
        $_array = cache('tags_comment_tpl_'.input('pagename'));
        $_array && $code =trim($_array[input('name')]);        
        return $code;
    }
    
    /**
     * 评论标签设置
     */
    public function set(){
        if($this->request->isPost()){
            $this->setTag_value("@");
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        $info = $this->getTagInfo();
        
        if(empty($info) || empty($info['view_tpl'])){        
            //$info['view_tpl'] = $this->get_cache_tpl();
        }
        $cfg = unserialize($info['cfg']);
        $cfg['rows'] || $cfg['rows']=10;
        $cfg['order'] || $cfg['order']='id';
        $cfg['by'] || $cfg['by']='desc';
        $cfg['status'] = intval($cfg['status']);
        $this->form_items = [
                ['hidden','type','showpage_set_'.config('system_dirname')],
                ['text','rows','每页显示几条评论','',$cfg['rows']],
                ['radio','order','按什么排序','',['id'=>'发布日期','list'=>'可控排序','reply'=>'回复数','agree'=>'点赞数'],$cfg['order']],
                ['radio','by','排序方式','',['desc'=>'降序','asc'=>'升序'],$cfg['by']],
                ['radio','status','范围限制','',['不限','已审核的'],$cfg['status']],
                ['textarea','view_tpl','模板代码','',$info['view_tpl']],
                ['button', 'choose_style', [
                        'title' => '点击选择模板',
                        'icon' => 'fa fa-plus-circle',
                        'href'=>url('index/label/choose_style',['type'=>'comment','tpl_cache'=>'tags_comment_tpl_'.input('pagename'),'name'=>input('name')]),
                        //'data-url'=>url('choose_style',['type'=>'images']),
                        'class'=>'form-btn btn btn-primary pop',
                ],
                        'a'
                ],
        ];
        $this->tab_ext['page_title']='评论设置';
        return $this->editContent(unserialize($info['cfg']));
    }
	
}
