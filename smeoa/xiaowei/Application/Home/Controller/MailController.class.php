<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/
namespace Home\Controller;

class MailController extends HomeController {
	protected $config = array('app_type' => 'personal');
	private $tmpPath = "Data/Temp/";
	// 过滤查询字段

	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		$map['user_id'] = array('eq', get_user_id());
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$where['name'] = array('like', "%" . $keyword . "%");
			$where['content'] = array('like', "%" . $keyword . "%");
			$where['from'] = array('like', "%" . $keyword . "%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
		}
	}

	//--------------------------------------------------------------------
	//   邮件首页
	//--------------------------------------------------------------------
	public function index() {
		$this -> redirect('Mail/folder', array('fid' => 'inbox'));
	}

	//--------------------------------------------------------------------
	// mailbox 1. 收件箱		folder=1
	// mailbox 2. 已发送		folder=2
	// mailbox 3. 草稿箱		folder=3
	// mailbox 4. 已删除		folder=4
	// mailbox 5. 垃圾邮件	folder=5
	// mailbox 6. 永久删除	is_del=1
	//--------------------------------------------------------------------

	public function folder($fid) {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _get_mail_account(get_user_id());
		$this -> _assign_mail_folder_list();

		$folder_id = $fid;
		$mail_system_folder = array('receve', 'inbox', 'outbox', 'darftbox', 'delbox', 'spambox', 'unread', 'all');
		if (in_array($folder_id, $mail_system_folder)) {
			$folder = $folder_id;
		} else {
			$folder = 'user';
		}

		$where = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($where);
		}
		if ($folder == "receve") {
			$this -> assign("receve", true);
			$folder = "inbox";
			cookie('current_node', 101);
		}

		$this -> assign("folder", $folder);

		switch ($folder) {
			case 'inbox' :
				$this -> assign("folder_name", '收件箱');
				$where['folder'] = array("eq", '1');

				break;
			case 'outbox' :
				$this -> assign("folder_name", '已发送');
				$where['folder'] = array("eq", '2');

				break;
			case 'darftbox' :
				$this -> assign("folder_name", '草稿箱');

				$where['folder'] = array("eq", '3');

				break;
			case 'delbox' :
				$this -> assign("folder_name", '已删除');
				$where['folder'] = array("eq", '4');

				break;
			case 'spambox' :
				$this -> assign("folder_name", '垃圾箱');

				$where['folder'] = array("eq", '5');

				break;
			case 'unread' :
				$this -> assign("folder_name", '未读邮件');

				$where['read'] = array("eq", '0');

				break;
			case 'all' :
				$this -> assign("folder_name", '全部邮件');

				break;
			case 'user' :
				$folder_name = M("UserFolder") -> where("id={$folder_id}") -> getField("name");
				$this -> assign("folder_name", $folder_name);
				$where['folder'] = array('eq', $folder_id);
			default :
				break;
		}

		$model = D('Mail');
		if (!empty($model)) {
			$this -> _list($model, $where, "create_time desc");
		}
		$this -> display();
	}

	//--------------------------------------------------------------------
	// mailbox 1. 收件箱		folder=1
	// mailbox 2. 已发送		folder=2
	// mailbox 3. 草稿箱		folder=3
	// mailbox 4. 已删除		folder=4
	// mailbox 5. 垃圾邮件	folder=5
	// mailbox 6. 永久删除	is_del=1
	//--------------------------------------------------------------------
	public function mark($id, $action) {
		switch ($action) {
			case 'del' :
				$field = 'folder';
				$val = 4;
				$result = $this -> _set_field($id, $field, $val);
				break;
			case 'del_forever' :
				$this -> _del($id);
				break;
			case 'spam' :
				$field = 'folder';
				$val = 5;
				$result = $this -> _set_field($id, $field, $val);
				break;
			case 'readed' :
				$field = 'read';
				$val = 1;
				$result = $this -> _set_field($id, $field, $val);
				break;
			case 'unread' :
				$field = 'read';
				$val = 0;
				$result = $this -> _set_field($id, $field, $val);
				break;
			case 'darft' :
				$field = 'folder';
				$val = 3;
				$result = $this -> _set_field($id, $field, $val);
				break;
			case 'move_to' :
				$field = 'folder';
				$val = I('val');
				$result = $this -> _set_field($id, $field, $val);
				break;
			default :
				break;
		}
		if ($result !== false) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('操作成功!');
		} else {
			//失败提示
			$this -> error('操作失败!');
		}
	}

	function folder_manage() {
		$this -> _user_folder_manage('邮件自定义文件夹', false);
	}

	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	//--------------------------------------------------------------------
	//  写邮件
	//--------------------------------------------------------------------
	function add() {
		$this -> _get_mail_account();
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		//$this -> assign("recent", $this -> _get_recent());
		//添加最近联系人
		$this -> display();
	}

	//--------------------------------------------------------------------
	//  发送邮件
	//--------------------------------------------------------------------
	public function send() {
		$mail_account = $this -> _get_mail_account();

		$title = I('name');
		$body = I('content');

		$to = I('to');
		$cc = I('cc');
		$bcc = I('bcc');

		//$this -> _set_recent($to . $cc . $bcc);

		import("@.ORG.Util.send");
		//从PHPMailer目录导入class.send.php类文件
		$mail = new \PHPMailer(true);
		// the true param means it will throw exceptions on errors, which we need to catch
		$mail -> IsSMTP();
		// telling the namespace Home\Controller;

		try {
			$mail -> Host = $mail_account['smtpsvr'];
			//"smtp.qq.com"; // SMTP server 部分邮箱不支持SMTP，QQ邮箱里要设置开启的
			$mail -> SMTPDebug = false;
			// 改为2可以开启调试
			$mail -> SMTPAuth = true;
			// enable SMTP authentication
			$mail -> Port = 25;
			// set the SMTP port for the GMAIL server
			$mail -> CharSet = "UTF-8";
			// 这里指定字符集！解决中文乱码问题
			$mail -> Encoding = "base64";

			$mail -> Username = $mail_account['mail_id'];
			// SMTP account username
			$mail -> Password = $mail_account['mail_pwd'];
			// SMTP account password
			$mail -> SetFrom($mail_account['email'], $mail_account['mail_name']);
			//发送者邮箱
			$mail -> AddReplyTo($mail_account['email'], $mail_account['mail_name']);
			//回复到这个邮箱

			$arr_to = array_filter(explode(';', $to));
			foreach ($arr_to as $item) {
				if (strpos($item, "dept_") !== false) {
					$arr_tmp = array_filter(explode('|', $item));
					$dept_id = str_replace("dept_", '', $arr_tmp[2]);
					$mail_list = $this -> get_mail_list_by_dept_id($dept_id);
					foreach ($mail_list as $val) {
						$mail -> AddAddress($val["email"], $val["name"]);
						// 收件人
					}
				} else {
					$arr_tmp = explode('|', $item);
					$mail -> AddAddress($arr_tmp[1], $arr_tmp[0]);
					// 收件人
				}
			}

			$arr_cc = array_filter(explode(';', $cc));
			foreach ($arr_cc as $item) {
				if (strpos($item, "dept@group") !== false) {
					$arr_tmp = array_filter(explode('|', $item));
					$dept_id = str_replace("dept_", '', $arr_tmp[2]);
					$mail_list = $this -> get_mail_list_by_dept_id($dept_id);
					foreach ($mail_list as $val) {
						$mail -> AddCC($val["email"], $val["name"]);
						// 收件人
					}
				} else {
					$tmp = explode('|', $item);
					$mail -> AddCC($tmp[1], $tmp[0]);
					// 收件人
				}
			}

			$arr_bcc = array_filter(explode(';', $bcc));
			foreach ($arr_bcc as $item) {
				if (strpos($item, "dept@group") !== false) {
					$arr_tmp = array_filter(explode('|', $item));
					$dept_id = str_replace("dept_", '', $arr_tmp[2]);
					$mail_list = $this -> get_mail_list_by_dept_id($dept_id);
					foreach ($mail_list as $val) {
						$mail -> AddBCC($val["email"], $val["name"]);
						// 收件人
					}
				} else {
					$tmp = explode('|', $item);
					$mail -> AddBCC($tmp[1], $tmp[0]);
					// 收件人
				}
			}

			$mail -> Subject = "=?UTF-8?B?" . base64_encode($title) . "?=";
			//嵌入式图片处理
			if (preg_match('/\/Data\/files\/\d{6}\/.{14}(jpg|gif|png)/', $body, $images)) {
				$i = 1;
				foreach ($images as $image) {
					if (strlen($image) > 20) {
						$cid = 'img' . ($i++);
						$name = $mail -> AddEmbeddedImage(substr($image, 1), $cid);
						$body = str_replace($image, "cid:$cid", $body);
					}
				}
			}

			$mail -> MsgHTML($body);

			$add_file = $_REQUEST['add_file'];
			if (!empty($add_file)) {
				$files = array_filter(explode(';', $add_file));
				foreach ($files as $file) {
					$file_id = think_decrypt($file);
					$vo = M("File") -> find($file_id);
					$mail -> AddAttachment(__ROOT__ . C('DOWNLOAD_UPLOAD.rootPath') . $vo['savepath'] . $vo['savename'], $vo['name']);
				}
			}

			$model = D('Mail');
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			$model -> user_id = get_user_id();
			$model -> folder = 2;
			$model -> read = 1;
			$model -> from = $mail_account['mail_name'] . '|' . $mail_account['email'];
			$model -> reply_to = $mail_account['mail_name'] . '|' . $mail_account['email'];

			if (empty($_POST["id"])) {
				$list = $model -> add();
			} else {
				$model -> create_time = time();
				$list = $model -> save();
			}

			if ($mail -> Send()) {
				cookie('current_node', 105);
				$this -> assign('jumpUrl', U('mail/folder?fid=outbox'));
				$this -> success("发送成功");
			} else {
				$this -> error($mail -> ErrorInfo);
			};
		} catch (phpmailerException $e) {
			echo $e -> errorMessage();
			//Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e -> getMessage();
			//Boring error messages from anything else!
		}
	}

	//--------------------------------------------------------------------
	//   保存草稿箱
	//--------------------------------------------------------------------
	public function save_darft() {
		$mail_account = $this -> _get_mail_account();
		$model = D('Mail');
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> user_id = get_user_id();
		$model -> folder = 3;
		$model -> from = $mail_account['mail_name'] . '|' . $mail_account['email'];
		$model -> reply_to = $mail_account['mail_name'] . '|' . $mail_account['email'];
		if (empty($_POST["id"])) {
			$list = $model -> add();
		} else {
			$list = $model -> save();
		}
		if ($list) {
			$this -> assign('jumpUrl', U('mail/folder?fid=darftbox'));
			$this -> success("操作成功");
		} else {
			$this -> error("操作失败");
		};
	}

	//--------------------------------------------------------------------
	//   显示邮件内容
	//--------------------------------------------------------------------
	public function read($id) {
		$this -> _assign_mail_folder_list();
		$this -> _assign_next_link($id);

		$where['id'] = array('eq', $id);
		$where['user_id'] = array('eq', get_user_id());

		$model = M('Mail');
		$model -> where($where) -> setField('read', '1');

		$vo = $model -> getById($id);

		$this -> assign('vo', $vo);

		$this -> display();
	}

	public function edit($id) {
		$plugin['editor'] = true;
		$plugin['uploader'] = true;
		$this -> assign("plugin", $plugin);

		$this -> _edit($id);
	}

	//--------------------------------------------------------------------
	//   回复，转发邮件内容
	//--------------------------------------------------------------------
	public function reply() {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);

		$type = I('type');
		;
		$this -> assign('type', $type);

		if ($type == "reply") {
			$prefix = "回复:";
		}
		if ($type == "all") {
			$prefix = "回复:";
		}
		if ($type == "forward") {
			$prefix = "转发:";
		}

		$this -> assign('prefix', $prefix);

		$id = I('id');
		$where['id'] = array('eq', $id);
		$where['user_id'] = array('eq', get_user_id());

		$model = M('Mail');
		$model -> where($where) -> setField('read', '1');

		$vo = $model -> getById($id);

		$this -> assign('vo', $vo);
		$this -> display();
	}

	//--------------------------------------------------------------------
	//   接收邮件
	//--------------------------------------------------------------------

	public function receve($user_id = null) {
		set_time_limit(0);
		if (empty($user_id)) {
			$user_id = get_user_id();
		}
		$mail_account = $this -> _get_mail_account($user_id);

		$new = 0;
		session_write_close();
		import("@.ORG.Util.receve");
		$mail_list = array();
		$mail = new \receiveMail();
		$connect = $mail -> connect($mail_account['pop3svr'], '110', $mail_account['mail_id'], $mail_account['mail_pwd'], 'INBOX', 'pop3/novalidate-cert');
		if (!$connect) {
			$connect = $mail -> connect($mail_account['pop3svr'], '995', $mail_account['mail_id'], $mail_account['mail_pwd'], 'INBOX', 'pop3/ssl/novalidate-cert');
		}
		$mail_count = $mail -> mail_total_count();
		if ($connect) {
			for ($i = 1; $i <= $mail_count; $i++) {
				$mail_id = $mail_count - $i + 1;
				$mail_header = $mail -> mail_header($mail_id);
				$where['mid'] = array('eq',$mail_header['mid']);
				$where['user_id']=array('eq',get_user_id());
				$count = M('Mail') -> where($where) -> count();
				if ($count == 0) {
					$model = M("Mail");
					$model -> create($mail_header);					
					if ($model -> create_time < strtotime(date('y-m-d H:i:s')) - 86400 * 30) {
						$mail -> close_mail();
						if ($new > 0) {
							$push_data['type'] = '邮件';
							$push_data['action'] = '';
							$push_data['title'] = '收到' . $new . '封邮件';
							$push_data['content'] = '';
							$push_data['url'] = U('Mail/folder','fid=inbox&return_url=Mail/index');

							send_push($push_data, $user_id);
						}
						return;
					}

					$new++;
					$model -> user_id = $user_id;
					$model -> read = 0;
					$model -> folder = 1;
					$model -> is_del = 0;
					$str = $mail -> get_attach($mail_id);
					$model -> add_file = $this -> _receive_file($str, $model);
					$this -> _organize($model);
					$model -> add();

				} else {
					if ($new == 0) {
						$push_data['type'] = '邮件';
						$push_data['action'] = '';
						$push_data['title'] = '没有新邮件';
						$push_data['content'] = '';
						send_push($push_data, $user_id);
						return;
					}
					$mail -> close_mail();
				}
			}
		}
		$mail -> close_mail();
		if ($new == 0) {
			$push_data['type'] = '邮件';
			$push_data['action'] = '';
			$push_data['title'] = '没有新邮件';
			$push_data['content'] = '';
			send_push($push_data, $user_id);
			return;
		} else {
			$push_data['type'] = '邮件';
			$push_data['action'] = '';
			$push_data['title'] = '收到' . $new . '封邮件';
			$push_data['content'] = '';
			$push_data['url'] = U('Mail/folder','fid=inbox&return_url=Mail/index');
			send_push($push_data, $user_id);
		}
	}

	//--------------------------------------------------------------------
	//   接收邮件附件
	//--------------------------------------------------------------------
	private function _receive_file($str, &$model) {

		$ar = array_filter(explode("?", $str));
		$files = array();
		if (!empty($ar)) {
			foreach ($ar as $key => $value) {
				$ar2 = explode("|", $value);
				$cid = $ar2[0];
				$inline = $ar2[1];
				$file_name = $ar2[2];
				$tmp_name = $ar2[3];

				$files[0]['name'] = $file_name;
				$files[0]['tmp_name'] = $tmp_name;
				$files[0]['size'] = filesize($tmp_name);
				$files[0]['is_move'] = true;
				
				if (!empty($files)) {
					$File = D('File');
					$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
					$info = $File -> upload($files, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));
					if ($inline == "INLINE") {
						$model -> content = str_replace("cid:" . $cid, $info[0]['path'], $model -> content);
					} else {
						$add_file = $add_file . think_encrypt($info[0]['id']) . ';';
					}
				}
			}
		}
		return $add_file;
	}

	private function _organize(&$model) {
		$where['user_id'] = get_user_id();
		$where['is_del'] = 0;
		$list = M("MailOrganize") -> where($where) -> order('sort') -> select();

		foreach ($list as $val) {
			//包含
			if (($val['sender_check'] == 1) && ($val['sender_option'] == 1) && (strpos($model -> from, $val['sender_key']) !== false)) {
				$model -> folder = $val['to'];
				return;
			}
			//不包含
			if (($val['sender_check'] == 1) && ($val['sender_option'] == 0) && (strpos($model -> from, $val['sender_key']) == false)) {
				$model -> folder = $val['to'];
				return;
			}

			//包含
			if (($val['domain_check'] == 1) && ($val['domain_option'] == 1) && (strpos($model -> from, $val['domain_key']) !== false)) {
				$model -> folder = $val['to'];
				return;
			}

			//不包含
			if (($val['domain_check'] == 1) && ($val['domain_option'] == 0) && (strpos($model -> from, $val['domain_key']) == false)) {
				$model -> folder = $val['to'];
				return;
			}

			//包含
			if (($val['recever_check'] == 1) && ($val['recever_option'] == 1) && (strpos($model -> to, $val['recever_key']) !== false)) {
				$model -> folder = $val['to'];
				return;
			}
			//不包含
			if (($val['recever_check'] == 1) && ($val['recever_option'] == 0) && (strpos($model -> to, $val['recever_key']) == false)) {
				$model -> folder = $val['to'];
				return;
			}

			//包含
			if (($val['title_check'] == 1) && ($val['title_option'] == 1) && (strpos($model -> name, $val['title_key']) !== false)) {
				$model -> folder = $val['to'];
				return;
			}
			//不包含
			if (($val['title_check'] == 1) && ($val['title_option'] == 0) && (strpos($model -> name, $val['title_key']) == false)) {
				$model -> folder = $val['to'];
				return;
			}
		}
	}

	//--------------------------------------------------------------------
	//  获取最近联系人
	//--------------------------------------------------------------------
	private function _get_recent() {
		$model = M("Recent");
		$user_id = get_user_id();
		return $model -> where("user_id=$user_id") -> getField("recent");
	}

	//--------------------------------------------------------------------
	//   下载邮件附件，返回文件ID
	//--------------------------------------------------------------------
	private function _real_file($str) {

	}

	//--------------------------------------------------------------------
	//  设置最近联系人
	//--------------------------------------------------------------------
	private function _set_recent($address_list) {
		$user_id = get_user_id();
		$data["user_id"] = $user_id;
		$model = M("Recent");
		$recent = $model -> where("user_id=$user_id") -> getField("recent");
		if (!empty($recent)) {
			$address_list = implode(";", array_unique(array_filter(explode(";", $address_list . $recent, 20), "is_dept")));
			//保留20个数据
			$recent = $model -> where("user_id=$user_id") -> setField("recent", $address_list);
		} else {
			$address_list = implode(";", array_unique(array_filter(explode(";", $address_list, 20), "is_dept")));
			//保留20个数据
			if (!empty($address_list)) {
				$model -> user_id = $user_id;
				$model -> recent = $address_list;
				$model -> add();
			}
		}
	}

	//--------------------------------------------------------------------
	//   上一封 下一封
	//--------------------------------------------------------------------
	private function _assign_next_link($id) {
		$folder_id = M('Mail') -> where("id=$id") -> getField('folder');
		$create_time = M('Mail') -> where("id=$id") -> getField('create_time');

		$model = M("Mail");

		$where['folder'] = array("eq", $folder_id);
		$where['_string'] = "create_time>$create_time";
		$where['user_id'] = array('eq', get_user_id());

		$prev = $model -> where($where) -> field("id,name") -> order('create_time asc') -> limit('1') -> select();
		if ($prev) {
			$prev_id = $prev[0]["id"];
			$title = $prev[0]["name"];
			$url = U('mail/read?id=' . $prev_id);
			$prev_html = "<a id=\"prev_link\" class=\"btn btn-primary\" href=\"$url\" title=\"$title\">上一封</a>";
		} else {
			$prev_html = "<a class=\"btn btn-primary disabled\" onclick=\"javascript:return false;\">上一封</a>";
		}

		$where = array();
		$where['folder'] = array("eq", $folder_id);
		$where['_string'] = "create_time<$create_time";
		$where['user_id'] = array('eq', get_user_id());

		$next = $model -> where($where) -> field("id,name") -> order('create_time desc') -> limit('1') -> select();

		if ($next) {
			$next_id = $next[0]["id"];
			$title = $next[0]["name"];
			$url = U('mail/read?id=' . $next_id);
			$next_html = "<a id=\"next_link\" class=\"btn btn-primary\" href=\"$url\" title=\"$title\">下一封</a>";
		} else {
			$next_html = "<a class=\"disabled btn btn-primary\" onclick=\"javascript:return false;\">下一封</a>";
		}
		$html = $prev_html . $next_html;
		$this -> assign('next_link', $html);
	}

	//--------------------------------------------------------------------
	//  读取邮箱用户数据
	//--------------------------------------------------------------------
	private function _get_mail_account($user_id = null) {
		if (empty($user_id)) {
			$user_id = get_user_id();
		}
		$model = M('MailAccount');
		$list = $model -> field('mail_name,email,pop3svr,smtpsvr,mail_id,mail_pwd') -> find($user_id);
		if (empty($list['mail_name']) || empty($list['email']) || empty($list['pop3svr']) || empty($list['smtpsvr']) || empty($list['mail_id']) || empty($list['mail_pwd'])) {
			$this -> assign('jumpUrl', U('MailAccount/index'));
			cookie('current_node', null);
			$this -> error("请设置邮箱帐号");
			die ;
		} else {
			return $list;
		}
	}

	//--------------------------------------------------------------------
	//  显示自定义文件夹
	//--------------------------------------------------------------------
	public function _assign_mail_folder_list() {
		$model = D("UserFolder");
		$user_folder = $model -> get_folder_list("Mail");
		$system_folder = array( array("id" => 1, "name" => "收件箱"), array("id" => 2, "name" => "已发送"));
		if (!empty($user_folder)) {
			$mail_folder = array_merge($system_folder, $user_folder);
		} else {
			$mail_folder = $system_folder;
		}
		$tree = list_to_tree($mail_folder);
		$this -> assign('folder_list', dropdown_menu($tree));
		$temp = tree_to_list($tree);
		return $temp;
	}

	private function get_mail_list_by_dept_id($id) {
		$dept = tree_to_list(list_to_tree( M("Dept") -> where('is_del=0') -> select(), $id));
		$dept = rotate($dept);
		$dept = implode(",", $dept['id']) . ",$id";
		$model = M("User");
		$where['dept_id'] = array('in', $dept);
		$where['is_del'] = array('eq', 0);
		$where['email'] = array('neq', '');
		$data = $model -> where($where) -> select();
		return $data;
	}

}
?>