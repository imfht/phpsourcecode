<?php
/**
 * 模板引擎类，主要包含两个部分：1. HTML 语义标签，2. 输出表达式(与非输出表达式)
 * 与原生 PHP 类似，本引擎会递归地解析每一个使用 <include> 引入的子模版。
 * 并且，你可以在模板中嵌套原生的 PHP 代码，如果你觉得模板语法满足支撑你的需要。
 * 本引擎会出色地避免大多数 js/css 代码冲突，除了具有二义性的代码。
 * 本引擎支持模板布局结构，使用 __CONTENT__ 作为内容占位符，并且可以使用多个 <layout> 标签
 * 或者将其 data 属性设置为多个文件名来使用多布局。
 */
final class template{
	public static $rootDir = ''; //工程根目录(目录均设置为绝对路径并且以 / 结尾，下同)
	public static $rootDirURL = ''; //工程根目录 URL
	public static $saveDir = ''; //保存目录
	public static $extensions = array('php', 'html', 'htm'); //后缀列表
	public static $extraTags = array(); //额外的标签，应为函数名
	private static $tags =  array('include', 'else', 'elseif', 'break', 'continue', 'goto', 'case', 'default', 'layout'); //普通标签
	private static $endTags = array('if', 'switch', 'for', 'foreach', 'while'); //必须显式关闭的标签
	private static $currentDir = ''; //当前目录

	/**
	 * compile() 编译模板
	 * @static
	 * @param  string $tpl 模板文件名
	 * @return null
	 */
	static function compile($tpl, $isRoot = true){
		$file = str_replace('\\', '/', realpath($tpl));
		$root = &self::$rootDir;
		if(!$root || ($root[0] != '/' && $root[1] != ':'))
			$root = realpath($root); //获取真实工程目录路径
		if($isRoot){ //最顶级调用(用户层)
			self::$currentDir = substr($file, 0, strrpos($file, '/')+1); //设置当前目录
			self::$tags = array_merge(self::$tags, self::$endTags, self::$extraTags); //合并标签
		}
		$ext = pathinfo($file, PATHINFO_EXTENSION); //文件后缀名
		if(!in_array($ext, self::$extensions) || !file_exists($file) || strpos($file, $root) !== 0) return false;
		$path = self::$saveDir.substr($file, strlen($root));
		$path = substr($path, 0, strrpos($path, '.')).'.php'; //编译文件保存路径
		$dir = substr($path, 0, strrpos($path, '/'));
		if($dir && !is_dir($dir)) mkdir($dir, 0777, true);
		$html = self::analyzeHTML(file_get_contents($file)); //解析模板
		return file_put_contents($path, $html) ? str_replace('\\', '/', realpath($path)) : false;
	}

	/**
	 * analyzeHTML() 解析模板内容
	 * @param  string $html 模板内容
	 * @return string       处理后的内容
	 */
	private static function analyzeHTML($html){
		if(!$html) return '';
		$html = self::handleExpression( //处理(非)输出表达式
			self::handleEndTag( //处理必须关闭的标签
				self::stripWhiteSpace($html) //去除空格
				)
			);
		$tags = self::getPHPTags($html); //获取 PHP 标签
		$layoutTags = array();
		if($tags){
			foreach ($tags as $tag) {
				if($tag['tagName'] == 'layout'){
					$layoutTags[] = $tag; //添加布局
				}else{
					$html = self::handleStartTag($tag, $html); //处理开始标签
				}
			}
		}
		if($layoutTags){
			foreach(array_reverse($layoutTags) as $tag) { //反转布局顺序，越靠后越优先(离内容越接近)
				$html = self::handleLayout($tag, $html); //处理布局
			}
		}
		return str_replace(array("\r", "\n\n"), "\n", $html);
	}

	/**
	 * handleLayout() 处理布局
	 * @param  array  $tag  标签信息
	 * @param  string $html HTML
	 * @return string       处理后的内容
	 */
	private static function handleLayout($tag, $html){
		$index = strpos($html, $tag['element']);
		if($index === false) return $html;
		$html = str_replace($tag['element'], '', $html);
		$files = array_reverse(explode(',', $tag['attributes']['data'])); //反转布局文件顺序，越靠后越优先(离内容越接近)
		foreach($files as $file){
			$file = trim($file);
			if($file[0] != '/' && $file[1] != ':'){
				$file = self::$currentDir.$file; //获取布局文件的绝对路径
			}
			if(strpos($file, self::$rootDir) === 0){
				$file = self::compile($file, false); //编译布局文件
			}
			if($file){
				//将布局文件中的内容占位符替换为模板内容
				$layout = file_get_contents($file);
				if(preg_match('/<!--[\s]*CONTENT[S\s]*-->|__CONTENT[S]*__/Ui', $layout, $match)){
					$layout = explode($match[0], $layout);
					$html = substr($html, 0, $index).$layout[0].substr($html, $index).$layout[1];
				}
			}
		}
		return $html;
	}

