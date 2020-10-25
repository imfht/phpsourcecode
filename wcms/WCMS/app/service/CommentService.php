<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 11:22
 */
Class CommentService{


    public function addComment($param){

        $arrIp = $this->getIp ();
        $param ['ip'] = $arrIp;
        $param ['date'] = time ();
        $ret=CommentModel::instance()->addComment($param);
        if($ret>0){
            return array('status'=>true,'message'=>"SUCCESS");
        }
        return array('status'=>false,'message'=>"FAILD");
    }


    public function getAllComment(){
        return CommentModel::instance()->getAllComment();
    }

    /**
     * 获得ip地址
     *
     * @return array $arrIp (ip country area)
     */
    protected function getIp() {
        $ip = new IpLocation ();
        return $ip->getIP ();
    }

    public function removeCommentById($id){
        return CommentModel::instance()->removeCommentById($id);
    }

}

class CommentModel extends Db{

    private $_table='w_news_comment';

    public function addComment($params){
        return $this->add($this->_table,$params);
    }

    public function getAllComment(){
        return $this->getAll($this->_table,null,null,'id desc');
    }

    public function removeCommentById($id){
        return $this->delete($this->_table,array('id'=>$id));
    }

    /**
     * @return CommentModel
     */
    public static function instance(){
        return parent::_instance(__CLASS__);
    }

}