<?php
    /**
	 * @author swu_mao
	 * @time 2014.12.29 1:40
	 * user 表的modle类 
	 * user 表的结构为：
	 * +--------------------------------------+
	 * | uid | username | password |   name   |
	 * +--------------------------------------+
	 * | int | char(20) | char(99) | char(20) |
	 * +--------------------------------------+
	 * 建表语句为：
	 * CREATE SCHEMA `message` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
	 * CREATE TABLE `message`.`message_user` (
	 *  `uid` INT NOT NULL AUTO_INCREMENT,
	 *  `username` VARCHAR(45) NOT NULL,
	 *  `password` VARCHAR(45) NOT NULL,
	 *  `name` VARCHAR(45) NOT NULL,
	 *  PRIMARY KEY (`uid`))
	 * ENGINE = InnoDB
	 * DEFAULT CHARACTER SET = utf8
	 * COLLATE = utf8_general_ci;
	*/
	class UserModel extends Model{
		/**
		 * @param $username--->用户名, $password--->密码, $name--->姓名
		 * @return 添加成功返回true，失败返回false
		 * 添加用户，成功返回true，失败返回false
		*/
		public function addUser($username, $password, $name){
			if($this->isExist($username)){
				$data = array();
				$data['username'] = $username;
				$data['password'] = md5($password);
				$data['name'] = $name;
				if($this->add($data)){
					return ture;
				}
			}
			return false;
		}

		/**
		 * @param $username--->用户名
		 * @return true  false
		 * 检查用户名为参数的用户是否已经存在于数据库中，存在返回false，不存在返回true
		*/
		public function isExist($username){
			if($this->where(array('username'=>$username))->find()!=NUll){
				return false;
			}
			return true;
		}

		/**
		 * @param $username--->用户名   $password--->密码
		 * @return boolean
		 * 检测数据库中是否存在匹配参数的用户
		*/
		public function checkUser($username, $password){
			$res = $this->where(array('username'=>$username))->select();
			if($res != NULL){
				if($res[0]['password']!=md5($password)){
					return false;
				}
				return true;
			}
		}

		/**
		 * @param $username--->用户名
		 * @return name--->姓名
		 * 根据用户名得到用户姓名
		*/
		public function getName($username){
			return $this->where(array('username'=>$username))->getField('name');
		}

		/**
		 * @param $username--->用户名
		 * @return uid--->用户uid
		 * 根据用户名得到uid
		*/
		public function getUid($username){
			return $this->where(array('username'=>$username))->getField('uid');
		}

		/**
		 * @param $uid--->用户id
		 * @return name--->用户姓名
		 * 根据用户id得到用户姓名
		*/
		public function getNameById($uid){
			return $this->where(array('uid'=>$uid))->getField('name');
		}
	}
?>