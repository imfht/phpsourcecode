<?php
/*
	CREATE TABLE `orders` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`title` varchar(50) NOT NULL,
		`timer` timestamp NOT NULL default CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	INSERT INTO `orders` VALUES (NULL, 'Hello World', '2009-11-26 22:59:54');
*/

class MultipageController extends Controller {

	function __construct()
	{
		parent::Controller();
		$this->load->database();
		$this->load->library('pagination');
	}

	function index($page=1)
	{
		echo $this->css();
		echo '<h4>Multipage</h4>';
		
		//$r = $this->db->query('SELECT COUNT(*) AS count FROM orders')->row();
		$this->pagination->initialize(array(
			'curPage' => intval($page),
			'pageUrl' => 'test/multipage/index/*',
			'perPage' => 10,
			'totalRows' => 299
		));
		
		$start = $this->pagination->getStart();
		
		//$res = $this->db->query("SELECT * FROM orders ORDER BY id DESC LIMIT $start,10")->result();
		
		//echo '<table cellpadding="1" cellspacing="1" width="400" border="1">';
		//foreach($res as $row) {
		//	echo '<tr>'
		//		.'<td>'.$row['id'].'</td>'
		//		.'<td>'.$row['title'].'</td>'
		//		.'<td>'.$row['timer'].'</td>'
		//	.'</tr>';
		//}
		//echo '</table>';
		
		
		echo $this->pagination->makeLinks();
	}

	private function css()
	{
		return '<style type="text/css">
.fpage {padding:3px; margin:3px;}
.fpage a,.fpage b {border:#eee 1px solid; padding-top:2px; padding-right:5px; padding-bottom:2px; padding-left:5px; margin:2px; color:#036cb4; text-decoration:none; outline-style:none;}
.fpage a:hover {border:#999 1px solid; background-color:#FCFFFF; text-decoration:none;}
.fpage b {border:#036cb4 1px solid; padding-top:2px 5px 2px 5px; font-weight:bold; margin:2px; color:#fff; background-color:#036cb4;}
.fpage input {border:#1586D6 1px solid; color:#036CB4; vertical-align:middle; text-align:center;}
</style>';
	}

}
