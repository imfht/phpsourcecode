<?php 
/*
 * 文章模型
 */
namespace Common\Model;
use Think\Model;
class ContentModel extends Model {
	
	private $setting;
	private $attachment;
	private $modelid;
	private $model_tablename;
 	private $data_fields = array(
	        'system' => array('catid', 'url', 'title', 'thumb', 'keywords', 'description', 'status', 'code', 'inputtime', 'updatetime'),  //对内容进行分表处理,  如果不需要,都放到system
	        'data' => array('content'),
	);

	public function __construct($name='',$tablePrefix='',$connection='') {
        // 模型初始化
        $this->_initialize();
        // 获取模型名称
        $this->name   =  '';
        // 设置表前缀
        if(is_null($tablePrefix)) {// 前缀为Null表示没有前缀
            $this->tablePrefix = '';
        }elseif('' != $tablePrefix) {
            $this->tablePrefix = $tablePrefix;
        }elseif(!isset($this->tablePrefix)){
            $this->tablePrefix = C('DB_PREFIX');
        }

        // 数据库初始化操作
        // 获取数据库操作对象
        // 当前模型有独立的数据库连接信息
        $this->db(0,empty($this->connection)?$connection:$this->connection,true);
		$this->attachment = D('Attachment');
		$this->setting = array(
			'enablesaveimage' => 0,
			'enablesavebase64image' => 0,
		);
    }
	// public function __construct(){
	// 	parent::__construct();
	// 	$this->attachment = D('Attachment');
	// 	$this->setting = array(
	// 		'enablesaveimage' => 0,
	// 		'enablesavebase64image' => 0,
	// 	);
	// }
	protected $_validate = array(
			array('title','require','标题不能为空！', 1), 
			array('content','require','内容不能为空！', 1), 
 			array('catid','require','栏目不能为空',1, '', 1), 
// 			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
// 			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
// 			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式 
	);
	
	public function set_model($modelid) {
		$models = D('Admin/Model')->getAllModels();
		$this->modelid = $modelid;
		$this->model_tablename = $models[$modelid]['tablename'];
		$this->name = $models[$modelid]['tablename'];
		//指定表
		$this->options['table'] = $this->tablePrefix . $this->model_tablename;
	}

	public function getDetail($id){
	    $map['id'] = $id;
	    //主表
	    $systeminfo = $this->where(array('id' => $id))->find();
	    if(!$systeminfo)
	        return false;
	    //附表, 临时解决办法
	    $modelinfo = M($this->model_tablename.'_data')->where(array('id' => $id))->find();
	    $modelinfo['content'] = html_entity_decode($modelinfo['content']);
	    $detail = array_merge($systeminfo,$modelinfo);
	    return $detail;
	}
		
	function getname($fileext){
	    return date('Ymdhis').rand(100, 999).'.'.$fileext;
	}
	
