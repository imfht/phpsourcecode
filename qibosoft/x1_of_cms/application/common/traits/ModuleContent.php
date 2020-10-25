<?php
namespace app\common\traits;


trait ModuleContent
{
	use AddEditList;
    
	/**
	 * 新发表内容入口 有的模型可能不使用栏目 而是直接在模型下面发布东西
	 * @param number $mid
	 * @return unknown
	 */
	public function postnew($mid = 0){
	    if (config('post_need_sort')==true) {
	        return self::chooseSort($mid);
	    }else{
	        if (empty($mid) && count(model_config())==1) {
	            $array = model_config();
	            $array = array_values($array);
	            $mid = $array[0]['id'];
	            header("location:".auto_url('add',['mid'=>$mid]));
	            exit;
	        }
	        return self::chooseModule();
	    }
	}
	
	/**
	 * 分类读取
	 * @param int $pid
	 * @param int $list
	 */
	public function showthissorts($pid=0,$list=0){
		$list++;
		$show="<ul>\n";
		$lanmu=$this->s_model->where('pid',$pid)->select();
		foreach($lanmu as $k=>$rs){
			$lanmu2=$this->s_model->get(['pid'=>$rs['id']]);
			if(!$lanmu2){
				if($rs['allowpost']&&!in_array($this->user['groupid'],explode(',',$rs['allowpost']))){  //设置了用户组权限.
					$show.='';
				}else{
					$show.="<li><div><a href=\"".urls('add',['fid'=>$rs['id']])."\">".$rs['name']."</a></div></li>\n";
				}
			}else{
				if($rs['allowpost']&&!in_array($this->user['groupid'],explode(',',$rs['allowpost']))){  //设置了用户组权限.
					$show.='';
				}else{
					$show.="<li onClick=\"showmore(".$rs['id'].",".$list.",$(this))\"><div class=\"more\">".$rs['name']."</div></li>\n";
				}
			}
		}
		$show.="</ul>";
		return $show;
	}
	
	/**
	 * 新发表内容入口  选择栏目
	 * @param number $mid 必须先指定模型I
	 * @return unknown
	 */
	protected function chooseSort($mid = 0){
	    $sort_list = $this->s_model->getTreeList(0, $mid);
		$lanmu=$this->showthissorts(0,0);
		$this->assign('lanmu',$lanmu);
	    $template = getTemplate('postnew');
	    $tpl = $template ? $template : config('post_choose_sort');
	    return $this->fetch($tpl,['sort_list'=>$sort_list]);
	}
	
	/**
	 * 新发表内容入口  选择模型
	 * @return unknown
	 */
	protected function chooseModule(){
	    $model_list = $this->m_model->getList();
	    $template = getTemplate('postnew');
	    $tpl = $template ? $template : config('post_choose_model');
	    return $this->fetch($tpl,['model_list'=>$model_list]);
	}
	
	

	/**
	 * 处理提交的新发表数据
	 * @param number $mid 模型ID
	 * @param number $fid 栏目ID
	 * @param array $data POST表单的数据
	 * @param string $url 发表成功后,返回的URL地址
	 */
	protected function saveAdd($mid=0,$fid=0,$data=[],$url=''){

	    //主要针对多选项的数组进行处理
	    $data = $this->format_post_data($data);

	    if(!empty($this->validate)){
	        // 验证
	        $result = $this->validate($data, $this->validate);
	        if(true !== $result) $this->error($result);
	    }
	    $data['uid'] = $this->user['uid'];
	    $data['mid'] = $this->mid;	    
	    $id = $this->model->addData($this->mid,$data);	
	    
	    if(is_numeric($id)){

			//以下两行是接口
			hook_listen('cms_add_end',$id,['data' =>$data, 'module' =>$this->request->module()]);	    
			$this->end_add($id,$data);

			$this->success('新增成功', $url?:auto_url('index',$fid ? ['fid'=>$fid] : ['mid'=>$mid]),['id'=>$id] );
	    }else{
	        $this -> error('新增失败:'.$id);
	    }
	}
	
