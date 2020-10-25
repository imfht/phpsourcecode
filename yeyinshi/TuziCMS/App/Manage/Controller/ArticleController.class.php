<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
//use Think\Controller;
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
use Common\Lib\String; //引入类函数
class ArticleController extends CommonController {
	/**
	 * 文章显示
	 */
    public function index(){
    	C('TOKEN_ON',false);//关闭表单令牌
    	//查询指定id的栏目信息
    	$id=I('get.id');//类别ID
    	$topcate=M('Column')->where("id=$id")->order('column_sort')->select();
//     	dump($topcate);
//     	exit;
    	
    	//查询所有栏目的信息
    	$m=M('Column')->order('column_sort')->select();
//     	dump($m);
//     	exit;
    	
    	//查询指定id的栏目下的所有文章
    	foreach ($topcate as $k => $v){
    		$cids=Category::getChildsId($m, $v['id']);//传递一个父级分类ID返回所有子分类ID
    		$cids[]=$v['id'];//将父级id也压进来赋值给$cids
//     		dump($cids);
//     		exit;
    		
    		//查询数据，没有分页
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		//$News=('News');
    		$topcate[$k]['news']=D('News')->where($where)->where("news_dell=0")->relation(true)->select();
    		$result=$topcate[$k]['news'];
    		
//     		dump($result);
//     		exit;
    		//**分页实现代码
    		$count = count($result);// 查询满足要求的总记录数
    		$Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    		$show = $Page->show();// 分页显示输出
    		//**分页实现代码

    		//查询数据，没有分页
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		//$News=('News');
    		$topcate[$k]['news']=D('News')->where($where)->where("news_dell=0")->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order('news_sort,id desc')->select();
    		$result=$topcate[$k]['news'];
    	}
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($result as $k2 => $v2){
    		$result[$k2]['news_title'] = Common::substr_ext($v2['news_title'], 0, 16, 'utf-8',"");
    	}
//     	dump($result);
//     	exit;
    	//**分页实现代码
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('count',$count);// 赋值分页输出
    	$this->assign('module',MODULE_NAME);// 赋值分页输出
    	//**分页实现代码
    	
    	$this->assign('vcolumn',$topcate);
    	$this->assign('vlist',$result);
    	$this->assign('nav',$id);
    	$this->display();	
    }
    
    
    /**
     * 显示文章视图
     */
    public function add(){	
    	$id=I('get.nav');
    	//文章所属分类
//     	dump($id);
//     	exit;
    	$m=D('Column')->order('column_sort ASC')->relation(true)->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	//$m=Category::unlimitedForlayer($m,'cate');
    	//$m=Category::getParents($m,21);
//     	dump($m);
//     	exit;
    	$this->assign('cate',$m);
    	
    	//文章属性
    	$attr=M('Attr')->select();
    	$this->assign('flagtypelist',$attr);
    	$this->assign('nav',$id);
    	$this->display();
    }
    
