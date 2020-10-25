<?php
    /**
	 * @author swu_mao
	 * @time 2014.12.29 3:15
	 * message 表的model
	 * message 表的结构如下：
	 * +---------------------------------------------+
	 * | mid |  title   |  content  |   time   | uid |
	 * +---------------------------------------------+
	 * | int | char(45) | char(140) | char(45) | int |
	 * +---------------------------------------------+
	 * 建表语句为：
	 *	 CREATE TABLE `message`.`message_message` (
	 *	  `mid` INT NOT NULL AUTO_INCREMENT,
	 *	  `title` VARCHAR(45) NOT NULL,
	 *	  `content` VARCHAR(140) NOT NULL,
	 *	  `time` VARCHAR(45) NOT NULL,
	 *	  `uid` INT NOT NULL,
	 *	  PRIMARY KEY (`mid`));
	*/
	class MessageModel extends Model{
		/**
		 * @param $title--->留言标题，$content--->留言内容，$uid--->留言者uid
		 * @return 保存成功返回true，失败返回false
		 * 添加一条记录
		*/
		public function addMessage($title, $content, $uid){
			$time = date("Y.m.d");
			$data = array();
			$data['title'] = $title;
			$data['content'] = $content;
			$data['uid'] = $uid;
			$data['time'] = $time;
			if($this->add($data)){
				return true;
			}
			return false;
		}

		/**
		 * @param
		 * @return all messages
		 * 得到所有的message
		*/
		public function getAllMessage(){
			return $this->select();
		}
	}
?>