	/**
	 * 保存修改的数据
	 * @param number $mid
	 * @param array $data POST表单的数据
	 * @param string $url 发表成功后,返回的URL地址
	 */
	protected function saveEdit($mid=0,$data=[],$url=''){
	    
	    if (empty($data['id'])) {
	        $this -> error('ID参数值不存在!!');
	    }
	    
	    //主要针对多选项的数组进行处理
	    $data = $this->format_post_data($data);
	    
	    // 验证
// 	    if(!empty($this->validate)){
// 	        $result = $this->validate($data, $this->validate);
// 	        if(true !== $result) $this->error($result);
// 	    }
	    //$data['ispic'] = empty($data['picurl']) ? 0 : 1 ;
	    
	    $info = $this->getInfoData($data['id']);

	    $result = $this->model->editData($this->mid,$data);
	    
	    if($result){
	        //以下两行是接口
	        hook_listen('cms_edit_end',$data,['result' =>$result, 'module' =>$this->request->module(),'info'=>$info]);	        
	        $this->end_edit($data['id'],$data,$info);
	        
	        $this -> success('修改成功', $url?:auto_url('index',['mid'=>$mid]) );
	    }else{
	        $this -> error('修改无效');
	    }
	}
	
	/**
	 * 可同时删除多条
	 * @param unknown $ids
	 * @return number
	 */
	protected function deleteContent($ids){
	    $ids = is_array($ids) ? $ids : [$ids];
	    $num = 0;
	    foreach($ids AS $id){
	        if($this->deleteOne($id)){	            
	            $num++;
	        }
	    }
	    return $num;
	}
	
	/**
	 * 获取相关栏目，给做模板时扩展调用,不是必须的
	 * @param string $type
	 * @param number $fid
	 * @return unknown
	 */
	public function get_sort_title($type='top',$fid=0){
	    if ($type=='all') {    //所有栏目
	        $map = [];
	    }elseif($type=='top'){     //一级栏目
	        $map = ['pid'=>0];
	        $array = $this->s_model->getTitleList(['pid'=>0]);
	    }elseif($type=='son'&&$fid){     //子栏目
	        $map = ['pid'=>$fid];
	    }else{
	        $map = [];
	    }
	    $array = $this->s_model->getTitleList($map);
	    return $array;
	}
	
	/**
	 * 获取相关模型，做模板时扩展调用 ,不是必须的
	 * @return unknown
	 */
	public function get_model_title(){
	    $array = $this->m_model->getTitleList();
	    return $array;
	}
	
	/**
	 * 后台列表数据的搜索字段
	 * @return array[]
	 */
	protected function getEasySearchItems()
	{
	    return \app\common\field\Table::get_search_field($this->mid);
	}
	
	/**
	 * 后台列表数据的筛选字段
	 * @return array[]
	 */
	protected function getEasyfiltrateItems()
	{
	    return \app\common\field\Table::get_filtrate_field($this->mid);
	}
	
	/**
	 * 获取列表页面要显示的自定义字段
	 * @return unknown[][]|string[][]|mixed[][]
	 */
	protected function getEasyIndexItems($field_array=[])
	{
	    return \app\common\field\Table::get_list_field($this->mid,$field_array);
	}
	
	/**
	 * 某个字段要关联其它字段
	 * @return string[][]|unknown[][]
	 */
	protected function getEasyFieldTrigger(){
	    return \app\common\field\Form::getTrigger($this->mid);
	}
	
	/**
	 * 发表与修改表页面的自定义字段信息
	 * @return unknown[][]|array[][]
	 */
	protected function getEasyFormItems()
	{
	    return \app\common\field\Form::get_all_field($this->mid);
	}
	
	/**
	 * 具体某个栏目的配置信息
	 * @param unknown $fid
	 * @return array
	 */
	protected function sortInfo($fid){
	    $s_info = [];
	    if($fid){
	        $s_info = $this->s_model->getInfoById($fid);
	    }
	    return $s_info;
	}
	
