<?php
namespace Home\Controller;

/**
 *
 * 公开页面访问接口
 */
class EmptyController extends CommonController {

    static $controllers = array("Lzz","Article");

    /**
     * 执行过滤
     * @return
     */
    public function _initialize() {
        parent::_initialize();
        $this->ensureExistContoller();
    }

    public function category(){
        $id = $this->getId();
        $Category = M('Category');
        $category = $Category->getById($id);
        if(empty($category)){
            return $this->_empty();
        }
        //获取父类栏目
        $this->assignParetnCategory($category['id']);
        
        $subCategorys = $Category->where("pid = ".$category['id'])->select();
        if(empty($subCategorys)){//没有2级菜单
            $this->createData($category);
            $this->display('Default:'.$category['target_tpl']);
        }else{//当有子栏目时，默认选择第一个子栏目。
            $subCate =  $subCategorys[0];
            $this->createData($subCate);
            $this->display('Default:'.$subCate['target_tpl']);
        }
    }

    public function article_detail(){
        $id =$this->getId();
        $Article = M('Article');
        $article = $Article->getById($id);
        if(empty($article)){
            return $this->_empty();
        }
        $this->pre_next_article($id,$article['category_id']);
        $this->assignParetnCategory($article['category_id']);
        $this->assign('result',$article);

        $keyword = $_GET['keyword'];
        $this->assign('keyword',$keyword);

        //评论
        if($article['open_comment']==1){
            $where['relation_model'] = 'Article';
            $where['relation_id'] = $article['id'];
            $result =$this->getPagination('Comment',$where,'public_time');

            $this->assign('comments', $result['data']);
            $this->assign('pager', $result['pager']);
        }

        $this->display('Default:detail_news');
    }

    public function job_detail(){
        $id = $this->getId();
        $Job = M('Job');
        $job = $Job->getById($id);
        if(empty($job)){
            return $this->_empty();
        }
        $cate_id = $job['category_id'];
        $this->assignParetnCategory($cate_id);
        $this->assign('result',$job);

        $this->display('Default:detail_jobs');
    }

    //评论save
    public function comment(){
        $comment = $_POST['comment'];
        if (!isset($comment)) {
            return $this->errorReturn('无效的操作！');
        }

        $Comment= D('Comment');
        if (false === ($resume = $Comment->create($comment))) {
            return $this->errorReturn($resume->getError());
        }
        $rs = $Comment->add($comment);
        if (false === $rs) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn('评论成功！');
    }

    public function search(){
        $keyword = $_GET['keyword'];
        if (!isset($_GET['keyword'])) {
            return $this->_empty();
        }

        $where = "category_id in (12,13) and (is_slide =2 or is_slide is null) and title like '%".$keyword."%' or content like '%".$keyword."%'";
        $result =$this->getPagination('Article',$where,'public_time desc');
        $this->assign('result', $result['data']);
        $this->assign('pager', $result['pager']);
        $this->assign('keyword',$keyword);
        $this->display('Default:loop_search');
    }

    public function resume_save(){
        $resume = $_POST['resume'];
        if (!isset($resume)) {
            return $this->errorReturn('无效的操作！');
        }

        $Resume = D('Resume');
        if (false === ($resume = $Resume->create($resume))) {
            return $this->errorReturn($Resume->getError());
        }

        $once = false;
        $uploadInfo = null;
        $uploadDir = C('UPLOAD_ROOT') .'photo/';
        if (!$once) {
            // 只执行一次上传
            $uploadInfo = upload($uploadDir);
            if (false === $uploadInfo['status']
                && !empty($uploadInfo['info'])) {
                // 上传失败
                return $this->errorReturn($uploadInfo['info']);
            }
            $once = true;
        }
        if (true === $uploadInfo['status']
            && !$this->isEmpty($_FILES['file']['tmp_name'])
            && is_array($uploadInfo['info'][0])) {
            // 处理真正上传过的file表单域
            $size = $uploadInfo['info'][0]['size'];
            if (convMb2B(2) < $size) {//最大2M
                // 删除已上传的文件
                foreach ($uploadInfo['info'] as $upload) {
                    // 删除文件
                    unlink(WEB_ROOT . $upload['path']);
                }
                // 超过限制大小
                $msg ="文件大小不能超过2M！";
                return $this->errorReturn($msg);
            }

            $resume['photo'] = $uploadInfo['info'][0]['path'];
            array_shift($uploadInfo['info']);

        }
        $resume['read_tag']='未读';
        $rs = $Resume->add($resume);
        if (false === $rs) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn('简历投递成功，请等待人事部联系您！');
    }

