<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\User as UserModel;

/**
 * 用户实名认证
 */
class Yz extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext = [
        'id'=>false,                //用户数据表非常特殊，没有用id而是用uid
        'page_title'=>'用户实名认证',
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new UserModel();
    }
    
    /**
     * 用户列表
     */
    public function index() {
        if (plugins_config('baidu_api')) {
            $url = purl('baidu_api/realname/index',[],'admin');
            return $this->redirect($url);
        }
        $order = 'lastvist desc';
        $map = ['idcard'=>['<>','']];
        $this->list_items = [
            //['uid', '用户UID', 'text'],
            ['uid', '帐号', 'username'],
            ['truename', '真实名', 'text'],
            ['idcard', '证件号码', 'text'],
            ['uid','证件扫描件','callback',function($k,$v){
                $ext = $v['ext_field']?json_decode($v['ext_field'],true):[];
                if($ext['idcardpic']){
                    $ext['idcardpic'] = tempdir($ext['idcardpic']);
                }
                return "<a href='{$ext['idcardpic']}' target='_blank'><img src='{$ext['idcardpic']}' style='width:100px;height:75px;'></a>";
            }],
            ['idcard_yz', '审核操作', 'switch'],            
        ];
        
        $this -> tab_ext['search'] = ['username'=>'帐号','truename'=>'真实名','uid'=>'用户ID'];    //支持搜索的字段
        $this -> tab_ext['order'] = 'truename,idcard,uid';   //排序选择
        $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
        $url = urls('market/show',['id'=>465]);
        $this -> tab_ext['help_msg'] = '建议你安装更专业、更好用的公安联网实名插件，可以自动审核实名制。<a style="color:red;" href="javascript:open_market()">点击安装</a>'."
<script type='text/javascript'>  
function open_market(){
    layer.open({
    		type: 2,
    		title: '推荐安装应用',
    		shadeClose: true,
    		//offset: 'lb',
    		shade:  [0.5, '#393D49'],
    		maxmin: false, //开启最大化最小化按钮
    		area: ['1100px', '600px'],
    		content: '$url',
    		end: function(){
    			//setup_app(id,keywords,price,1);
    		}
    });
}
layer.confirm('建议你安装更专业、更好用的公安联网实名插件，可以自动审核实名制。', {
			title: '友情提醒!',
			btn : [ '马上安装', '取消' ],
			time: 6000,
			offset: 'rb',
			anim:2,
			shade: 0 //不显示遮罩 
	}, function(index) {
        layer.close(index);
	   open_market();
	});
</script>
"; 
        
        //筛选字段
        $this -> tab_ext['filter_search'] = [
            'groupid'=>getGroupByid(),
            'wx_attention'=>['未关注','已关注'],
            'yz'=>['未审核','已审核'],
            'idcard_yz'=>['未审核','已审核'],
        ];
        $this -> tab_ext['top_button'] = [];
        $this -> tab_ext['right_button'] = [];
 
        
        return $this -> getAdminTable(self::getListData($map, $order ));
    }
    
 
}