	/**
	 * 列表页取数据
	 * @param array $map
	 * @param array $order
	 * @param array $pages
	 * @return unknown
	 */
	protected function list_page_data($map=[],$order=[],$pages=[]){
	    return $this->getListData($map ,$order ,$pages);
	}

	/**
	 * 获取数据，自定义字段的必须按模型或栏目获取，因为字段不一样。
	 * @param array $map
	 * @param string $order
	 * @param number $rows
	 * @param array $pages
	 * @param string $format 是否对数据进行转义
	 * @return unknown
	 */
	protected function getListData($map=[],$order='',$rows=0,$pages=[],$format=false)
	{
		// 查询
	    $map = array_merge($this->getMap(),$map);
		// 排序
	    $order = $this->getOrder($order);
		// 数据列表
		//$data_list = $this->model->where($map)->order($order)->paginate();		
	    //$table = $this->model->get_model_key().'_content'.$this->mid;
	    $order = trim($order);
	    if(empty($order)){
	        $order = 'list desc ,id desc';
	    }elseif($order == 'list desc'){
	        $order .= ',id desc';
	    }
	    return $this->model->getListByMid($this->mid,$map,$order,$rows,$pages,$format);
	}
		
	/**
	 * 对POST的数据进行转义处理
	 * @param unknown $data
	 * @return number
	 */
	protected function format_post_data($data){
	    //$field_array = $this->f_model->getFields(['mid'=>$this->mid]);
	    $field_array = get_field($this->mid);
	    foreach ($field_array as $rs) {
	        $value = \app\common\field\Post::format($rs,$data);
	        if($value!==null){     //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
	            $data[$rs['name']] = $value;
	        }
// 	        $name = $rs['name'];
// 	        $type = $rs['type'];
// 	        if (!isset($data[$name])) {
// 	            switch ($type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 0;
// 	                    break;
// 	                case 'checkbox':
// 	                    $data[$name] = '';
// 	                    break;
// 	            }
// 	        } else {
// 	            // 如果值是数组则转换成字符串，适用于复选框等类型
// 	            if (is_array($data[$name])) {
// 	                $data[$name] = implode(',', $data[$name]);
// 	                $type == 'checkbox' && $data[$name] = ','.$data[$name] .',';   //方便搜索 like %,$value,%
// 	            }
// 	            switch ($type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 1;
// 	                    break;
// 	                case 'images2':
// 	                    //$data[$name] = serialize(array_values($data['images2'][$name]));
// 	                    //$data[$name] = json_encode(array_values($data['images2'][$name])); 
// 	                    break;
// 	                    // 日期时间
// 	                case 'date':
// 	                case 'time':
// 	                case 'datetime':
// 	                    $data[$name] = strtotime($data[$name]);
// 	                    break;
// 	            }
// 	        }
	    }
	    return $data;
	}
	
	
	/**
	 * 获取单条内容信息,修改内容时要用到 内容显示页也会用到
	 * @param number $id 内容ID
	 * @param string $format  是否转义, 修改内容时不允许转义,必须取数据库的原始数据, 内容页也不建议使用
	 * @return unknown
	 */
	protected function getInfoData($id=0,$format=false)
	{
	    return $this->model->getInfoByid($id , $format);
	}
	
	/**
	 * 删除单条内容
	 * @param unknown $id 内容ID
	 * @param number $mid 模型ID,可为空
	 * @return boolean
	 */
	protected function deleteOne($id,$mid=0){
	    $info = $this->getInfoData($id);
	    
	    if ($this->model->deleteData($id,$mid)) {
	        //以下两行是接口
	        hook_listen('cms_delete_end',$info,$this->request->module());	            
	        $this->end_delete($id,$info);
	        
	        return true;
	    }
	}
	
	/**
	 * 会员中心自动生成辅栏目列表页模板
	 * @param unknown $data_list
	 * @param string $tpl
	 * @param array $vars
	 * @return mixed|string
	 */
	protected function makeListInfo($data_list,$tpl='',$vars=[])
	{	    
	    //前台列表页母模型，可以自由定义
	   // $template = $tpl ? $tpl : config('automodel_category_listpage');
	    
	    //return $list_table->fetch();
	}
	