    /**
     * 处理添加文章
     */
    public function do_add(){
//     	dump($_POST);
//     	exit;
    	C('TOKEN_ON',false);//关闭表单验证
    	//**查询提交所属的栏目模型，是外链或者单页模型则提示添加失败
    	$nv_id=I('post.nv_id');
    	$nav=I('post.nav');
    	//dump($nv_id);
    	//exit;
    	$m=M('Column');
    	//配置文件开启了表单令牌验证 防止表单重复提交
    	if (!$m->autoCheckToken($_POST)){
    		$this->error('表单重复提交！');
    	}
    	$arr=$m->find($nv_id);
    	$column_link=$arr['column_link'];
    	$column_type=$arr['column_type'];
    	if($column_link==1){
    		$this->error('外部链接栏目,不能添加文章');
    	}
    	 
    	$m=M('Model');
    	$arr=$m->find($column_type);
    	$model_table=$arr['model_table'];
    	//dump($model_table);
    	//exit;
    	 
    	if($model_table=='page'){
    		$this->error('单页模型栏目,不能添加文章');
    	}
    	//echo 111;
    	//exit;
    	
    	$m=D('News'); //读取Message表的model模型文件MeesageModel.class.php
    	//自动创建  不需要接收表单    	
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	//**需要另外添加到数据库的在这里填写 
    	//**将内容截取一部分中文插入到描述中
    	//**trim去初空格       mb_substr截取字符      strip_tags去除html字符    

    	$content=I('post.news_content');
    	$description=trim(mb_substr(strip_tags($content),0, 200, 'utf-8'));
    	$description= str_replace("&nbsp;","",$description);
    	$description= str_replace(" ","",$description);
    	$description= str_replace("	","",$description);
    	$description= str_replace("\n",",",$description);
    	preg_match_all('/[x{4e00}-\x{9fa5}]+/u', $description,$arr);
    	$description=implode(',',$arr[0]);
    	//implode('用什么链接到一起',array(数组))

    	$m->news_description=$description;
    	$m->news_dell=0;
    	//dump($arr);
    	//dump($content);
    	
    	//处理图片文件上传
    	$file=$_FILES['news_pic']['name'];
//     	dump($file);
//     	exit;
    	if (!empty($file)) {
    		//如果有文件上传 上传附件
	    	$upload = new \Think\Upload();// 实例化上传类
	    	$upload->maxSize 	= 3145728;// 设置附件上传大小
	    	$upload->exts    	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    	$upload->savePath	= './Uploads/';// 设置附件上传根目录
	    	$upload->savePath  	=  '/Tmp/'; // 网站主栏目图片 设置附件上传（子）目录  缩略图
	    	$upload->autoSub 	= true;
	    	$upload->subName 	= array('date','Ymd');
	    	$upload->saveName = array('uniqid','');//设置上传文件规则
	    	$info = $upload->upload();
	    	//设置需要生成缩略图，仅对图像文件有效
	    	//$upload->thumb              = true;
	    	//设置需要生成缩略图的文件后缀
	    	//$upload->thumbPrefix        = 'm_,s_';  //生产2张缩略图
	    	//设置缩略图最大宽度
	    	//$upload->thumbMaxWidth      = '400,100';
	    	//设置缩略图最大高度
	    	//$upload->thumbMaxHeight     = '400,100';
	    	 
	    	//$upload->thumbRemoveOrigin  = true;//删除原图
	    	if (!$info) {
	    		//捕获上传异常
	    		$this->error($upload->getErrorMsg());
	    	} else {
	    		//取得成功上传的文件信息
	    		//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
	    		//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
	    		//dump($uploadList[0]);
	    		//exit;
	    		 
	    		foreach($info as $file){
	    			//$image = $file['savepath'].$file['savename'];
	    			$image = '/'.'Uploads'.$file['savepath'].$file['savename'];
	    			$size = $file['size'];
	    		}
	    	}
	    	
	    	$m->news_pic = $image;
	    	$news_content = I('post.news_content');
	    	$m->news_content = $news_content;
    	}else {
    		//将内容中的第一张图片的地址截取放在news_pic字段中
    		$str=$_POST['news_content'];
    		preg_match('/<img\s[^<>]*?src=[\'\"]([^\'\"<>]+?)[\'\"][^<>]*?>/i', $str,$pic);
    		if ($pic[1]){
    			$m->news_pic=$pic[1];
    		}else {
    			$image="/Data/Images/nopic.jpg";
    			$m->news_pic=$image;
    		}
    	}
// 		dump($image);
// 		exit;
    	
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库	
    	//$m->news_addtime=time();
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	// 判断内容是否为图片附件
    	$news_content = I('post.news_content');
    	//dump($news_content);
    	//exit;
    	// 正则判断文本内容是否包含图片
    	if(preg_match("/img src*/",$news_content)){
    		$m->news_images = 1;
    	}else {
    		$m->news_images = 0;
    	}
    	
    	$arr=$m->relation(true)->add();
//     	dump($arr);
//     	exit;
    	
    	//处理鉴定属性添加
    	$id=$arr;
    	$db=D('Attr_news');
    	$db->where(array('news_id'=>$id))->delete();
    	$data=array();
    	foreach (I('post.access') as $v){
    		$data[]=array(
    				news_id=>$id,
    				attr_id=>$v,
    		);
    	}
    	//     	    	dump($data);
    	//     	    	exit;
    	$result=$db->addAll($data);
    	//     	dump($result);
    	//     	exit;
    	
    	
    	if ($arr){
    		$this->success("新增成功", U('Article/index', array('id' => $nav)));
    	}else {
    		$this->error('新增失败');
    	}
    	
    }
    /**
     * 显示文章修改
     */
    public function edit(){
    	$id=I('get.id');
    	$m=D('News');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	$arr['news_showpic'] = __ROOT__.$arr['news_pic'];
//     	dump($arr);
//     	exit;
    	$this->assign('cate',$arr);
    	
    	//读取原有属性的id  然后跟属性数据库的值对比  有则置值为1  没有则是0
    	$m=D('News');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	$arr=$arr['child'];
//     	dump($arr);
//     	exit;
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]=$v2['id'];
    	}
