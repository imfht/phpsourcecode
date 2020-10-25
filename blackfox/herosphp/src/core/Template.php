<?php
/**
 * 模板编译类。将数据模型导入到模板并输出。
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\core;

use herosphp\files\FileUtils;

class Template {
    /**
     * 通过assign函数传入的变量临时存放数组
     * @var array
     */
    private $templateVar = array();

    /**
     * 模板目录
     * @var string
     */
    private $templateDir = "";

    /**
     * 编译目录
     * @var string
     */
    private $compileDir = "";

    /**
     * 模板编译缓存配置
     * 0 : 不启用缓存，每次请求都重新编译(建议开发阶段启用)
     * 1 : 开启部分缓存， 如果模板文件有修改的话则放弃缓存，重新编译(建议测试阶段启用)
     * -1 : 不管模板有没有修改都不重新编译，节省模板修改时间判断，性能较高(建议正式部署阶段开启)
     * @var int
     */
    private $cache = 0;

    /**
     * 模板编译规则
     * @var array
     */
    private static $tempRules = array(
        /**
         * 输出变量,数组
         * {$varname}, {$array['key']}
         */
        '/{\$([^\}|\.]{1,})}/i' => '<?php echo \$${1}?>',
        /**
         * 以 {$array.key} 形式输出一维数组元素
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'	=> '<?php echo \$${1}[\'${2}\']?>',
        /**
         * 以 {$array.key1.key2} 形式输出二维数组
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'	=> '<?php echo \$${1}[\'${2}\'][\'${3}\']?>',

        //for 循环
        '/{for ([^\}]+)}/i'	=> '<?php for ${1} {?>',
        '/{\/for}/i'    => '<?php } ?>',

        /**
         * foreach key => value 形式循环输出
         * foreach ( $array as $key => $value )
         */
        '/{loop\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s*}/i'   => '<?php foreach ( \$${1} as \$${2} => \$${3} ) { ?>',
        '/{\/loop}/i'    => '<?php } ?>',

        /**
         * foreach 输出
         * foreach ( $array as $value )
         */
        '/{loop\s+\$(.*?)\s+\$([0-9a-z_]{1,})\s*}/i'	=> '<?php foreach ( \$${1} as \$${2} ) { ?>',
		'/{\/loop}/i'	=> '<?php } ?>',

        /**
         * {run}标签： 执行php表达式
         * {expr}标签：输出php表达式
         * {url}标签：输出格式化的url
         * {date}标签：根据时间戳输出格式化日期
         * {cut}标签：裁剪字指定长度的字符串,注意截取的格式是UTF-8,多余的字符会用...表示
         */
        '/{run\s+(.*?)}/i'   => '<?php ${1} ?>',
        '/{expr\s+(.*?)}/i'   => '<?php echo ${1} ?>',
        '/{url\s+(.*?)}/i'   => '<?php echo url("${1}") ?>',
        '/{date\s+(.*?)(\s+(.*?))?}/i'   => '<?php echo $this->getDate(${1}, "${2}") ?>',
        '/{cut\s+(.*?)(\s+(.*?))?}/i'   => '<?php echo $this->cutString(${1}, "${2}") ?>',

        /**
         * if语句标签
         * if () {} elseif {}
         */
        '/{if\s+(.*?)}/i'   => '<?php if ( ${1} ) { ?>',
        '/{else}/i'   => '<?php } else { ?>',
        '/{elseif\s+(.*?)}/i'   => '<?php } elseif ( ${1} ) { ?>',
        '/{\/if}/i'    => '<?php } ?>',

        /**
         * 导入模板
         * require|include
         */
        '/{(require|include)\s{1,}([0-9a-z_\.\:]{1,})\s*}/i'
							=> '<?php include $this->getIncludePath(\'${2}\')?>',

        '/{(res)\s+([^\}]+)\s*}/i'
        => '<?php echo $this->getResourceURL(\'${2}\')?>',

        /**
         * 引入静态资源 css file,javascript file
         */
        '/{(res):([a-z]{1,})\s+([^\}]+)\s*}/i'
							=> '<?php echo $this->importResource(\'${2}\', "${3}")?>'
	);

    /**
     * 模板编译配置
     * @var array
     */
    private $configs = array();

    /**
     * 静态资源模板
     * @var array
     */
    private static $resTemplate = array(
		'css'	=> "<link rel=\"stylesheet\" type=\"text/css\" href=\"{url}\" />\n",
		'less'	=> "<link rel=\"stylesheet/less\" type=\"text/css\" href=\"{url}\" />\n",
		'js'	=> "<script charset=\"utf-8\" type=\"text/javascript\" src=\"{url}\"></script>\n"
	);

	/**
	 * 构造函数
	 */
	public function __construct() {

        $webApp = WebApplication::getInstance();
        $this->configs = $webApp->getConfigs();
        $this->cache = $this->configs['temp_cache'];
        //添加用户自定义的模板编译规则
        $this->addRules($this->configs['temp_rules']);

        //初始化模板目录和编译目录
        $request = $webApp->getHttpRequest();
        $this->configs['module'] = $request->getModule();
        $this->configs['action'] = $request->getAction();
        $this->configs['method'] = $request->getMethod();
        $this->templateDir = APP_PATH.'modules/'.$this->configs['module'].'/template/'.$this->configs['template'].'/';
        $this->compileDir = APP_RUNTIME_PATH.'views/'.$this->configs['module'].'/';

	}

	/**
	 * 增加模板替换规则
     * @param array $rules
	 */
    public  function addRules($rules) {
        if ( is_array($rules) && !empty($rules) )
		    self::$tempRules = array_merge(self::$tempRules, $rules);
	}

    /**
     * 将变量分配到模板
     * @param  string $varname
     * @param  string $value 变量值
     */
    public function assign($varname, $value) {
		$this->templateVar[$varname] = $value;
	}

	/**
	 * 获取指定模板变量
	 * @param string $varname 变量名
     * @return mixed
	 */
	public function getTemplateVar($varname) {
		return $this->templateVar[$varname];
	}

    /**
     * 获取所有模板变量
     * @return mixed
     */
    public function getTemplateVars() {
        return $this->templateVar;
    }

	/**
	 * 编译模板
	 * @param 		string 		$tempFile 	 	模板文件路径
	 * @param		string		$compileFile	编译文件路径
	 */
	private function complieTemplate($tempFile, $compileFile) {

        //根据缓存情况编译模板
        if ( !file_exists($compileFile)
            || ($this->cache == 1 && filemtime($compileFile) < filemtime($tempFile))
            || $this->cache == 0 ) {

            //获取模板文件
            $content = @file_get_contents($tempFile);
            if ( $content == FALSE ) {
                if ( APP_DEBUG ) {
                    E("加载模板文件 {".$tempFile."} 失败！请在相应的目录建立模板文件。");
                }
            }
            //替换模板
            $content = preg_replace(array_keys(self::$tempRules), self::$tempRules, $content);
            //生成编译目录
            if ( !file_exists(dirname($compileFile)) ) {
                FileUtils::makeFileDirs(dirname($compileFile));
            }

            //生成php文件
            if ( !file_put_contents($compileFile, $content, LOCK_EX) ) {
                if ( APP_DEBUG ) {
                    E("生成编译文件 {$compileFile} 失败。");
                }
            }
        }

	}

	/**
	 * 显示模板
	 * @param		string		$tempFile		模板文件名称
	 */
	public function display($tempFile=null) {

		//如果没有传入模板文件，则访问默认模块下的默认模板
        if ( !$tempFile ) {
            $tempFile = $this->configs['action'].'_'.$this->configs['method'].EXT_TPL;
        } else {
            $tempFile .= EXT_TPL;
        }

        //判断是否是引用其他模块的模板, {module}:index.html
        if ( ($idx = strpos($tempFile, ":")) !== false ) {
            $module = substr($tempFile, 0, $idx);
            $this->templateDir = str_replace("/modules/{$this->configs['module']}/", "/modules/{$module}/", $this->templateDir);
            $tempFile = substr($tempFile, $idx+1);
        }
        $compileFile = $tempFile.'.php';
		if ( file_exists($this->templateDir.$tempFile) ) {
            $this->complieTemplate($this->templateDir.$tempFile, $this->compileDir.$compileFile);
			extract($this->templateVar);	//分配变量
			include $this->compileDir.$compileFile;		//包含编译生成的文件
		} else {
			if ( APP_DEBUG ) {
                E("要编译的模板[".$this->templateDir.$tempFile."] 不存在！");
            }
		}

	}

	/**
	 * 获取include路径
     * 参数格式说明：app:module.templateName
     * 'home:public.top'
     * 如果没有申明应用则默认以当前的应用为相对路径
	 * @param string $tempPath	        被包含的模板路径
     * @return string
	 */
	public function getIncludePath($tempPath = null) {

	    if ( !$tempPath ) return '';
        //切割module.templateName,找到对应模块的模板
        $pos = strpos($tempPath, '.');
        $module = substr($tempPath, 0, $pos);
        $__path = substr($tempPath, $pos);
        $tempDir = APP_PATH.'modules/'.$module.'/template/'.$this->configs['template'].'/';
        $compileDir = APP_RUNTIME_PATH.'views/'.$module.'/';
        $filename = str_replace('.', '/', $__path).EXT_TPL;   //模板文件名称
        $tempFile = $tempDir.$filename;
        $compileFile = $compileDir.$filename.'.php';
        //编译文件
        $this->complieTemplate($tempFile, $compileFile);
		return $compileFile;
	}

    /**
     * 获取日期
     * @param $time
     * @param $format
     * @return string
     */
    private function getDate($time, $format) {

        if ( !$time ) return '';
        if ( !$format ) $format = 'Y-m-d H:i:s';
        return date($format, $time);
    }

    /**
     * 裁剪字符串，使用utf-8编码裁剪
     * @param $str 要裁剪的字符串
     * @param $length 字符串长度
     * @return string
     */
    private function cutString($str, $length) {

        if ( mb_strlen($str, 'UTF-8') <= $length ) {
            return $str;
        }
        return mb_substr($str, 0, $length, 'UTF-8').'...';
    }

	/**
	 * 引进静态资源如css，js
	 * @param string $type 资源类别
	 * @param string $path 资源路径
     * @return string
	 */
	public function importResource($type, $path) {
        $src = '/static/'.$path;
        $template = self::$resTemplate[$type];
        $result = str_replace('{url}', $src, $template);
        return $result;
	}

    /**
     * 加载资源服务器上的图片或者文件
     * @param $url
     * @return string
     */
    public function getResourceURL($url) {
        if (defined("RES_SERVER_URL")) {
            return RES_SERVER_URL.$url;
        }
        return $url;
    }

	/**
	 * 获取页面执行后的代码
	 * @param	string $tempFile
	 * @return	string $html
	*/
	public function &getExecutedHtml($tempFile) {

		ob_start();
		$this->display( $tempFile );
        $html = ob_get_contents();
		ob_end_clean();
		return  $html;

	}

}