	/**
	 * 分组显示处理
	 * @param unknown $form_items
	 * @return array|unknown
	 */
	protected function get_group_form($form_items){
	    $_field = $this->f_model->where('mid',$this->mid)->where('nav','<>','')->column('name,nav');
	    
	    if(!empty($_field)){
	        $_group = [];
	        foreach ($form_items AS $key=>$rs){
	            if($_field[$rs[1]]){
	                $_group[$_field[$rs[1]]][] = $rs;
	            }else{
	                $_group['基础信息'][] = $rs;
	            }
	        }
	    }
	    return $_group;
	}
	
	/**
	 * 齐博首创 钩子文件扩展接口 每个应用下的对应文件,比如 bbs/ext/add_check_xxxx.php
	 * preg_replace("/([^:]+)::([^:]+)/i", "\\2", __METHOD__)
	 * @param string $type 类型分别是 cms_add_begin,cms_edit_begin,cms_delete_begin,cms_add_end,cms_edit_end,cms_delete_end
	 * @param array $data POST表单提交的内容
	 * @param array $info 数据库的内容
	 * @param array $array 附加参数
	 */
	protected function get_hook($type='',&$data=[],$info=[],$array=[]){
	    return parent::get_hook($type,$data,$info,$array);    //继承 \app\common\controller\base.php
	}
	

	/**
	 * 适用于前台会员 新增加前做检查
	 * @param number $mid 模型ID
	 * @param number $fid 栏目ID
	 * @param array $data POST表单的数据,可以进行再次修改
	 * @return boolean
	 */
	protected function add_check($mid=0,$fid=0,&$data=[]){
	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_add_begin',$data,$info=[],['mid'=>$mid,'fid'=>$fid]);
	    if($result!==null){
	        return $result;
	    }
	    
	    if(empty($this->webdb['allow_guest_post']) && !$this->user){
	        return '请先登录!';
	    }elseif($this->user['groupid']==2){
	        return '很抱歉,你已被列入黑名单,没权限发布,请先检讨自己的言行,再联系管理员解封!';
	    }elseif($this->user && $this->user['yz']==0){
	        return '很抱歉,你的身份还没通过审核验证,没权限发布!';
	    }elseif($mid && !get_field($mid)){
	        return '模型不存在!';
	    }elseif(!$this->admin && fun('admin@sort',$fid)!==true && config('webdb.can_post_group') && !in_array($this->user['groupid'], config('webdb.can_post_group'))){
	        return '你所在用户组没权限!';
	    }elseif(empty($this->admin) && $this->webdb['forbid_post_topic_phone_noyz'] && empty($this->user['mob_yz']) ){
	        return '很抱歉,你没有绑定手机,没权限发布,请先进会员中心绑定手机!';
	    }
	    
	    $result = $this->market_check();
	    if ($result!==true){
	        return $result;
	    }
	    
	    $result = $this->check_info_num();
	    if ($result!==true) {  //检查对应用户组的发布数量限制	        
	        return $result;
	    }
	    
	    $result = $this->check_post_money();
	    if ($result!==true) {  //检查对应用户组的发布数量限制
	        return $result;
	    }
	    
	    $data['status'] = 0;
	    if ( empty($this->webdb['post_auto_pass_group'])
	        || in_array($this->user['groupid'], $this->webdb['post_auto_pass_group'])
	        || $this->admin
	        || fun('admin@sort',$data['fid'])===true
	        || ($this->webdb['post_auto_pass_uids'] && in_array($this->user['uid'], str_array($this->webdb['post_auto_pass_uids'])))
	        ) {
	            $data['status'] = 1;
	    }
	    
	    $s_config = $fid ? get_sort($fid,'config') : [];
	    if($s_config['allowpost']){
	        if( !$this->admin && fun('admin@sort',$fid)!==true && !in_array($this->user['groupid'], explode(',',$s_config['allowpost'])) ){
	            return '你所在用户组,无权在此栏目发布!';
	        }
	    }
	    if($s_config['ext_id'] && !$data['ext_id']){
	        $data['ext_id'] = $s_config['ext_id']; //比如论坛栏目自动绑定到圈子
	    }
	    
	    if(!$this->admin){
	        if($data['title']){
	            if(get_cookie('cms_title')==md5($data['title'])){
	                return '请不要重复发表相同的主题!';
	            }
	        }
	        if($data['content']){
	            if(get_cookie('cms_content')==md5($data['content'])){
	                return '请不要重复发表相同的内容!';
	            }
	        }
	    }
	    
	    $data = array_merge(input(),$data);
	    $array = explode(',','view,replynum,usernum,agree,reward,list,id');
	    foreach($array AS $key){
	        unset($data[$key]);
	    }
	    
	    $this->get_bdmap($data,$mid);
// 	    if(isset($data['map'])&&strstr($data['map'],',')){
// 	        list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
// 	    }

	    //$data['title'] = filtrate($data['title']);                             //标题过滤
	    //$data['content'] = fun('filter@str',$data['content']);     //内容过滤
	    if ($this->request->isPost()&&fun('ddos@add',$data)!==true) {    //防灌水
	        return fun('ddos@add',$data);
	    }
	    
	    $result = $this->check_post_filed($data,$mid);
	    if ($result!==true) {
	        return $result;
	    }

	    return true;
	}
	