	/**
	 * handleStartTag() 处理开始标签
	 * @param  array  $tag  标签内容
	 * @param  string $html HTML
	 * @return string       处理后的内容
	 */
	private static function handleStartTag($tag, $html){
		$tagName = $tag['tagName'];
		$attrs = $tag['attributes'];
		$noDataTags = array('else', 'default', 'break', 'continue');
		if(empty($attrs['data']) && !in_array($tagName, array_merge($noDataTags, self::$extraTags))){
			return str_replace($tag['element'], '', $html);
		}
		foreach ($attrs as $k => $v) {
			$attrs[$k] = preg_replace_callback('/<\?php echo(.*)[;]*\?>/U', function($match){
				return eval("return {$match[1]};"); //计算属性中的 PHP 代码，并用结果进行替换
			}, $v);
		}
		if($tagName == 'include'){ //include 语句
			$code = array();
			foreach (explode(',', $attrs['data']) as $file) {
				$file = trim($file);
				if($file[0] != '/' && $file[1] != ':'){
					$file = self::$currentDir.$file;
				}
				if(strpos($file, self::$rootDir) === 0){
					$file = self::compile($file, false); //编译引入文件
				}
				if($file) $code[] = "include '".substr($file, 0, strrpos($file, '.')).".php'";
			}
			$code = implode('; ', $code);
		}elseif($tagName == 'case'){ //case 语句
			$code = explode(',', $attrs['data']);
			foreach ($code as &$v) {
				$v = 'case '.trim($v).':';
			}
			$code = implode(' ', $code);
		}elseif($tagName == 'default' || $tagName == 'else'){ //default/else 语句
			$code = "{$tagName}:";
		}elseif(in_array($tagName, $noDataTags) || $tagName == 'goto'){ //无参数语句和 goto 语句
			$code = $tagName.(!empty($attrs['data']) ? ' '.$attrs['data'] : '').';';
		}elseif(in_array($tagName, self::$endTags) || $tagName == 'elseif'){ //必须关闭的语句和 elseif 语句
			$code = "{$tagName}({$attrs['data']}):";
		}else{
			if(!empty($attrs['data'])){
				$args = explode(',', $attrs['data']);
				foreach ($args as &$v) {
					$v = trim($v);
					$v = defined($v) ? $v : '"'.str_replace('"', '\"', $v).'"';
				}
				$args = implode(',', $args);
			}else{
				$args = '';
			}
			$code = "{$tagName}($args);"; //用户自定义标签
		}
		return str_replace($tag['element'], $code ? "<?php $code ?>" : '', $html); //替换标签为 PHP 代码
	}

	/**
	 * handleEndTag() 处理结束标签
	 * @param  string $html 模板内容
	 * @return string       处理后的内容
	 */
	private static function handleEndTag($html){
		$html = str_ireplace(array('</case>', '</default>'), '<?php break; ?>', $html);
		$tags = $endTags = $_endTags = array();
		foreach (self::$tags as $v) {
			$tags[] = '</'.$v.'>'; //普通结束标签
		}
		foreach (self::$endTags as $v) {
			$endTags[] = '</'.$v.'>'; //必须关闭的结束标签
			$_endTags[] = '<?php end'.$v.'; ?>'; //必须关闭的结束标签对应的 PHP 代码
		}
		$html = str_ireplace($endTags, $_endTags, $html); //替换必须关闭的标签为 PHP 代码
		return str_ireplace($tags, '', $html); //移除将普通结束标签
	}

