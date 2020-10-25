<?php

class DatabaseController extends Controller {

	private $securityKey = '356513040153597';
	private $faceList = array(
		0 => "/呲牙",
		1 => "/调皮",
		2 => "/流汗",
		3 => "/偷笑",
		4 => "/再见",
		5 => "/敲打",
		6 => "/擦汗",
		7 => "/猪头",
		8 => "/玫瑰",
		9 => "/流泪",
		10 => "/大哭",
		11 => "/嘘...",
		12 => "/酷",
		13 => "/抓狂",
		14 => "/委屈",
		15 => "/便便",
		16 => "/炸弹",
		17 => "/菜刀",
		18 => "/可爱",
		19 => "/色",
		20 => "/害羞",
		21 => "/得意",
		22 => "/吐",
		23 => "/微笑",
		24 => "/发怒",
		25 => "/尴尬",
		26 => "/惊恐",
		27 => "/冷汗",
		28 => "/爱心",
		29 => "/示爱",
		30 => "/白眼",
		31 => "/傲慢",
		32 => "/难过",
		33 => "/惊讶",
		34 => "/疑问",
		35 => "/睡",
		36 => "/亲亲",
		37 => "/憨笑",
		38 => "/爱情",
		39 => "/衰",
		40 => "/撇嘴",
		41 => "/阴险",
		42 => "/奋斗",
		43 => "/发呆",
		44 => "/右哼哼",
		45 => "/拥抱",
		46 => "/坏笑",
		47 => "/飞吻",
		48 => "/鄙视",
		49 => "/晕",
		50 => "/大兵",
		51 => "/可怜",
		52 => "/强",
		53 => "/弱",
		54 => "/握手",
		55 => "/胜利",
		56 => "/抱拳",
		57 => "/凋谢",
		58 => "/饭",
		59 => "/蛋糕",
		60 => "/西瓜",
		61 => "/啤酒",
		62 => "/瓢虫",
		63 => "/勾引",
		64 => "/OK",
		65 => "/爱你",
		66 => "/咖啡",
		67 => "/钱",
		68 => "/月亮",
		69 => "/美女",
		70 => "/刀",
		71 => "/发抖",
		72 => "/差劲",
		73 => "/拳头",
		74 => "/心碎",
		75 => "/太阳",
		76 => "/礼物",
		77 => "/足球",
		78 => "/骷髅",
		79 => "/挥手",
		80 => "/闪电",
		81 => "/饥饿",
		82 => "/困",
		83 => "/咒骂",
		84 => "/折磨",
		85 => "/抠鼻",
		86 => "/鼓掌",
		87 => "/糗大了",
		88 => "/左哼哼",
		89 => "/哈欠",
		90 => "/快哭了",
		91 => "/吓",
		92 => "/篮球",
		93 => "/乒乓",
		94 => "/NO",
		95 => "/跳跳",
		96 => "/怄火",
		97 => "/转圈",
		98 => "/磕头",
		99 => "/回头",
		100 => "/跳绳",
		101 => "/激动",
		102 => "/街舞",
		103 => "/献吻",
		104 => "/左太极",
		105 => "/右太极",
		106 => "/闭嘴"
	);

	function __construct()
	{
		parent::Controller();
		//$this->load->database();
	}

	function index()
	{
		$this->load->database();
		$this->load->helper('text');
		
		$r = $this->db->query('show databases')->result();
		
		echo '<style type="text/css">pre{font-family:"Courier New";font-size:14px;}</style>';
		
		$table = printTextTable($r, true);
		printr($table, $this->db);
		
		echo $this->db->getServerVersion();
		echo '<br />';
		echo $this->db->getClientVersion();
	}

	public function get()
	{
		$this->load->database();
		$this->load->helper('text');
		
		$result = $this->db->get('a_wx_stat_clickhistory', '*', null, array('limit'=>20))->result();
		
		header('Content-Type: text/html;charset=utf-8');
		echo "<pre>a_wx_stat_clickhistory\n\n";
		printTextTable($result);
		
		echo "\n\na_wx_stat_clickhistory\n\n";
		$result = $this->db->get('uc_members', '*', null, array('limit'=>20))->result();
		printTextTable($result);
		
		echo "</pre>";
	}

	function update()
	{
		$this->db->update('items',array('a'=>'cvcvc','content'=>'你哪是"AND b=1 OR猪啊'),array('gsf'=>'gudi','fd'=>'甘道"夫'));
		//$this->db->query("UPDATE items SET title='天啊',content='我又看到猪啦~！！！' WHERE id='4'");
	}

	function delete($id)
	{
		$id = intval($id);
		$SQL = "DELETE FROM items WHERE id='$id'";
		$this->db->query($SQL);
	}

	function other()
	{
		$this->load->database();
		$this->db->get('test', '*', array('b.id in'=>array(1,2,3,4,5), 'title %'=>'%MyTitle'), array('having'=>array('a'=>'b')));
	}

	function moredb()
	{
		$this->load->database('default','db1');
		$this->db1->get('user')->row();
		printr($this->db1);
		$this->load->database('ucenter','db2');
		$this->db2->get('settings')->row();
		$this->db1->get('user')->row();
		printr($this->db2,$this->db1);
	}

	function txtSQL()
	{
		$this->load->library('tdb',array('database'=>'test'));

		$this->tdb->selectdb('test');

		$result = array();

		//$result = $this->tdb->get('test.orders','*','','LIMIT 0,25')->result();

		printr($result);

		//$this->tdb->insert('orders',array('title'=>'Hello World','content'=>'猪啊还在睡'));

		$this->tdb->delete('orders',array('state = 1'),'LIMIT 0,100');

		$this->tdb->update('orders',array('state'=>'0'));

		//printr($this->txtSQL);
	}