	/**
	 * 检查字段
	 * @param array $data
	 * @param number $mid
	 * @return string|boolean
	 */
	protected function check_post_filed(&$data=[],$mid=0){
	    if ($this->request->isPost()){
	        foreach(get_field($mid) AS $rs){
	            if($rs['group_post']!='' && !in_array($this->user['groupid'],explode(',',$rs['group_post']))){ //指定用户组才能使用的字段
	                unset($data[$rs['name']]);
	            }elseif ($rs['ifmust']==1&&$data[$rs['name']]=='') {   //0 或0.00 没做判断处理
	                return $rs['title'].'不能为空!';
	            }elseif($data[$rs['name']]!=''){
	                if (in_array($rs['type'], ['text','image','images','file','files'])) {
	                    $data[$rs['name']] = filtrate($data[$rs['name']]);
	                }	                
	            }	            
	        }
	    }
	    return true;
	}
	
	/**
	 * 检查发布主题的时候是奖励积分,还是需要扣除积分,扣除积发的话, 如果用户积分不足,就不能发布.
	 * @return boolean|string
	 */
	protected function check_post_money(){
	    $group_array = json_decode($this->webdb['group_post_money'],true);
	    $groupid = $this->user['groupid'];
	    if( empty($group_array[$groupid]) ){
	        return true;
	    }
	    if($group_array[$groupid]<0 && $this->user['money']<abs($group_array[$groupid])){
	        return '你的积分不足 '.abs($group_array[$groupid]) .'，请先充值！';
	    }
	    return true;
	}
	
	/**
	 * 检查对应用户组的发布数量限制	   
	 * @param number $mid 为0的时候,针对所有模型
	 * @return string|boolean
	 */
	protected function check_info_num($mid=0){
// 	    if($this->admin){
// 	        return true;
// 	    }
	    $group_array = json_decode($this->webdb['group_create_num'],true);
	    $groupid = $this->user['groupid'];
	    if($group_array[$groupid]<0){
	        return '你所在用户组没权限发表，想要发表， 请升级用户组';
	    }elseif (empty($group_array[$groupid])) {
	        return true;
	    }
	    
	    $num = $this->model->user_info_num($this->user['uid'],0);

	    if($num>=$group_array[$groupid]){
	        return '你所在用户组的发布数量不能超过 '. $group_array[$groupid] .' 条记录，想要发表更多， 请升级用户组';
	    }
	    return true;
	}
	
