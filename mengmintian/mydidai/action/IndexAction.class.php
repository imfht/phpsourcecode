<?php

class IndexAction extends Action{

	public $model;

	public function __construct(){
        


	}
	
	//��ȡ������Ŀ
	public function getNav(){
		$this->nav = new ColumnModel();
        return $this->nav->showNav();
	}
	
	
        //��ȡ������������
        public function getTextLink(){
                $this->flink = new FriendlinkModel();
                return $this->flink->textFriendlink();
        }

        //��ȡͼƬ��������
        public function getPicLink(){
                          $this->flink = new FriendlinkModel();
                return $this->flink->picFriendlink();
        }

        //��ҳͼ���Ƽ�
        public function getRecBook(){
                $this->content = new ContentModel();
                return $this->content->RecBook();
                
        }
        
        //获取最新文章
        public function getNewNews(){
            $this->content = new ContentModel();
            return $this->content->NewNews();
        }
        
        public function getOneTopNews(){
            $this->content = new ContentModel();
            return $this->content->OneTopNews();
        }
         
}