	/**
	 * 添加内容
	 *
	 * @param $datas
	 * @param $isimport 是否为外部接口导入
	 */
	public function add_content($data, $isimport = 0){
		$modelid = $this->modelid;
		//内容检测， 区分主表信息和附表信息
		$content_input = new \Lain\Phpcms\content_input($this->modelid);
		$inputinfo = $content_input->get($data,$isimport);

		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];
		//是否下载内容中的图片
		if($this->setting['enablesaveimage']){
			$data['content'] = $this->attachment->download('content', $data['content']);
		}
		//是否有创建时间
		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = NOW_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}
		
		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		
		//自动提取摘要
		if(isset($_POST['add_introduce']) && $data['description'] == '' && isset($data['content'])) {
			$content = stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$introcude_length = intval($_POST['introcude_length']);
			$data['description'] = str_cut(str_replace(array("'","\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;'), '', strip_tags($content)),$introcude_length);
			$systeminfo['description'] = addslashes($data['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $data['thumb'] == '' && isset($data['content'])) {
			$content = $content ? $content : stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$data['description']);
		//$systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);
		//主表
		$id = $this->add($systeminfo);
		//附表
		$modelinfo['id'] = $id;
		// $this->table(C('DB_PREFIX').$this->model_tablename.'_data')->add($modelinfo);
		M($this->model_tablename.'_data')->add($modelinfo);
		//$this->update($systeminfo,array('id'=>$id));
		//更新URL地址
		/* if($data['islink']==1) {
			$urls[0] = trim_script($_POST['linkurl']);
			$urls[0] = remove_xss($urls[0]);
				
			$urls[0] = str_replace(array('select ',')','\\','#',"'"),' ',$urls[0]);
		} else {
			$urls = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'],$inputinfo,'add');
		}
		 */
		if($data['status']==99) {
			//更新到全站搜索
			$this->search_api($id,$data);
		}

		//更新栏目统计数据
		$this->update_category_items($data['catid'],'add',1);

		return $id;
	}
	
	/**
	 * 修改内容
	 *
	 * @param $datas
	 */
	public function edit_content($data, $id) {
		$content_input = new \Lain\Phpcms\content_input($this->modelid);
		$inputinfo = $content_input->get($data);
	    
		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];
		//是否下载内容中的图片
		if($this->setting['enablesaveimage']){
			$data['content'] = $this->attachment->download('content', $data['content']);
		}
		
		//是否有创建时间
		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		//自动提取摘要
		if(isset($_POST['add_introduce']) && $systeminfo['description'] == '' && isset($modelinfo['content'])) {
			//$systeminfo是通过I方法传入的,　所以需要html_entity_decode反转义html标签
			$content = stripslashes(html_entity_decode($modelinfo['content'], ENT_QUOTES));
			$introcude_length = intval($_POST['introcude_length']);
			$systeminfo['description'] = str_cut(str_replace(array("'","\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;', ' '), '', strip_tags($content)),$introcude_length);
			$systeminfo['description'] = addslashes($systeminfo['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $systeminfo['thumb'] == '' && isset($modelinfo['content'])) {
			//$systeminfo是通过I方法传入的,　所以需要html_entity_decode反转义html标签
			$content = $content ? $content : stripslashes(html_entity_decode($modelinfo['content'], ENT_QUOTES));
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['description']);
		//保存主表信息
		$this->where(['id'=>$id])->save($systeminfo);
        //保存附表信息
		M($this->model_tablename.'_data')->where(array('id' => $id))->save($modelinfo);

		//调用 update
		$content_update = new \Lain\Phpcms\content_update($this->modelid, $id);
		$content_update->update($data);
		
		return true;
		
	}
	/**
	 * 删除内容
	 * @param $id 内容id
	 * @param $file 文件路径
	 * @param $catid 栏目id
	 */
	public function delete_content($id, $catid = null) {
		//删除主表数据
		//$this->delete(array('id'=>$id));
		$this->where('id='.$id)->delete();
		//删除从表数据
		M($this->model_tablename.'_data')->where(['id' => $id])->delete();
		//重置默认表
		//$this->table_name = $this->db_tablepre.$this->model_tablename;
		//更新栏目统计
		//$this->update_category_items($catid,'delete');
	}
	
	//栏目统计
	private function update_category_items($catid,$action = 'add',$cache = 0) {
		if($action=='add') {
			D('Category')->where(['catid' => $catid])->setInc('items');
		}  else {
			D('Category')->where(['catid' => $catid])->setDec('items');
		}
	}
	
	public function search_api($id = 0, $data = array(), $action = 'update') {
		$model = 'article';
		if($action == 'update') {
			//要搜索的字段
			$fulltext_array = array('title', 'description');
			foreach($fulltext_array AS $key){
				$fulltextcontent .= $data[$key];
			}
			D('Search')->update_search($model, $id, $fulltextcontent,addslashes($data['title']));
		} elseif($action == 'delete') {
			D('Search')->delete_search($model, $id);
		}
	}
}