	/**
	 * 适用于前台会员 修改前做检查
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 * @return boolean
	 */
	protected function edit_check($id=0,$info=[],&$data=[]){
	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_edit_begin',$data,$info,['id'=>$id]);
	    if($result!==null){
	        return $result;
	    }
	    
	    $result = $this->market_check();
	    if ($result!==true){
	        return $result;
	    }
	    
	    if($info['uid']!=$this->user['uid'] && empty($this->admin) && fun('admin@sort',$info['fid'])!==true && ENTRANCE!=='admin'){
	        return '你没权限!';
	    }
	    if($data){
	        $this->get_bdmap($data,$info['mid']);
//     	    if(isset($data['map'])){
//     	        list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
//     	    }
    	    unset($data['uid'],$data['status'],$data['view'],$data['mid'],$data['list']);
    	    if (isset($data['picurl'])) {
    	        $data['ispic'] = empty($data['picurl']) ? 0 : 1 ;
    	    }
    	    if(!empty($this->validate)){
    	        $result = $this->validate($data, $this->validate);
    	        if(true !== $result) return $result;
    	    }
	    }
	    //$data['title'] = filtrate($data['title']);                             //标题过滤
	    //$data['content'] = fun('Filter@str',$data['content']);     //内容过滤	  
	    
	    $result = $this->check_post_filed($data,$info['mid']);
	    if ($result!==true) {
	        return $result;
	    }
	    
	    return true;
	}
	
	/**
	 * 获取地图坐标
	 * @param array $data
	 * @param number $mid
	 */
	protected function get_bdmap(&$data=[],$mid=0){
	    foreach(get_field($mid) AS $k=>$rs){
	        if ($rs['type']=='bmap' && isset($data[$k]) && $data[$k]) {
	            list($data['map_x'],$data['map_y']) = explode(',', $data[$k]);
	            return ;
	        }
	    }
	}

	/**
	 * 适用于前台会员 删除前做检查
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 * @return boolean
	 */
	protected function delete_check($id=0,$info=[]){
	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_delete_begin',$data=[],$info,['id'=>$id]);
	    if($result!==null){
	        return $result;
	    }
	    
	    if( empty($this->admin) && fun('admin@sort',$info['fid'])!==true ){
	        if ($info['uid']!=$this->user['uid'] || empty($info['uid'])) {
	            return '你没权删除ID:' . $id;
	        }	        
	    }
	    return true;
	}
	
	/**
	 * 同时适用于前台与后台 新增加后做个性拓展
	 * @param number $id 内容ID
	 * @param number $data 内容数据
	 */
	protected function end_add($id=0,$data=[]){
	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_add_end',$data,$info=[],['id'=>$id]);
	    if($result!==null){
	        return $result;
	    }
	    $this->add_post_money($data);
	    $this->add_post_category($id,$data);
	    $this->send_admin_msg($id,$data);
	    set_cookie('cms_title', md5($data['title']));
	    set_cookie('cms_content', md5($data['content']));
	}
	
	/**
	 * 对频道管理员进行消息通知
	 * @param number $id
	 * @param array $data
	 */
	protected function send_admin_msg($id=0,$data=[]){
	    if ($data['status']==0 && $this->webdb['admin']!='') {
	        $detail = explode(',',$this->webdb['admin']);
	        foreach($detail AS $_uid){
	            if ($_uid=='' || in_array($_uid, [147,69])) {
	                continue;
	            }
	            $title = '请及时审核 '.M('name').' 新主题';
	            $content = '“'.$this->user['username'].'” 刚刚在 '.M('name').' 发布了: 《' . $data['title'] . '》，请尽快审核！<a href="'.get_url(urls('content/show',['id'=>$id])).'" target="_blank">点击查看详情</a>';
	            send_msg($_uid, $title, $content);
	            send_wx_msg($_uid, $content);
	        }
	    }
	}
	
	/**
	 * 发布主题的时候是奖励积分或者扣除积分
	 */
	protected function add_post_money($info=[]){
	    $group_array = json_decode($this->webdb['group_post_money'],true);
	    $groupid = $this->user['groupid'];
	    if( empty($group_array[$groupid]) ){
	        return ;
	    }
	    if($group_array[$groupid]<0){
	        $msg = M('name') . '发布扣除:'.$info['title'];
	    }else{
	        $msg = M('name') . '发布奖励:'.$info['title'];
	    }
	    add_jifen($this->user['uid'], $group_array[$groupid],$msg,$this->webdb['group_topic_jftype']);
	}
	