	/**
	 * handleExpression() 处理表达式，表达式包裹形式为 {$var} 输出; !{$var} 非输出
	 * @param  string $html 模板内容
	 * @return string       处理后的内容
	 */
	private static function handleExpression($html){
		$regexp = array(
			'/[!]{0,1}\{([!]*[\@$_a-zA-Z0-9\("\'\\\\][\s\S]*)\}/U', //表达式
			'/[!]{0,1}\{[!]*[$_a-zA-Z0-9\-"\']+[\s]*:[$_a-zA-Z0-9\s"\'\-][\s\S]*\}/U' //不合法表达式
			);
		if(preg_match_all($regexp[0], $html, $exps)){ //获取所有表达式
			foreach ($exps[0] as $exp) {
				if(!preg_match($regexp[1], $exp)){ //判断表达式是否合法
					$echo = $exp[0] != '!' ? 'echo ' : ''; //是否输出
					$i = $echo ? 1 : 2;
					$code = '<?php '.$echo.substr($exp, $i, strlen($exp)-$i-1).'; ?>'; //PHP 代码
					$html = str_replace($exp, $code, $html); //替换表达式
				}
			}
		}
		return $html;
	}

	/** stripWhiteSpace() 去除空格 */
	private static function stripWhiteSpace($html){
		return preg_replace(array(
			'/[\s\t]+=[\s\t]+(["\'])+/U', //标签属性等号两边的空格
			'/[\r\n]+[\t\s]+([\S]*)/' //行前空白
		), array('=$1', "\n$1"), $html);
	}

	/** hasPHPTag() 判断是否有 PHP 标签 */
	private static function hasPHPTag($html, &$tagName){
		$bool = preg_match('/<('.join('|', self::$tags).')\b/Ui', $html, $result);
		$tagName = isset($result[1]) ? $result[1] : '';
		return $bool;
	}

	/**
	 * getAttr() 获取标签属性
	 * @param  string $tag  元素标签
	 * @param  string $left 剩余内容，可能包含另一个标签
	 * @return array        属性数组
	 */
	private static function getAttr($tag, &$left, &$len = 0, $tagName){
		static $attrs = array();
		$str = ltrim($tag, "\n\r\"'/ "); //去除多余字符
		if($str[0] == '<'){ //标签开始
			$left = '';
			$len = 1;
			$attrs = array();
		}
		$i = strpos($tag, $str);
		$len += $i ? $i-1 : 0;
		if(strpos($str, '<'.$tagName) === 0){ //标签开始
			$_str = ltrim(substr($str, strlen($tagName)+1), ' /');
			$i = strpos($str, $_str);
			$len += $i ? $i: 0;
			$str = $_str;
		}
		if($str[0] == '>'){ //标签结尾
			$left = substr($str, 1);
			return $attrs;
		}
		if(strpos($str, '=') === false){ //无(剩余)属性
			$i = strpos($str, '>');
			$left = substr($str, $i+1);
			$len += $i;
			return $attrs;
		}
		$_str = strstr($str, '=', true);
		$__str = strstr($str, '=');
		$attr = trim(substr($_str, strrpos($_str, ' '))); //属性
		$i = strpos(ltrim($__str, '='.$__str[1]), $__str[1]); //属性值尾部引号位置，$__str[1] 为引号
		$value = substr($__str, 2, $i); //属性值
		$len += strlen($_str)+strlen($value)+3;
		$attrs[$attr] = $value; //保存属性
		$str = substr($__str, $i+2);
		if(ltrim($str, "\n\r\"'/ ")){
			self::getAttr($str, $left, $len, $tagName); //递归运算
		}
		$left = $left ? ltrim($left, "\n\r\"'/ >") : '';
		return $attrs;
	}

	/**
	 * getPHPTags() 获取 PHP 元素
	 * @param  string $html 模板内容
	 * @param  bool   $isFirst 是否为第一个元素
	 * @return array        元素数组
	 */
	private static function getPHPTags($html, $isFirst = true){
		static $tags = array();
		static $i = 0;
		if($isFirst){
			$tags = array();
			$i = 0;
		}
		if(self::hasPHPTag($html, $tagName)){
			$str = trim($html);
			$_i = strpos($str, '<'.$tagName); //标签位置
			$html = substr($str, $_i);
			$b = $html[strlen('<'.$tagName)];
			if($b != ' ' && $b != '/' && $b != '>'){ //略过开头相同的标签，如 iframe，它与 if 开头相同
				$_i = strpos($html, '>');
				$html = substr($html, $_i+1);
				self::getPHPTags($html, $isFirst);
			}else{
				$tags[$i]['element'] = '';
				$tags[$i]['tagName'] = strtolower($tagName);
				$tags[$i]['attributes'] = self::getAttr($html, $left, $len, $tagName);
				if($left){
					$tags[$i]['element'] = trim(substr($str, $_i, $len));
					$i++;
					self::getPHPTags($left, false); //递归运算
				}else{
					$tags[$i]['element'] = trim(substr($str, $_i));
				}
			}
		}
		return $tags;
	}
}