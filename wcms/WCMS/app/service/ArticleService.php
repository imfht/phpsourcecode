<?php
/**
 * 文章系统
 * User: Administrator
 * Date: 2018/8/24
 * Time: 10:55
 */
class ArticleService{

    private $_limit=20;
    private $_thumb_width=800;
    private $_thumb_height=800;


    /**
     * 获取指定分类
     * @param $cid
     * @param $p
     * @return array
     */
    public function getNewsByCid($cid,$p){
        $total=ArticleModel::instance()->getNewsNumByCid($cid);
        $page=$this->page($total,$p,$this->_limit);
        $rs= ArticleModel::instance()->getNewsByCid($cid,$page['start'],$this->_limit);
        return array('data'=>$rs,'page'=>$page);
    }

    /**
     * 获取所有信息
     * @param $cid
     * @param $p
     * @return array
     */
    public function getNewsByPage($p){
        $total=ArticleModel::instance()->getNewsNum();
        $page=$this->page($total,$p,$this->_limit);
        $rs= ArticleModel::instance()->getAllNews($page['start'],$this->_limit);
        return array('data'=>$rs,'page'=>$page);
    }


    /**
     * @param field_type $_con
     */
    public function addCon($data) {

        $uploadfile = $this->thumb ( $_FILES ['thumb'] ); // 检查是否存在缩略图
        $filePath = $uploadfile ['message'];
        $content=$data['content'];
        unset ( $data ['content'] );
        /* bug fix 2013-4-23 增加替换全角字符 */

        if (! empty ( $filePath )) {
            $thumb = $this->reduceImage ( $filePath,$this->_thumb_width, $this->_thumb_height) ;
            $data ['thumb'] = $thumb;
            $data ['pic'] = $filePath;
        }
        $data ['date'] = time();

        $lasterinsertId=ArticleModel::instance()->addNewsList($data);

        ArticleModel::instance()->addNewsCon(array('nid'=>$lasterinsertId,'content'=>$content));
    }

    /**
     * @param field_type $_con
     */
    public function saveCon($data) {

        $uploadfile = $this->thumb ( $_FILES ['thumb'] ); // 检查是否存在缩略图
        $filePath = $uploadfile ['message'];
        $content=$data['content'];
        $id=$data['id'];
        unset($data['id']);
        unset ( $data ['content'] );
        unset($data['date']);
        /* bug fix 2013-4-23 增加替换全角字符 */

        if (! empty ( $filePath )) {
            $thumb = $this->reduceImage ( $filePath,$this->_thumb_width, $this->_thumb_height) ;
            $data ['thumb'] = $thumb;
            $data ['pic'] = $filePath;
        }

        ArticleModel::instance()->saveNewsListById($data,$id);
        ArticleModel::instance()->saveNewsConByNid(array('content'=>$content),$id);
    }

    /**
     * 对接ckeditor
     * 注意上传文件大小限制
     *
     * @return string $filePath 图片路径
     */
    public function upload ($type)
    {
        $file = $this->thumb($_FILES['upload'], 'image');
        if ($type) {
            echo json_encode($file);
            return;
        }
        $callback = $_REQUEST["CKEditorFuncNum"];
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'" .
            $file['message'] . "','');</script>";
    }


    public function thumb ($files, $dir = "thumb")
    {
        $imagetrans = new Image();
        return $imagetrans->upload($files, $dir,false);
    }

    /**
     * 生成缩略图
     *
     * @param unknown_type $id
     */
    protected function reduceImage ($imagefile, $thumbWidth, $thumbHeight)
    {
        $imagetrans = new Image();
        return $imagetrans->reduceImage($imagefile, $thumbWidth, $thumbHeight);
    }

    public function getAllNews(){
        return ArticleModel::instance()->getAllNews();
    }

    public function removeNewsById($id){
        ArticleModel::instance()->removeNewsById($id);
        ArticleModel::instance()->removeNewsConByNid($id);
    }

    public function getNewsById($id){
        return ArticleModel::instance()->getNewsById($id);
    }

    /**
     * 分页
     *
     * @return void
     */
    private function page($total, $pageid, $num) {
        $pageid = isset ( $pageid ) ? $pageid : 1;
        $rs = $pageid * $num - $total;
        $start = ($pageid - 1) * $num;
        $pagenum = ceil ( $total / $num );
        /*修正分类不包含内容 显示404错误*/
        $pagenum = $pagenum == 0 ? 1 : $pagenum;
        /*如果超过了分类页数 404错误*/
        if ($pageid > $pagenum) {
            header ( "HTTP/1.1 404 Not Found" );
        }
        $page = array ('start' => $start, 'num' => $num, 'current' => $pageid, 'page' => $pagenum );
        return $page;
    }

}

class ArticleModel extends  Db{


    private $_news_list='w_news_list';
    private $_news_con='w_news_content';

    public function getAllNews($start,$num){
        $sql="SELECT a.*,b.name FROM $this->_news_list a left join w_news_category b on a.cid=b.id ORDER BY a.id DESC LIMIT $start,$num";
        return $this->fetchAll($sql);
    }

    public function getNewsById($id){
        $sql="SELECT a.*,c.name,b.content FROM $this->_news_list a left join $this->_news_con b on a.id=b.nid  left join w_news_category c on a.cid=c.id WHERE a.id=$id";
        return $this->fetch($sql);
    }

    public function getNewsNumByCid($cid){
        $sql="SELECT id FROM $this->_news_list WHERE cid=$cid";
        return $this->rowCount($sql);
    }

    public function getNewsNum(){
        $sql="SELECT id FROM $this->_news_list";
        return $this->rowCount($sql);
    }

    public function removeNewsById($id){
        return $this->delete($this->_news_list,array('id'=>$id));
    }

    public function removeNewsConByNid($nid){
        return $this->delete($this->_news_con,array('nid'=>$nid));
    }


    public function getNewsByCid($cid,$start,$num){
        $sql="SELECT a.*,b.name FROM $this->_news_list a left join w_news_category b on a.cid=b.id WHERE a.cid=$cid ORDER BY a.id DESC LIMIT $start,$num";
        return $this->fetchAll($sql);
    }


    public function addNewsList($params){
        return $this->add($this->_news_list,$params);
    }


    public function saveNewsListById($params,$id){
        return $this->update($this->_news_list,$params,array('id'=>$id));
    }

    public function saveNewsConByNid($params,$id){
        return $this->update($this->_news_con,$params,array('nid'=>$id));
    }

    public function addNewsCon($params){
          return $this->add($this->_news_con,$params);
    }


    /**
     * @return ArticleModel
     */
    public static function instance(){
        return parent::_instance(__CLASS__);
    }



}