//     	dump($arr);
//     	exit;
    	//文章属性
    	$attr=M('Attr')->select();
    	foreach($attr as $k2 => $v2){
    		if (in_array($v2['id'],$arr)){
    			$attr[$k2]['access'] = '1';
    		}else {
    			$attr[$k2]['access'] = '0';
    		}
    	}
    	$this->assign('flagtypelist',$attr);

//     	dump($attr);
//     	dump($arr);
//     	exit;
    	
    	//显示所属栏目
    	$m=M('Column')->order('column_sort ASC')->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	$this->assign('Columnlist',$m);
    
    	$this->display();
    }
    /**
     * 处理文章修改
     */
    public function do_edit(){
    	C('TOKEN_ON',false);
    	
//     	dump($_POST);
//     	exit;
    	
    	//处理鉴定属性编辑
    	$id=I('post.id');
		$db=D('Attr_news');
		$db->where(array('news_id'=>$id))->delete();
    	$data=array();
    	foreach (I('post.access') as $v){
    		$data[]=array(
    				news_id=>$id,
    				attr_id=>$v,
    				);
    	}
//     	dump($data);
//     	exit;
    	$result=$db->addAll($data);
//     	dump($result);
//     	exit;
    	
    	//处理文章添加
    	$m=D('News');
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	//**查询提交所属的栏目模型，是外链或者单页模型则提示添加失败
    	$nv_id=I('post.nv_id');
    	//dump($nv_id);
    	//exit;
    	$m=M('Column');
    	$arr=$m->find($nv_id);
    	$column_link=$arr['column_link'];
    	$column_type=$arr['column_type'];
    	if($column_link==1){
    		$this->error('所属栏目为外链模型！');
    	}
    	
    	$m=M('Model');
    	$arr=$m->find($column_type);
    	$model_table=$arr['model_table'];
    	//dump($model_table);
    	//exit;
    	
    	if($model_table=='page'){
    		$this->error('所属栏目为单页模型！');
    	}
    	
		//**数据插入操作
        $m=D('News');
        if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	//**需要另外添加到数据库的在这里填写 
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库	
    	$m->news_updatetime=time();
    	
    	//**将内容截取一部分中文插入到描述中
    	//**trim去初空格       mb_substr截取字符      strip_tags去除html字符    	
    	$content=I('post.news_content');
    	$description=trim(mb_substr(strip_tags($content),0, 200, 'utf-8'));
    	$description= str_replace("&nbsp;","",$description);
    	$description= str_replace(" ","",$description);
    	$description= str_replace("	","",$description);
    	$description= str_replace("\n",",",$description);
    	//dump($description);
    	//exit;
    	//正则截取中文函数
    	preg_match_all('/[x{4e00}-\x{9fa5}]+/u', $description,$arr);
    	//dump($arr);
    	//exit;
    	$description=implode(',',$arr[0]);
    	//implode('用什么链接到一起',array(数组))
    	//dump($description);
    	//exit;
    	$m->news_description=$description;
		
    	//缩略图上传
    	$file=$_FILES['news_pic']['name'];
    	$pic=I('post.pic');
    	 
    	if ($file){//选中上传图片
    		$id=I('post.id');
    		$m=M('News');
    		$arr=$m->find($id);
//     		dump($arr);
//     		exit;
    		//删除本地图片附件 unlink('图片url')
    		unlink('./Uploads/'.$arr["news_pic"]);
    		
    		//删除图片后重新上传
    		$upload = new \Think\Upload();// 实例化上传类
    		$upload->maxSize 	= 3145728;// 设置附件上传大小
    		$upload->exts    	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    		$upload->savePath	= './Uploads/';// 设置附件上传根目录
    		$upload->savePath  	=  '/Tmp/'; // 网站主栏目图片 设置附件上传（子）目录  缩略图
    		$upload->autoSub 	= true;
    		$upload->subName 	= array('date','Ymd');
    		$upload->saveName = array('uniqid','');//设置上传文件规则
    		$info = $upload->upload();
    		//设置需要生成缩略图，仅对图像文件有效
    		//$upload->thumb              = true;
    		//设置需要生成缩略图的文件后缀
    		//$upload->thumbPrefix        = 'm_,s_';  //生产2张缩略图
    		//设置缩略图最大宽度
    		//$upload->thumbMaxWidth      = '400,100';
    		//设置缩略图最大高度
    		//$upload->thumbMaxHeight     = '400,100';
    		 
    		//$upload->thumbRemoveOrigin  = true;//删除原图
    		if (!$info) {
    			//捕获上传异常
    			$this->error($upload->getErrorMsg());
    		} else {
    			//取得成功上传的文件信息
    			//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
    			//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
    			//dump($uploadList[0]);
    			//exit;
    		
    			foreach($info as $file){
    				//$image = __ROOT__.'/'.'Uploads'.$file['savepath'].$file['savename'];
    				$image = '/'.'Uploads'.$file['savepath'].$file['savename'];
    				$size = $file['size'];
    			}
    		}
//     		dump($image);
//     		exit;
    		
    		$m->news_pic = $image;
    		//$m->news_images = 1;
    	}else {
    		if ($pic){
    			$pic=I('post.pic');
//     			dump($pic);
//     			exit;
		    	if(preg_match("/nopic*/",$pic)){
		    		
		    		//将内容中的第一张图片的地址截取放在news_pic字段中
		    		$str=$_POST['news_content'];
		    		preg_match('/<img\s[^<>]*?src=[\'\"]([^\'\"<>]+?)[\'\"][^<>]*?>/i', $str,$pic);
		    		if ($pic[1]){
		    			$m->news_pic=$pic[1];
		    		}else {
		    			$image="/Data/Images/nopic.jpg";
		    			$m->news_pic=$image;
		    		}
		    	}else {
		    		$m->news_pic = $pic;
		    	}
    			
    			//$m->news_images =  1;
    		}else {
    			$image="/Data/Images/nopic.jpg";
    			$m->news_pic = $image;
    			//$m->news_images =  0;
    		}
    	}
    	
    	// 判断内容是否为图片附件
    	$news_content = I('post.news_content');
//     	dump($news_content);
//     	exit;
    	// 正则判断文本内容是否包含图片
    	if(preg_match("/img src*/",$news_content)){
    		$m->news_images = 1;
    	}else {
    		$m->news_images = 0;
    	}

    	 
    	//exit;
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	$arr=$m->save(); //自动修改 不需要定义id 因为post表单中已经有
    	//dump($arr);
    	//exit;
    	if ($arr){
    		$this->success('修改成功');
    	}else {
    		$this->error('修改失败');
    		//$this->error($m->geterror());
    	}
    }
    
    
    /**
     * 显示回收站新闻
     */
    public function trach(){
    	$m=D('News');
    	$arr=$m->relation(true)->where("news_dell=1 or nv_id=0 or news_title=null")->order('news_addtime desc')->select();
//     	dump($arr);
//     	exit;
    	//显示被删除news_dell=1的数据
    	
    	//**分页实现代码
    	$count = count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$arr=$m->relation(true)->limit($Page->firstRow.','.$Page->listRows)->where("news_dell=1 or nv_id=0 or news_title=null")->order('news_addtime desc')->select();
    	//显示被删除news_dell=1的数据
//     	dump($arr);
//     	exit;
    	
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['news_title'] = Common::substr_ext($v2['news_title'], 0, 16, 'utf-8',"");
    	}
    	//dump($arr);
    	//exit;
    	
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('count',$count);
    	$this->assign('vlist',$arr);
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    /**
     * 删除新闻至回收站处理
     */
    public function do_trach(){

        $m=M('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
        $data['id']=I('get.id');
    	$data['news_dell']=1;
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('删除至回收站成功！');
    	}
    	else {
    		$this->error('删除至回收站失败！');
    	}
    
    }
    
    
    /**
     * 回收站新闻还原处理
     */
    public function to_trach(){
    
    	$m=M('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	//配置文件开启了表单令牌验证 防止表单重复提交
    	if (!$m->autoCheckToken($_POST)){
    		$this->error('表单重复提交！');
    	}
    	$data['id']=I('get.id');
    	$data['news_dell']=0;
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('还原成功！');
    	}
    	else {
    		$this->error('还原失败！');
    	}
    
    }
    
    /**
     * 批量还原处理
     */
    public function to_trachall(){
    	//dump($_POST);
    	//exit;
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	//配置文件开启了表单令牌验证 防止表单重复提交
    	if (!$m->autoCheckToken($_POST)){
    		$this->error('表单重复提交！');
    	}
    	$id = I('post.id');
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$data['news_dell']=0;
    	$count=$m->where($where)->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功还原{$count}条！");
    	}
    	else {
    		$this->error('批量还原失败！');
    	}
    
    }
    
    /**
     * 显示批量移动
     */
    public function move(){
    	//dump($_POST);
    	//exit;
    	$id = I('post.id');
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	
    	$id_one=$id[0];
    	//dump($id_one);
    	//exit;
    	$m=D('News');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id_one);
    	//dump($arr);
    	//exit;
    	
    	//组合数组赋值给前台显示
    	$id = implode(',',$id);
    	$this->assign('data',$id);
    	
    	//显示所属栏目
    	$m=M('Column')->order('column_sort ASC')->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	$this->assign('Columnlist',$m);
    	
    	$this->assign('cate',$arr);
    	$this->display();
    
    }
    
    
    /**
     * 显示回收站批量移动
     */
    public function movetrach(){
    	//dump($_POST);
    	//exit;
    	$id = I('post.id');
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	 
    	$id_one=$id[0];
    	//dump($id_one);
    	//exit;
    	$m=D('News');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id_one);
    	//dump($arr);
    	//exit;
    	 
    	//组合数组赋值给前台显示
    	$id = implode(',',$id);
    	$this->assign('data',$id);
    	 
    	//显示所属栏目
    	$m=M('Column')->order('column_sort ASC')->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	$this->assign('Columnlist',$m);
    	 
    	$this->assign('cate',$arr);
    	$this->display();
    
    }
    
    /**
     * 批量移动处理
     */
    public function do_move(){
    	//dump($_POST);
    	//exit;
    	$nv_id=I('post.nv_id');
    	$id=I('post.id');
    	
    	//**查询提交所属的栏目模型，是外链或者单页模型则提示添加失败
    	$nv_id=I('post.nv_id');
    	//dump($nv_id);
    	//exit;
    	$m=M('Column');
    	//配置文件开启了表单令牌验证 防止表单重复提交
    	if (!$m->autoCheckToken($_POST)){
    		$this->error('表单重复提交！');
    	}
    	$arr=$m->find($nv_id);
    	$column_link=$arr['column_link'];
    	$column_type=$arr['column_type'];
    	if($column_link==1){
    		$this->error('所属栏目为外链模型！');
    	}
    	 
    	$m=M('Model');
    	$arr=$m->find($column_type);
    	$model_table=$arr['model_table'];
    	//dump($model_table);
    	//exit;
    	 
    	if($model_table=='page'){
    		$this->error('所属栏目为单页模型！');
    	}
    	//echo 111;
    	//exit;
    	
    	//正则截取中文函数,将逗号相隔的id转化成数组
    	preg_match_all('/[\w]+/u', $id,$arr); //匹配英文字母，大小写，_ 和数字
    	$id=$arr[0];
    	//dump($id);
    	//exit;
    	
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$data['nv_id']=$nv_id;
    	$count=$m->where($where)->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功移动{$count}条！", U('Article/index', array('id' => $nv_id)));
    	}
    	else {
    		$this->error('移动失败！');
    	}
    
    }
    
    
    /**
     * 批量移动处理
     */
    public function do_movetrach(){
    	//dump($_POST);
    	//exit;
    	$nv_id=I('post.nv_id');
    	$id=I('post.id');
    	
    	
    	//**查询提交所属的栏目模型，是外链或者单页模型则提示添加失败
    	$nv_id=I('post.nv_id');
    	//dump($nv_id);
    	//exit;
    	$m=M('Column');
    	//配置文件开启了表单令牌验证 防止表单重复提交
    	if (!$m->autoCheckToken($_POST)){
    		$this->error('表单重复提交！');
    	}
    	$arr=$m->find($nv_id);
    	$column_link=$arr['column_link'];
    	$column_type=$arr['column_type'];
    	if($column_link==1){
    		$this->error('所属栏目为外链模型！');
    	}
    	 
    	$m=M('Model');
    	$arr=$m->find($column_type);
    	$model_table=$arr['model_table'];
    	//dump($model_table);
    	//exit;
    	 
    	if($model_table=='page'){
    		$this->error('所属栏目为单页模型！');
    	}
    	//echo 111;
    	//exit;
    	 
    	//正则截取中文函数,将逗号相隔的id转化成数组
    	preg_match_all('/[\w]+/u', $id,$arr); //匹配英文字母，大小写，_ 和数字
    	$id=$arr[0];
    	//dump($id);
    	//dump($nv_id);
    	//exit;
    	 
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$data['nv_id']=$nv_id;
    	$data['news_dell']=0;
    	$count=$m->where($where)->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功移动{$count}条！", U('Article/index', array('id' => $nv_id)));
    	}
    	else {
    		$this->error('移动失败！');
    	}
    
    }
    
    
    /**
     * 彻底删除新闻处理
     */
    public function delete(){
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=M('News');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功');
    	}
    	else {
    		$this->error('删除失败');
    	}
		
    }
    
    /**
     * 批量彻底删除新闻处理
     */
    public function deleteall(){
        //dump($_POST);
    	//exit;
    	
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$id = I('post.id');
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;

    	$count=$m->where($where)->delete();
    	if ($count>0){
    		$this->success("成功彻底删除{$count}条！");
    	}
    	else {
    		$this->error('批量彻底删除失败！');
    	}
    
    }
    
    
    /**
     * 批量删除至回收站处理
     */
    public function delall(){		
		//dump($_POST);
		//exit;
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$id = I('post.id');   
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$data['news_dell']=1;
    	$count=$m->where($where)->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功删除{$count}条！");
    	}
    	else {
    		$this->error('批量删除失败！');
    	}
    
    }
    
    
    /**
     * 查询数据表单处理类文件
     */
    public function search(){
//     	dump($_GET);
//     	exit;
    	C('TOKEN_ON',false);//关闭表单令牌
    	$keyword=I('get.keyword');
        	//判断存在id
    	if ($id==null){
    		$this->assign('ifid',not);
    	}
    	if ($keyword==null){
    		$this->error('请输入搜索关键字！');
    	}
    	
    	$m=D('News');
    	$data['news_title']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->where('news_dell=0')->relation(true)->select();
//     	dump($arr);
//     	exit;
    	//**分页实现代码
    	$count=count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$arr=$m->where($data)->where('news_dell=0')->relation(true)->limit($Page->firstRow.','.$Page->listRows)->select();
//     	    	dump($arr);
//     	    	exit;
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['news_title'] = Common::substr_ext($v2['news_title'], 0, 16, 'utf-8',"");
    	}

    	if ($arr==null){
    		$this->error('没有数据');
    
    	}else {
    		//**分页实现代码
    		$this->assign('page',$show);// 赋值分页输出
    		//**分页实现代码
    		$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
    		$this->assign('module',MODULE_NAME);
    		$this->assign('count',$count); //在新查询到的数据再分配给前台模板显示
    		$this->display('index'); //指定模板
    	} 
    
    }
    
}
?>
