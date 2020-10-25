<?php
namespace app\admin\controller;
use app\common\controller\AdminBase; 
use app\common\model\Chatmod AS ChatmodModel;
use app\common\util\Tabel;
use app\common\util\Form;

class Chatmod extends AdminBase
{
    protected $validate = '';
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new ChatmodModel();
    }
    
    public function index($pcwap='-1')
    {
        $map = [];
        if($pcwap>=0){
            $map = [
                'pcwap'=>$pcwap
            ];            
        }
	    
	    $listdb =  ChatmodModel::where($map)->order('list DESC,id ASC')->column(true);

	    $tab = [
	            //['id','ID','text'],
	            ['icon','图标','icon'],
	            ['name','模块名称','text'],
	            ['keywords','关键字','text'],
	            ['type','使用范围','select',['共用','群聊专用','私聊专用']],
	            ['pcwap','所属终端','select2',['通用模块','wap专用','PC专用','APP专用']],
	            ['list','排序值','text.edit'],
	            ['status','是否启用','switch'],
	            //['target','新窗口打开','yesno'],
	            //['right_button', '操作', 'btn'],
// 	            ['right_button', '操作', 'callback',function($value,$rs){
// 	                if($rs['pid']>0)$value=str_replace('_tag="add"', 'style="display:none;"', $value);
// 	                return $value;
// 	            },'__data__'],
	    ];

	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'添加模块','href'=>url('add',['type'=>$type])])
	    ->addTopButton('delete')	    
	    //->addRightButton('add',['title'=>'添加下级菜单','href'=>url('add',['pid'=>'__id__'])])
	    ->addRightButton('delete')	    
	    ->addRightButton('edit')
	    //->addPageTips('省份管理')
	    //->addOrder('id,list')
	    ->addPageTitle('模块管理')
	    ->addNav( [
	            -1=>['title' => '全部', 'url' =>url('index',['pcwap'=>'-1'])],
	            0=>['title' => '通用模块', 'url' =>url('index',['pcwap'=>'0'])],
	            1=>['title' => 'wap专用', 'url' =>url('index',['pcwap'=>1])],
	            2=>['title' => 'PC专用', 'url' =>url('index',['pcwap'=>2])],
	            3=>['title' => 'APP专用', 'url' =>url('index',['pcwap'=>3])],
	            
	    ],$pcwap);

        return $table::fetchs();
	}
	
	public function add($pid=0,$type=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($data['name']==''){
	            $this->error('模块名称不能为空!');
	        }elseif($data['keywords']==''){
	            $this->error('关键字不能为空!');
	        }
	        
	        if(ChatmodModel::create($data)){
	            cache('chatmod_cfg',null);
	            $this->success('创建成功 ', url('index',['pcwap'=>$data['pcwap']]) );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    if($pid){
	        $info = ChatmodModel::get($pid);
	    }else{
	        $info['type'] = intval($type);
	    }

	    
	    $form = Form::make()
	    ->addPageTips('关键字不能为空')
	    ->addText('name','模块名称')
	    ->addText('keywords','关键字')
	    ->addRadio('pcwap','所属终端','',['通用模块','wap专用','PC专用','APP专用'],0)
	    ->addRadio('type','使用范围','',['通用','群聊专用','私聊专用'],1)
	    ->addIcon('icon','图标','若为空,就不显示按钮')
	    ->addText('init_jsfile','初始化加载JS网址(推荐)','(比较常用,推荐使用)界面少,逻辑多,就推荐用JS网址<a href="https://www.kancloud.cn/php168/x1_of_qibo/1434200" target="_blank">点击查看开发教程</a>')
	    ->addText('init_iframe','初始化加载框架网址(不推荐)','(比较少用,跨窗口要注意的事项比较多)界面多,才考虑用框架<a href="https://www.kancloud.cn/php168/x1_of_qibo/1434200" target="_blank">点击查看开发教程</a>')	    
	    ->addTextarea('init_jscode','脚本代码','(少用,代码较多的话,不推荐)太多语句,就不建议用,不能加外包围&lt;script type=&quot;text/javascript&quot;&gt;&lt;/script&gt;')
	    ->addCheckbox('allowgroup','限制用户组使用','',getGroupByid())
	    ->addPageTitle('添加模块');
	    return $form::fetchs();
	}

	public function edit($id=0){
	    
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();
	        $data['allowgroup'] = $data['allowgroup']?implode(',', $data['allowgroup']):'';
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (ChatmodModel::update($data)) {
	            cache('chatmod_cfg',null);
	            $this->success('修改成功',url('index',['type'=>$data['type']]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $info = getArray(ChatmodModel::get($id));
	    $form = Form::make([],$info)
	    ->addPageTitle('修改模块')
	    ->addText('name','模块名称')	    
	    ->addText('keywords','关键字')
	    ->addRadio('pcwap','使用范围','',['通用模块','wap专用','PC专用','APP专用'])
	    ->addRadio('type','使用范围','',['通用','群聊专用','私聊专用'])	    
	    ->addIcon('icon','图标','若为空,就不显示按钮')	    
	    ->addText('init_jsfile','初始化加载JS网址(推荐)','(比较常用,推荐使用)界面少,逻辑多,就推荐用JS网址<a href="https://www.kancloud.cn/php168/x1_of_qibo/1434200" target="_blank">点击查看开发教程</a>')
	    ->addText('init_iframe','初始化加载框架网址(不推荐)','(比较少用,跨窗口要注意的事项比较多)界面多,才考虑用框架<a href="https://www.kancloud.cn/php168/x1_of_qibo/1434200" target="_blank">点击查看开发教程</a>')
	    ->addTextarea('init_jscode','脚本代码','(少用,代码较多的话,不推荐)不能加外包围&lt;script type=&quot;text/javascript&quot;&gt;&lt;/script&gt;')
	    ->addNumber('list','排序值')
	    ->addCheckbox('allowgroup','指定用户组使用','不设置,即都有权限',getGroupByid())
	    ->addHidden('id',$id);

	    return $form::fetchs();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (ChatmodModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
