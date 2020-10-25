<?php
/** 自动修复程序 */
/** 恢复目录 */
$tmp = session_save_path();
if(config("mod.session.savePath") && !is_dir($tmp)) mkdir($tmp, 0777, true); //Session 目录
if(!is_dir($tpl = template_path())) mkdir($tpl, 0777, true); //模板目录
if(!is_dir($upl = __ROOT__.config('file.upload.savePath'))) mkdir($upl, 0777, true); //文件上传目录
if(!is_dir($dir = __ROOT__.'user/')) mkdir($dir); //用户目录
if(!is_dir($dir = __ROOT__.'user/classes/')) mkdir($dir); //用户类库目录
if(!is_dir($dir = __ROOT__.'user/functions/')) mkdir($dir); //用户函数目录
if(!is_dir($cdir = __ROOT__.'user/config/')) mkdir($cdir); //用户配置目录
if(!is_dir($ldir = __ROOT__.'user/lang/')) mkdir($ldir); //用于语言包目录

/** 恢复所有文件 */
if(!MOD_ZIP && file_exists($zip = __ROOT__.'modphp.zip') && extension_loaded('zip')){
	foreach(zip_list($zip, false) as $file){
		if(!file_exists(__ROOT__.$file)){
			file_put_contents(__ROOT__.$file, file_get_contents('zip://'.$zip.'#'.$file));
		}
	}
}

//Session 保存目录的 .htaccess，禁止客户端访问该目录
if(path_starts_with($tmp, __ROOT__) && !file_exists($file = $tmp.'.htaccess')){
	file_put_contents($file, "order deny,allow\ndeny from all");
}

/** 恢复默认首页 */
if(!file_exists($file = $tpl.config('site.home.template'))){
	file_put_contents($file, file_get_contents(__CORE__.'common/index.php'));
}

/** 恢复用户配置文件 */
foreach ($GLOBALS['CORE'.INIT_TIME] as $file) {
	if(strpos($file, 'config/') === 0 && !file_exists($cdir.basename($file))){
		file_put_contents($cdir.basename($file), file_get_contents(__CORE__.$file));
	}
}

/** 恢复语言包文件 */
$file = strtolower(config('mod.language')).'.php';
if(!file_exists($ldir.$file)){
	file_put_contents($ldir.$file, file_get_contents(__CORE__.'lang/'.$file));
}

/** 恢复自定义模块类文件 */
if(config('mod.installed')){
	foreach (array_keys(database()) as $table) {
		$coreFile = 'classes/'.$table.'.class.php';
		$userFile = __ROOT__.'user/classes/'.$table.'.class.php';
		if(!in_array($coreFile, $GLOBALS['CORE'.INIT_TIME]) && !file_exists($userFile)){
			$data = '<?php
final class '.$table.' extends mod{
	const TABLE = "'.$table.'";
	const PRIMKEY = "'.get_primkey_by_table($table).'";
}';
			file_put_contents($userFile, $data); //写出类文件
		}
	}
}