	/**
	 * 删除主题时,如果原来新发表有奖励的话,这里要对应的扣除. 如果新发表是扣除的话,这里不做补偿
	 */
	protected function delete_post_money($info=[]){
	    if ($info['status']==-1) {
	        return ;   //回收站清除,就不重复扣积分了
	    }
	    $group_array = json_decode($this->webdb['group_post_money'],true);
	    $groupid = $this->user['groupid'];
	    if( empty($group_array[$groupid]) ){
	        return ;
	    }
	    if($group_array[$groupid]<0){
	        return ;
	    }else{
	        $msg = M('name') . '删除主题扣除:'.$info['title'];
	    }
	    add_jifen($info['uid'], -$group_array[$groupid],$msg,$this->webdb['group_topic_jftype']);
	}
	
	/**
	 * 同时适用于前台与后台 修改后做个性拓展
	 * @param number $id 内容ID
	 * @param array $data 内容数据
	 */
	protected function end_edit($id=0,$data=[],$info=[]){
	    
	    $this->edit_post_category($id,$data);	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_edit_end',$data,$info,['id'=>$id]);
	    if($result!==null){
	        return $result;
	    }
	    
	}
	
	/**
	 * 同时适用于前台与后台 删除后做个性拓展
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 */
	protected function end_delete($id=0,$info=[]){
	    
	    //齐博首创 钩子文件扩展接口
	    $result = $this->get_hook('cms_delete_end',$data=[],$info,['id'=>$id]);
	    if($result!==null){
	        return $result;
	    }
	    
	    $this->delete_post_money($info);
	}
	
	/**
	 * 加内容进辅栏目
	 * @param number $id
	 * @param array $data
	 */
	protected function add_post_category($id=0,$data=[]){
	    if ( empty($data['category_fids']) || empty(config('use_category')) ) {
	        return [];
	    }
	    $array = [];
	    foreach($data['category_fids'] AS $fid){
	        $array[] = [
	            'aid'=>$id,
	            'cid'=>$fid,
	        ];
	    }
	    $this->info_model->saveAll($array);
	}
	
	/**
	 * 修改处理辅栏目
	 * @param number $id
	 * @param array $data
	 * @return array
	 */
	protected function edit_post_category($id=0,$data=[]){
	    if (empty(config('use_category')) ) {
	        return [];
	    }
	    $cid_array = $this->info_model->where('aid',$id)->column('id,cid');
	    foreach($cid_array AS $k=>$fid){
	        if (!in_array($fid, $data['category_fids'])) {
	            $this->info_model->where('id',$k)->delete();
	        }
	    }
	    $array = [];
	    foreach($data['category_fids'] AS $fid){
	        if (!in_array($fid, $cid_array)) {
	            $array[] = [
	                'aid'=>$id,
	                'cid'=>$fid,
	            ];
	        }	        
	    }
	    $array && $this->info_model->saveAll($array);
	}
	
	
	/**
	 * 获取辅栏目
	 * @return array|string[][]
	 */
	protected function get_category_select($id=0){
	    if (empty(config('use_category')) || !in_array($this->user['groupid'], $this->webdb['post_use_category'])) {
	        return [];
	    }
	    $fid_array = $this->category_model->getTreeTitle(0,0,false);
	    if(empty($fid_array)){
	        return [];
	    }
	    $array = [];
	    if ($id) {
	        $array = $this->info_model->where('aid',$id)->column('cid');
	    }
	    return [
	        ['checkboxtree','category_fids','辅栏目','',$fid_array,$array]
	    ];
	}
	