	public function tinydata()
	{
		$this->load->library('tinydata');

		$row = $this->tinydata->row('guestbook', 100);
	}

	public function sqlite($action='')
	{
		$config = array(
			'filename' => APPLICATION_PATH.'/data/270075658.db',
			'tbprefix' => '',
			'dbdriver' => 'sqlite',
			'debug'    => TRUE
		);

		$this->load->db($config);
		//$this->db->exec("SET NAMES UTF8");

		header('Content-type:text/html;charset=utf-8');

		switch ($action) {
			case 'showtables':
				$master = $this->db->get('sqlite_master', 'name,sql', array('type'=>'table'), array('orderby'=>'name asc'))->result();
				printr($master);
				break;

			case 'sequence':
				$rs = $this->db->get('sqlite_sequence')->result();
				printr($rs);
				break;

			case 'update':
				$this->db->update('mr_friend_38FBACE063B733FD26840F82B113934E', array('msg'=>'tabl\'e','msgseq'=>123), array('_id'=>999));
				break;

			case 'quote':
				echo $this->db->escapeStr("quote' world");
				break;

			case 'select':
				$this->db->get('mr_troop_B8A539724BD59A8E1F9A9EACF481B431');

				$faceCount = count($this->faceList);

				while ($row = $this->db->fetch()) {
					$row['senderuin'] = $this->decode($row['senderuin']);
					$row['frienduin'] = $this->decode($row['frienduin']);
					$row['selfuin'] = $this->decode($row['selfuin']);

					//$row['msg'] = iconv('GB2312', 'UTF-8', $row['msg']);
					//$row['msg'] = mb_convert_encoding($row['msg'], 'UTF-8', 'GB2312');

					$row['ogg'] = $row['msg'];
					$row['msg'] = $this->decode($row['msg']);

					$msg = '';
					$count = strlen($row['msg']);

					$suf = '';
					for ($i=0; $i<$count; $i++) {
						$suf .= ord($row['msg']{$i}).' ';
					}

					for ($i=0; $i<$count; $i++) {
						$char = $row['msg']{$i};
						$ord = ord($char);
						//java: \024
						if ($ord == 20 && $i + 1 < $count && ord($row['msg']{$i + 1}) < $faceCount) {
							$j = ord($row['msg']{$i + 1});
							$str = $this->faceList[$j];
							$row['msg'] = substr_replace($row['msg'], $str, $i, 2);
							$i += strlen($str) - 1;
							$count = strlen($row['msg']);
						}
					}

					if ($row['_id'] == 45) {
						$base = '在深圳的';
						//$base = iconv('UTF-8', 'GBK', $base);

						$row['msg'] .= ' ('.$this->getStrCodeList($row['msg']).')';
						$row['ogg'] .= ' ('.$this->getStrCodeList($row['ogg']).')';
						$row['bse'] = $base.' ('.$this->getStrCodeList($base).')';
					}

					printr($row);
				}
				break;

			case 'char':
				$en = $this->decode('在深圳的');
				echo $en.'<br />';
				echo $this->getStrCodeList($en).'<br />';

				$de = $this->decode($en);
				echo $de.'<br />';
				echo $this->getStrCodeList($de).'<br />';
				break;

			case 'calc':
				break;
		}

		//com\tencent\mobileqq\activity\ChatHistory.java:226
		//com\tencent\mobileqq\utils\SecurityUtile.java

		$this->db->close();

		echo '<hr />';
		printr($this->db->QueryRecords);
	}

	private function getStrCodeList($str)
	{
		$codes = '';
		$count = strlen($str);
		for ($i=0; $i<$count; $i++) {
			$codes .= ord($str{$i}).' ';
		}

		return rtrim($codes);
	}

	private function decode($paramString)
	{
		static $codeKey = '0101';
		static $codeKeyLen = 0;

		if ($codeKeyLen == 0) {
			$codeKey = $this->securityKey;
			$codeKeyLen = strlen($this->securityKey);
		}

		$str = '';
		if ($paramString == null) $str = null;

		$length = strlen($paramString);
		$strList = array();

		if ($codeKeyLen >= $length) {
			for ($i=0; $i<$length; $i++) {
				$strList[$i] = $paramString{$i} ^ $codeKey{$i};
			}
		}

		for ($i=0; $i<$length; $i++) {
			$strList[$i] = $paramString{$i} ^ $codeKey{$i % $codeKeyLen};
		}

		if (count($strList) == 0) {
			$str = '';
		} else {
			$str = join($strList, '');
		}

		return $str;
	}

	public function pdosqlite()
	{
		require_once SYSTEM_PATH.'/database/db_sqlite.php';

		$sqlite = APPLICATION_PATH.'/data/sqlite.db';

		$this->db = new DB_sqlite;

		$this->db->open($sqlite);
		$query = $this->db->query("SELECT * FROM `session`");
		$this->db->showFetchTable($query);
	}

	public function pdo()
	{
		$this->load->database();

		$sess = $this->db->join('nums n', 'n.num=m.uid', 'left')->get('uc_members m','m.uid,m.username,n.num',array('m.uid <'=>31501,'m.uid >'=>31492),array('orderby'=>'m.uid desc','limit'=>10,'offset'=>'0','groupby'=>'m.uid'))->result('uid');
		$explain = $this->db->query('EXPLAIN '.$this->db->LastQuery)->result();

		printr($this->db->QueryRecords, $explain, $sess);
	}

}
