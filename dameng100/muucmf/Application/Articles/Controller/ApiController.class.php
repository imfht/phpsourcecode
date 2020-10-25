<?php
/**
     * APP News json接口
*/
namespace Articles\Controller;

use Think\Controller\RestController;

use Restful\Controller\BaseController;


class ApiController extends BaseController {
    protected $allowMethod    = array('get','post','put'); // REST允许的请求类型列表
    protected $allowType      = array('html','xml','json'); // REST允许请求的资源类型列表
    
    protected $ArticlesModel;
    protected $ArticlesCategoryModel;
	protected $ArticlesDetailModel;
    protected $codeModel;

    function _initialize()
    {
        parent::_initialize();

        //判断Restful模块是否安装
        $map['name'] = 'Restful';
        $map['is_setup'] = 1;
        $res = M('Module')->where($map)->find();
        if(!$res){
            echo 'Restful模块未安装，请到应用商店下载并安装该模块。';exit;
        }

        $this->articlesModel = D('Articles/Articles');
        $this->articlesCategoryModel = D('Articles/ArticlesCategory');
        $this->articlesDetailModel = D('Articles/ArticlesDetail');
        $this->codeModel = D('Restful/Code');

    }
	/**
     * 获取列表接口
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function index()
    {
        switch ($this->_method){
            case 'get': //get请求处理代码

                $page = I('page',1,'intval');
                $r = I('r',10,'intval');
                $aId = I('id',0,'intval');
                $category = I('cid',0,'intval');
                
                if($aId)
                { //如果有ID，输出ID内容详细
                    if (!($aId && is_numeric($aId))) {
                        $info = 'ID错误';
                    }else{
                        $data=$this->articlesModel->getData($aId);

                        $data['content']=$data['detail']['content'];
                        $data['author']=query_user(array('uid','nickname','avatar64','signature'),$data['uid']);
    					$data['thumbnail'] = getThumbImageById($data['cover'],352,240);
                        $data['user_articles_count']=$this->articlesModel->where(array('uid'=>$data['uid']))->count();

                        $data['thumbnail']=get_http_https() . $_SERVER['SERVER_NAME'] . ($data['thumbnail']);
                        $data['content']=$this->imageUrl($data['content']);
                        $data['create_time_str'] = friendlyDate($data['create_time']);
                        $data['update_time_str'] = friendlyDate($data['update_time']);
                        unset($data['detail']);
                        $this->_category($data['category']);

                        /*获取上传文件路径*/
                        $file_id = $data['download'];
                        $downfile = D('file')->find($file_id);
                        //dump($downfile);
                        /* 更新浏览数 */
                        $map = array('id' => $aId);
                        $this->articlesModel->where($map)->setInc('view');
                        $result = $this->codeModel->code(200);
                        $result['data'] = $data;
                        $this->response($result,$this->type);
                    }
                    
                }
                elseif($category)
                { //如果有分类ID，列出列表

                    $map['category']=$category;
                    $map['status']=1;
                    /* 获取当前分类下资讯列表 */
                    list($data,$totalCount) = $this->articlesModel->getListByPage($map,$page,'sort desc,update_time desc','*',$r);
                    foreach($data as &$val){
                        $val['author']=query_user(array('avatar32','nickname'),$val['uid']);
                        $val['thumbnail'] = getThumbImageById($val['cover'],352,240);
                        $val['thumbnail']=get_http_https() . $_SERVER['SERVER_NAME'] . ($val['thumbnail']);
                        $val['create_time_str'] = friendlyDate($val['create_time']);
                        $val['update_time_str'] = friendlyDate($val['update_time']);
                    }
                    unset($val);
                    $result = $this->codeModel->code(200);
                    $result['data'] = $data;
                    $this->response($result,$this->type);
                    
                }//结束列表输出
                else
                {//默认输出全部分类内容
                    $map['status']=1;
                    list($data,$totalCount) = $this->articlesModel->getListByPage($map,$page,'sort desc,update_time desc','*',$r);
                    foreach($data as &$val){
                        $val['author']=query_user(array('avatar32','nickname'),$val['uid']);
                        $val['thumbnail'] = getThumbImageById($val['cover'],352,240);
                        $val['thumbnail']=get_http_https() . $_SERVER['SERVER_NAME'] . ($val['thumbnail']);
                        $val['create_time_str'] = friendlyDate($val['create_time']);
                        $val['update_time_str'] = friendlyDate($val['update_time']);
                    }
                    unset($val);
                    $result = $this->codeModel->code(200);
                    $result['data'] = $data;
                    $this->response($result,$this->type);
                }   
            break;
            case 'put':
                
                $result['info'] = 'PUT未定义';
            break;
            case 'post'://post请求处理代码
                         
                $result['info'] = 'post未定义';
            break;
        }

       
    }
    /**
     * 获取分类接口
     * @return [type] [description]
     */
    public function category()
    {
         switch ($this->_method){
            case 'get': //get请求处理代码
                $aId = I('id',0,'intval');
                if($aId)
                { //如果有ID，输出ID内容详细
                    if (!($aId && is_numeric($aId))) {
                        $info = 'ID错误';
                    }else{
                    $data=$this->articlesCategoryModel->find($aId);
                    $result = $this->codeModel->code(200);
                    $result['data'] = $data;
                    $this->response($result,$this->type);
                    }
                }
                else
                {//默认输出全部分类
                    $map['status']=1;
                    $data = $this->articlesCategoryModel->where($map)->select();
                    $result = $this->codeModel->code(200);
                    $result['data'] = $data;
                    $this->response($result,$this->type);
                }    
            break;
            case 'put':    
                $result['info'] = 'PUT未定义';
            break;
            case 'post'://post请求处理代码             
                $result['info'] = 'post未定义';
            break;
        }   
    }
}