	/**
	 * 获取用户加入过的圈子
	 * @param array $info 内容信息
	 * @return array
	 */
	protected function get_my_qun($info=[]){
	    $marray = modules_config('qun');
	    if ( empty($marray) || config('system_dirname')=='qun') {
	        return [];
	    }
	    if ($info&&$info['uid']) {
	        $uid = $info['uid'];
	    }else{
	        $uid = $this->user['uid'];
	    }
	    $array = fun('qun@myjoin',$uid);
	    if ($array) {        
	        if(!isset($this->webdb['M__qun']['modules_show_select_qun']) || in_array(config('system_dirname'), $this->webdb['M__qun']['modules_show_select_qun']) ){
	            $ext_sys = $marray['id'];
	            $data = [];
	            foreach($array AS $rs){
	                $data[$rs['id']] = $rs['title'];
	            }
	            $form_array = [
	                [ 'select','ext_id','所属'.QUN,'',$data,$_COOKIE['choose_qun_id']],
	                [ 'hidden','ext_sys',$ext_sys],
	            ];
	        }else{
	            $form_array = [];
	        }
	        
	        if (empty($info)) {    //修改就不处理了
	            if( $this->webdb['M__qun']['modules_show_select_topic'] && in_array(config('system_dirname'), $this->webdb['M__qun']['modules_show_select_topic']) ){
	            //if(!isset($this->webdb['M__qun']['modules_show_select_topic']) || in_array(config('system_dirname'), $this->webdb['M__qun']['modules_show_select_topic']) ){
	                $data2 = [];
	                foreach($array AS $rs){
	                    if ($rs['uid']==$uid) {
	                        $data2[$rs['id']] = $rs['title'];
	                    }
	                }
	                if ($data2) {
	                    $form_array[] = [ 'select','topic_aid','归属专题','',$data2];
	                    //$form_array[] = [ 'hidden','topic_sys',config('system_dirname')];
	                }
	            }	            
	        }
	        return $form_array;
	    }else{
	        return [];
	    }	    
	}
	
	/**
	 * 应用市场权限检测
	 * @param array $info
	 */
	protected function market_check($topic=[]){
	    if(ENTRANCE==='admin'||in_array(config('system_dirname'), ['vote'])){
			return true;
		}
	    if (defined('IN_PLUGIN')) {
	        $plugin = input('param.plugin_name');
	        $info = plugins_config($plugin);
	    }elseif(config('system_dirname')!=''){
	        $info = modules_config( config('system_dirname') );
	    }
	    $uid = $topic?$topic['uid']:$this->user['uid'];
	    if ($info['is_sell']) {
	        $groupid = $topic ? get_user($topic['uid'])['groupid'] : $this->user['groupid'];
	        if($info['admingroup']=='' || !in_array($groupid, explode(',', $info['admingroup'])) ){
	            $rs = \app\common\model\Module_buyer::where('uid',$uid)->where('mid',defined('IN_PLUGIN')?-$info['id']:$info['id'])->find();
	            if (!$rs || ($rs['endtime']>0&&$rs['endtime']<time())) {
	                if ($uid==$this->user['uid']) {
	                    if( $rs['endtime']>0 && $rs['endtime']<time() ){
	                        return '当前应用有效期已过，请先充值购买后才能继续使用！';
	                    }elseif($info['testday']>0){
	                        if ( $this->request->isPost() ) {
	                            $array = [
	                                'uid'=>$this->user['uid'],
	                                'mid'=>defined('IN_PLUGIN')?-$info['id']:$info['id'],
	                                'create_time'=>time(),
	                                'endtime'=>time()+$info['testday']*3600*24,
	                            ];
	                            \app\common\model\Module_buyer::create($array);
	                        }else{
	                           list($day,$rmb,$title)=explode('|',explode("\r\n", $info['money'])[0]);
	                           return '当前应用可以试用 '.$info['testday'].' 天，你可以先试用！免费体验觉得满意后再考虑续费！<br>注意：试用期结束后，'."{$rmb}元{$title}起售"; 
	                        }                        
	                    }else{
	                        return '当前应用没有试用期，请先充值购买后才使用！';
	                    }	                    
	                }else{
	                    return '该用户使用当前应用的有效期已失效，所以无法访问！';
	                }
	            }
	        }
	    }
	    return true;
	}
	
}





