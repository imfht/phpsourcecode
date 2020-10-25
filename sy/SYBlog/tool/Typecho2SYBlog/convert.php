<?php
require(__DIR__ . '/config.php');
require(__DIR__ . '/Parsedown.php');
if (PHP_SAPI === 'cli') {
	define('CLI', TRUE);
} else {
	define('CLI', FALSE);
}
define('ROOT', realpath( __DIR__ . '/../../') . '/');
define('APP', ROOT . 'application/');

if (!is_file(APP . 'config.php')) {
	echo 'Please install SYBlog before using the convert tool';
}

$Parsedown = new Parsedown();

if (!CLI) {
	header('Content-type: text/html; charset=utf-8');
	echo '<pre>';
}
//连接数据库
$dsn = 'mysql:host=' . $config['dbhost'] . ';port=' . $config['dbport'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
try {
	$link = new PDO($dsn, $config['dbuser'], $config['dbpwd']);
} catch (PDOException $e) {
	echo 'Fail to connect to mysql server', "\n";
	echo $e->getMessage();
	exit;
}
//slug
if ($config['slug']) {
	$link->query('DROP TABLE IF EXISTS `' . $config['sy_prefix'] . 'slug`');
	$link->query('CREATE TABLE `' . $config['sy_prefix'] . 'slug` (
  `type` int(1) unsigned DEFAULT \'0\' COMMENT \'0文章1标签或分类\',
  `slug` varchar(255) DEFAULT NULL,
  `id` bigint(10) unsigned DEFAULT NULL,
  KEY `type` (`type`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');
}
//清空已有数据
$link->query('TRUNCATE `' . $config['sy_prefix'] . 'article`');
$link->query('TRUNCATE `' . $config['sy_prefix'] . 'metas`');
$link->query('TRUNCATE `' . $config['sy_prefix'] . 'relation`');
$link->query('TRUNCATE `' . $config['sy_prefix'] . 'attachment`');
//转换meta
$meta = [];
$st = $link->query('SELECT * FROM `' . $config['te_prefix'] . 'metas`');
$st->setFetchMode(PDO::FETCH_ASSOC);
while ($row = $st->fetch()) {
	echo 'Convert type ', $row['mid'], "\n";
	$type = ($row['type'] === 'category' ? 1 : 2);
	$row['name'] = addslashes($row['name']);
	$link->query("INSERT INTO `{$config['sy_prefix']}meta`(`id`,`title`,`type`,`num`) VALUES ('{$row['mid']}','{$row['name']}','$type','{$row['count']}')");
	//slug
	if ($config['slug']) {
		$link->query("INSERT INTO `{$config['sy_prefix']}slug`(`type`,`slug`,`id`) VALUES ('1','{$row['slug']}','{$row['mid']}')");
	}
	$meta[$row['mid']] = $row;
}
//转换文章
$st = $link->query('SELECT * FROM `' . $config['te_prefix'] . 'contents` WHERE type = \'post\'');
$st->setFetchMode(PDO::FETCH_ASSOC);
while ($row = $st->fetch()) {
	echo 'Convert article ', $row['cid'], "\n";
	$row['title'] = addslashes($row['title']);
	//Markdown转HTML
	if (substr($row['text'], 0, 15) === '<!--markdown-->') {
		$body = $Parsedown->text(trim(substr($row['text'], 15)));
	} else {
		$body = $row['text'];
	}
	$body = addslashes($body);
	//Tags
	$tags = '';
	$relationships = $link->query("SELECT * FROM `{$config['te_prefix']}relationships` WHERE cid = '{$row['cid']}'");
	$relationships->setFetchMode(PDO::FETCH_ASSOC);
	while ($relation = $relationships->fetch()) {
		$link->query("INSERT INTO `{$config['sy_prefix']}relation`(`aid`,`mid`) VALUES ('{$relation['cid']}','{$relation['mid']}')");
		$tags .= $meta[$relation['mid']]['name'] . ',';
		echo 'Add relation ', $relation['mid'], ',', $relation['cid'], "\n";
	}
	$tags = addslashes(rtrim($tags, ','));
	//插入数据
	$link->query("INSERT INTO `{$config['sy_prefix']}article`(`id`,`title`,`tags`,`publish`,`modify`,`body`) VALUES ('{$row['cid']}','{$row['title']}','$tags','{$row['created']}','{$row['modified']}','$body')");
	//slug
	if ($config['slug']) {
		$link->query("INSERT INTO `{$config['sy_prefix']}slug`(`type`,`slug`,`id`) VALUES ('0','{$row['slug']}','{$row['cid']}')");
	}
}
//附件

/**
 * 获取文件类型
 * @access public
 * @param string $name
 * @param boolean $is_ext 是否为扩展名
 * @return int
 */
function getFileType($name, $is_ext = FALSE) {
	$img = ['bmp', 'tif', 'psd', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
	$doc = ['doc', 'txt', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'et', 'wps'];
	$pack = ['zip', 'rar', '7z', 'tar', 'gz', 'cab', 'lzma', 'bz2', 'bzip2', 'gzip'];
	$code = ['css', 'js', 'php', 'php3', 'php4', 'php5', 'asp', 'aspx', 'jsp', 'vbs', 'inc', 'c', 'h', 'cpp', 'hpp', 'sql', 'py', 'pl', 'lua', 'java'];
	$media = ['wma', 'wmv', 'wm', 'rm', 'rmvb', 'vob', 'mov', '3gp', 'amr', 'avi', 'mkv', 'mp4', 'mpeg', 'mpg', 'webm', 'ape', 'm4a', 'mid', 'midi', 'mp3', 'ogm', 'ogg', 'flv'];
	if (!$is_ext) {
		$info = pathinfo($name);
		$ext = strtolower($info['extension']);
	} else {
		$ext = strtolower($name);
	}
	if (in_array($ext, $img, TRUE)) {
		return 1; //图片
	} elseif (in_array($ext, $doc, TRUE)) {
		return 2; //文档
	} elseif (in_array($ext, $pack, TRUE)) {
		return 3; //打包文件
	} elseif (in_array($ext, $code, TRUE)) {
		return 4; //代码
	} elseif (in_array($ext, $media, TRUE)) {
		return 5; //媒体文件
	} else {
		return 0; //未知
	}
}
$st = $link->query('SELECT * FROM `' . $config['te_prefix'] . 'contents` WHERE type = \'attachment\' ORDER BY cid ASC');
$st->setFetchMode(PDO::FETCH_ASSOC);
while ($row = $st->fetch()) {
	echo 'Convert attachment ', $row['cid'], "\n";
	$info = unserialize($row['text']);
	$type = getFileType($info['type'], TRUE);
	$url = addslashes($config['attachmentDomain'] . $info['path']);
	$name = addslashes($info['name']);
	$link->query("INSERT INTO `{$config['sy_prefix']}attachment`(`name`,`type`,`size`,`url`,`time`) VALUES ('$name','$type','{$info['size']}','$url','{$row['created']}')");
}
//加入slug函数
if ($config['slug']) {
	$home = file_get_contents(APP . 'controllers/home.php');
	if (strpos($home, '//Tools here') !== FALSE) {
		$home = str_replace('//Tools here', '//Tools here
	public function actionTypecho() {
		$type = $_GET[\'type\'];
		if ($type === \'article\') {
			$id = YMysql::_i()->getOne(\'SELECT id FROM `#@__slug` WHERE type = ? AND slug = ?\', [0, $_GET[\'slug\']]);
			$id = $id[\'id\'];
			Sy::httpStatus(301);
			header(\'Location:\' . Sy::createUrl([\'article/view\', \'id\' => $id]));
			exit;
		}
		if ($type === \'meta\') {
			$id = YMysql::_i()->getOne(\'SELECT id FROM `#@__slug` WHERE type = ? AND slug = ?\', [1, $_GET[\'slug\']]);
			$id = $id[\'id\'];
			$page = intval($_GET[\'page\']);
			if ($page <= 0) {
				$page = 1;
			}
			Sy::httpStatus(301);
			header(\'Location:\' . Sy::createUrl([\'article/list\', \'type\' => \'id\', \'val\' => $id, \'page\' => $page]));
		}
	}', $home);
		if (file_put_contents(APP . 'controllers/home.php', $home) !== FALSE) {
			echo 'Put slug redirector to controllers/home.php successfully', "\n";
		} else {
			echo 'Fail to put slug redirector to controllers/home.php', "\n";
		}
	}
}
if (!CLI) {
	echo '</pre><hr><h5>Convert finished</h5>';
} else {
	echo str_repeat('-', 20);
	echo "\n";
	echo 'Convert finished';
}
?>