    /**
     * 强制确保控制器的CONTROLLER_NAME
     * @return
     */
    protected function ensureExistContoller() {
        if (!in_array(CONTROLLER_NAME, self::$controllers)) {
            return $this->_empty();
        }
    }

    //构建数据结构：单页面或列表页面的数据结构
    private function createData($category){
        $where = "category_id = ".$category['id'];
        $this->assign('category',$category);
        if($category['page_type']==1){
            $M = M($category['relation_model']);
            $results = $M->where($where)->limit(1)->select();
            $this->assign('result',$results[0]);
        }else{
            $result =$this->getPagination($category['relation_model'],$where,'public_time desc',$category['page_size']);
            $this->assign('result', $result['data']);
            $this->assign('pager', $result['pager']);
        }
    }

    //面包屑导航的栏目父类
    private function  assignParetnCategory($cate_id){
        $Category = M('Category');
        $category = $Category->getById($cate_id);
        //获取父类栏目id
        $pid = null;
        if($category['pid']==0 or empty($category['pid'])){
            $pid = $category['id'];
        }else{
            $pid = $category['pid'];
        }
        $this->assign('parent_category',$Category->getById($pid));
        $this->assign('p_cate_id',$pid);
    }


    /**
     * 得到数据分页
     * @param  string $modelName 模型名称
     * @param  array  $where     分页条件
     * @return array
     */
    protected function getPagination($modelName, $where,$order,$pageSize=null) {
        $totalRows = $this->getCount($modelName,$where);
        $M =  M($modelName);
        if (isset($where)) {
            $M = $M->where($where);
        }
        if(empty($pageSize)){
            $pageSize =C('PAGE_LIST_ROWS');
        };
        // 实例化分页
        $page = new \Org\Util\Page($totalRows, $pageSize);
        //$result['show'] = $page->show();
        // 数据排序
        if (isset($order)) {
            $M = $M->order($order);
        }
        // 查询限制
        $firstRow = $page->firstRow;
        $listRows = $page->listRows;
        if (isset($firstRow) && isset($listRows)) {
            $M = $M->limit($firstRow . ',' . $listRows);
        } else if (isset($listRows) && isset($firstRow)) {
            $M = $M->limit($listRows);
        }
        $data = $M->select();
        //echo $M->getLastSql();
        $result['data'] = $data;
        $result['pager']['total'] = $totalRows; //总条数
        $result['pager']['total_pages'] = $page->totalPages; //总页数
        $result['pager']['page_size'] = $listRows; //每页数据
        $result['pager']['page_no'] = $page->nowPage; //页码
        return $result;
    }

    protected function getCount($modelName, $where) {
        $count = M($modelName)->where($where)->count();
        return $count;
    }


    private function getId(){
        $id = $_GET['id'];
        if (!isset($_GET['id'])) {
            return $this->_empty();
        }
        return $id;
    }

    //文章的上一篇和下一篇
    private function pre_next_article($id,$cate_id){
        $M =  M();
        $pre = "select id,title from qt_article where id < ".$id." and category_id =".$cate_id." order by id desc limit 1";
        $next = "select id,title from qt_article where id > ".$id."  and category_id =".$cate_id." order by id  limit 1";
        $pre_result = $M->query($pre);
        if(!empty($pre_result)&&count($pre_result)>0){
            $this->assign('pre_article',$pre_result[0]);
        }
        $next_result = $M->query($next);
        if(!empty($next_result)&&count($next_result)>0){
            $this->assign('next_article',$next_result[0]);
        }
    }

    /**
     * 判断是否为空
     * @param  mixed  $mixed 需要检查的值
     * @return boolean
     */
    private function isEmpty($mixed) {
        if (is_array($mixed)) {
            $mixed = array_filter($mixed);
            return empty($mixed);
        } else {
            return empty($mixed);
        }
    }
}
