<?php
/**
 * TookPHP模板引擎
 *
 * @package     TookPHP模板
 * @subpackage  Driver
 * @author      lajox <lajox@19www.com>
 */
namespace Took\View\Driver;

class Tk extends \Took\View
{

    private $left; //标签左符号
    private $right; //标签右符号
    private $condition = array('neq'=>'<>', 'eq'=>'==','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<='); //比较运算符
    private $view; //视图对象
    private $aliasFunction = array( 'default' => '_default' ); //别名函数
    private $literal = array(); //不解析内容 (先将不解析内容放入数组中, 然后将从内容替换)

    /**
     * block 块标签, level 嵌套层次
     */
    public $Tag = array(
        'foreach'   => array('block' => 1, 'level' => 5),
        'list'      => array('block' => 1, 'level' => 5),
        'if'        => array('block' => 1, 'level' => 5),
        'elseif'    => array('block' => 0, 'level' => 0),
        'else'      => array('block' => 0, 'level' => 0),
        'js'        => array('block' => 0, 'level' => 0),
        'css'       => array('block' => 0, 'level' => 0),
        'jsconst'   => array('block' => 0, 'level' => 1),
        'empty'     => array('block' => 1, 'level' => 5),
        'notempty'  => array('block' => 1, 'level' => 5),
        'switch'    => array('block' => 1, 'level' => 2),
        'case'      => array('block' => 1, 'level' => 0),
        'default'   => array('block' => 0, 'level' => 1),
        'assign'    => array('block' => 0, 'level' => 1),
    );

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->left = C("TPL_TAG_LEFT") ? C("TPL_TAG_LEFT") : '{'; //左侧标签
        $this->right = C("TPL_TAG_RIGHT") ? C("TPL_TAG_RIGHT") : '}'; //右侧标签
        if (method_exists($this, '__init')) {
            $this->__init();
        }
    }

    /**
     * 运行编译
     */
    public function run(&$view = null, &$content = null, &$compileFile = null)
    {
        //View对象
        $this->view = $view;
        //模板内容
        $this->content = is_null($content) ? $this->view->content : $content;
        //获得不解析内容
        $this->_getNoParseContent();
        //解析include标签
        $this->content = $this->parseInclude($this->content);
        //解析变量
        $this->_parseVar();
        //加载标签类 (标签由系统标签与用户扩展标签构成)
        $this->_parseCommon();
        //将所有常量替换   如把__APP__进行替换
        $this->_parseUrlConst();
        //将不解析内容还原
        $this->_replaceNoParseContent();
        //清空包含{__NOLAYOUT__}字符串
        $this->content = str_replace('<!--{__NOLAYOUT__}-->','',$this->content);
        $this->content = str_replace('{__NOLAYOUT__}','',$this->content);
        $this->view->savefile($this->content, $compileFile);
    }

    /**
     * 获得不解析内容
     */
    private function _getNoParseContent()
    {
        $status = preg_match_all('@'.$this->left.'literal'.$this->right.'(.*?)'.$this->left.'\/literal'.$this->right.'@isU', $this->content, $info, PREG_SET_ORDER);
        if ($status) {
            foreach ($info as $n => $content) {
                if ( ! empty($content)) {
                    $this->literal[$n] = $content[1];
                    $this->content = str_replace($content[0], '###'.$n.'###', $this->content);
                }
            }
        }
    }

    /**
     * 将记录的不解析内容替换回来
     */
    private function _replaceNoParseContent()
    {
        foreach ($this->literal as $n => $content) {
            $this->content = str_replace('###'.$n.'###', $content, $this->content);
        }
    }

    /**
     * 解析变量
     *
     * @return mixed
     */
    private function _parseVar()
    {
        $preg = '#\{(((?![\{\}]).)+)\}#isU';
        $status = preg_match_all($preg, $this->content, $info, PREG_SET_ORDER);
        if ($status) {
            foreach ($info as $d) {
                $var = '';
                // 输出某个函数的结果
                if(0===strpos($d[1],':')) {
                    $var = 'echo '. substr($d[1],1);
                }
                // 执行某个函数
                elseif(0===strpos($d[1],'~')) {
                    $var = substr($d[1],1);
                }
                elseif(substr($d[1],0,2)=='//' || (substr($d[1],0,2)=='/*' && substr(rtrim($d[1]),-2)=='*/')){
                    //注释标签
                    $this->content = str_replace($d[0], '', $this->content);
                }
                elseif('$' == substr($d[1],0,1) && '.' != substr($d[1],1,1) && '(' != substr($d[1],1,1)){
                    //解析模板变量 格式 {$varName} 、{$varName|function1|function2=arg1,arg2}
                    $name = substr($d[1],1);
                    $var = ''. $this->parseVar($name);
                }
                elseif(0===strpos($d[1],'|')) {
                    //变量执行函数
                    $name = $d[1];
                    $var = ''. $this->parseVar($name);
                }
                elseif('-' == substr($d[1],0,1) || '+'== substr($d[1],0,1)){
                    // 输出计算
                    $var = 'echo '.$d[1];
                }
                if (!empty($var)) {
                    $replace = '<?php '. $var .';?>';
                    $this->content = str_replace($d[0], $replace, $this->content);
                }
            }
        }
    }

    /**
     * switch标签解析
     * 格式：
     * <switch name="a.name" >
     * <case value="1" break="false">1</case>
     * <case value="2" >2</case>
     * <default />other
     * </switch>
     * @access public
     * @param array $attr 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _switch($attr, $content) {
        $name       =   $attr['name'];
        $varArray   =   explode('|',$name);
        $name       =   array_shift($varArray);
        $name       =   $this->autoBuildVar($name);
        if(count($varArray)>0)
            $name   =   $this->parseVarFunction($name,$varArray);
        $php        =   '<?php switch('.$name.'): ?>'.ltrim($content).'<?php endswitch;?>';
        return $php;
    }

    /**
     * case标签解析 需要配合switch才有效
     * @access public
     * @param array $attr 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _case($attr, $content) {
        $value  = $attr['value'];
        if('$' == substr($value,0,1)) {
            $varArray   =   explode('|',$value);
            $value	    =	array_shift($varArray);
            $value      =   $this->autoBuildVar(substr($value,1));
            if(count($varArray)>0)
                $value  =   $this->parseVarFunction($value,$varArray);
            $value      =   'case '.$value.': ';
        }elseif(strpos($value,'|')){
            $values     =   explode('|',$value);
            $value      =   '';
            foreach ($values as $val){
                $value   .=  'case "'.addslashes($val).'": ';
            }
        }else{
            $value	=	'case "'.$value.'": ';
        }
        $php = '<?php '.$value.' ?>'.$content;
        $isBreak  = isset($attr['break']) ? $attr['break'] : '';
        if('' ==$isBreak || ($isBreak && strtolower($isBreak) != 'false' && strval($isBreak) != '0')) {
            $php .= '<?php break;?>';
        }
        return $php;
    }

    /**
     * default标签解析 需要配合switch才有效
     * 使用： <default />ddfdf
     * @access public
     * @return string
     */
    public function _default() {
        $php = '<?php default: ?>';
        return $php;
    }

    /**
     * assign标签解析
     * 在模板中给某个变量赋值 支持变量赋值
     * 格式： <assign name="" value="" />
     * @access public
     * @param array $attr 标签属性
     * @return string
     */
    public function _assign($attr) {
        $name = $this->autoBuildVar($attr['name']);
        if('$'==substr($attr['value'],0,1)) {
            $value = $this->autoBuildVar(substr($attr['value'],1));
        }else{
            $value = '\''.$attr['value']. '\'';
        }
        $php = '<?php '.$name.' = '.$value.'; ?>';
        return $php;
    }

    /**
     * 自动识别构建变量
     * @access public
     * @param string $name 变量描述
     * @return string
     */
    public function autoBuildVar($name) {
        if(strpos($name,'.')) {
            $vars = explode('.',$name);
            $count = count($vars);
            $var  =  array_shift($vars);
            if($count==2) {
                // 自动判断数组或对象 只支持二维
                $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
            }
            else {
                // 识别为数组
                $name = '$'.$var;
                foreach ($vars as $key=>$val){
                    if(0===strpos($val,'$')) {
                        $name .= '["{'.$val.'}"]';
                    }else{
                        $name .= '["'.$val.'"]';
                    }
                }
            }
        }elseif(strpos($name,':')){
            // 额外的对象方式支持
            $name   =   '$'.str_replace(':','->',$name);
        }elseif(!defined($name)) {
            $name = '$'.$name;
        }
        return $name;
    }

    /**
     * 模板变量解析,支持使用函数
     * 格式： {$varname|function1|function2=arg1,arg2}
     * @access public
     * @param string $varStr 变量数据
     * @return string
     */
    public function parseVar($varStr){
        $varStr = trim($varStr);
        static $_varParseList = array();
        //如果已经解析过该变量字串，则直接返回变量值
        if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];
        $parseStr   =   '';
        if(!empty($varStr)){
            $varArray = explode('|',$varStr);
            //取得变量名称
            $var = array_shift($varArray);
            if( false !== strpos($var,'.')) {
                //支持 {$var.property}
                $vars = explode('.',$var);
                $count = count($vars);
                $var  =  array_shift($vars);
                if($count==2) {
                    // 自动判断数组或对象 只支持二维
                    $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
                }
                else {
                    // 识别为数组
                    $name = '$'.$var;
                    foreach ($vars as $key=>$val){
                        if(0===strpos($val,'$')) {
                            $name .= '["{'.$val.'}"]';
                        }else{
                            $name .= '["'.$val.'"]';
                        }
                    }
                }
            }elseif(false !== strpos($var,':')) {
                // 识别为对象
                $vars = explode(':',$var);
                $var = array_shift($vars);
                $name = '$'.$var;
                foreach ($vars as $key=>$val) {
                    $name .= '->'.$val;
                }
            }elseif(false !== strpos($var,'[')) {
                //支持 {$var['key']} 方式输出数组
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];
            }elseif(false !==strpos($var,':') && false ===strpos($var,'(') && false ===strpos($var,'::') && false ===strpos($var,'?')){
                //支持 {$var:property} 方式输出对象的属性
                $vars = explode(':',$var);
                $var  = str_replace(':','->',$var);
                $name = "$".$var;
                $var  = $vars[0];
            }else {
                $name = "$$var";
            }
            //对变量使用函数
            if(count($varArray)>0)
                $name = $this->parseVarFunction($name,$varArray);
            $parseStr = 'echo ('.$name.');';
        }
        if($varStr) $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }


    /**
     * 对模板变量使用函数
     * 格式 {$varname|function1|function2=arg1,arg2}
     * @access public
     * @param string $name 变量名
     * @param array $varArray  函数列表
     * @return string
     */
    public function parseVarFunction($name,$varArray){
        for($i=0;$i<count($varArray);$i++ ){
            $args = explode('=',$varArray[$i],2);
            $fun = trim($args[0]);
            switch($fun) {
                case 'default':  // 特殊模板函数
                    if(false!==strpos($name,'(')) {
                        $name = '('.$name.' != "")?('.$name.'):'.$args[1];
                    }else{
                        $name = '(isset('.$name.') && ('.$name.' != ""))?('.$name.'):'.$args[1];
                    }
                    break;
                default:  // 通用模板函数
                    //函数名（别名函数中存在时，使用别名函数）
                    if (isset($this->aliasFunction[$fun])) {
                        $fun = $this->aliasFunction[$fun];
                    }
                    if(empty($name) || '$'==$name) {
                        $name = "$fun($args[1])";
                        if(isset($args[1])){
                            $name = "$fun($args[1])";
                        }else if(!empty($args[0])){
                            $name = "$fun()";
                        }
                    }
                    else {
                        if(isset($args[1])){
                            if(strstr($args[1],'###')){
                                $args[1] = str_replace('###',$name,$args[1]);
                                $name = "$fun($args[1])";
                            }
                            else{
                                $name = "$fun($name,$args[1])";
                            }
                        }else if(!empty($args[0])){
                            $name = "$fun($name)";
                        }
                    }
            }
        }
        return $name;
    }

    /**
     * 替换URL地址常量
     * 如__CONTROLLER__
     */
    private function _parseUrlConst()
    {
        $const = get_defined_constants(true);
        foreach ($const['user'] as $k => $v) {
            if (strstr($k, '__')) {
                $this->content = str_replace($k, $v, $this->content);
            }
        }
    }

    public function _parseCommon() {
        //标签库中的标签方法
        foreach ($this->Tag as $tag => $option) {
            //合法标签满足以下条件. 定义了block与level值
            if ( ! isset($option['block'])  || ! isset($option['level']) ) {
                continue;
            }
            //解析标签
            for ($i = 0; $i <= $option['level']; $i++) {
                if ( ! $this->parseTag($tag, $option, $this->content, $this->view) ) {
                    break;
                }
            }
        }
        //自定义标签
        if(C('TPL_TAGS')) {
            $tagdata = array();
            foreach(C('TPL_TAGS') as $tagName) {
                $name = str_replace(array('\\', '.', '#', '@'), array('/', '/', '.', MODULE), $tagName).'Tag';
                $info = explode('/',$name);
                if(count($info)==2) {
                    $name = basename(APP_COMMON_PATH).'/'.$name;
                }
                else if(count($info)==1) {
                    $name = basename(APP_COMMON_PATH).'/'.basename(MODULE_TAG_PATH).'/'.$name;
                }
                $class = str_replace('/','\\',$name);
                import($name);
                if (class_exists($class)) {
                    $objTag = new $class;
                    $tags = $objTag->Tag;
                    foreach($tags as $key=>$tag) {
                        $tagdata[$key] = array(
                            'class'=>$class,
                            'tag'=>$tag,
                        );
                    }
                }
            }
            foreach($tagdata as $tag=>$item) {
                $option = $item['tag'];
                //合法标签满足以下条件. 定义了block与level值
                if ( ! isset($option['block'])  || ! isset($option['level']) ) {
                    continue;
                }
                //解析标签
                $parser = new $item['class'];
                for ($i = 0; $i <= $option['level']; $i++) {
                    if ( ! $this->parseTag($tag, $option, $this->content, $this->view, $parser)
                    ) {
                        break;
                    }
                }
            }
        }
    }

    // 解析模板中的include标签
    protected function parseInclude($content) {
        // 读取模板中的include标签
        $find = preg_match_all('/'.$this->left.'include\s(.+?)\s*?\/'.$this->right.'/is',$content,$matches);
        if($find) {
            for($i=0;$i<$find;$i++) {
                $include = $matches[1][$i];
                //属性解析
                if (empty($include)) {
                    $attr = array();
                } else {
                    $attr = $this->parseTagAttr($include);
                }
                $file = $attr['file'];
                unset($attr['file']);
                $content = str_replace($matches[0][$i],$this->_parseIncludeItem($file,$attr),$content);
            }
        }
        return $content;
    }

    /**
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     * @access private
     * @param string $file  模板文件名
     * @param array $vars  要传递的变量列表
     * @return string
     */
    private function _parseIncludeItem($file, $vars=array()){
        // 分析模板文件名并读取内容
        $parseStr = $this->parseTpl($file);
        // 替换变量
        foreach ($vars as $key=>$val) {
            $parseStr = str_replace('['.$key.']',$val,$parseStr);
        }
        // 再次对包含文件进行模板分析
        return $this->parseInclude($parseStr);
    }

    /**
     * 分析加载的模板文件并读取内容 支持多个模板文件读取
     * @access private
     * @param string $file  模板文件名
     * @return string
     */
    private function parseTpl($file){
        // 支持加载变量文件名
        if(substr($file,0,1)=='$') {
            $file = $this->view->get(substr($file,1));
        }
        //替换常量
        $const = get_defined_constants(true);
        foreach ($const['user'] as $k => $v) {
            $file = str_replace($k, $v, $file);
        }
        $array = explode(',', get_view_file($file));
        $parseStr = '';
        foreach ($array as $file){
            if(trim($file)=='') continue;
            // 获取模板文件内容
            $parseStr .= file_get_contents($file);
        }
        return $parseStr;
    }

    //css标签
    public function _css($attr)
    {
        return "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$attr['file']}\"/>";
    }

    //js标签
    public function _js($attr)
    {
        return "<script type=\"text/javascript\" src=\"{$attr['file']}\"></script>";
    }

    //list标签
    public function _list($attr, $content)
    {
        //变量
        $from = $attr['from'];
        //name名
        $name = substr($attr['name'],1);
        //默认值
        $empty = isset($attr['empty']) ? $attr['empty'] : '';
        //显示条数
        $row = isset($attr['row']) ? $attr['row'] : 100;
        //间隔
        $step = isset($attr['step']) ? $attr['step'] : 1;
        //开始数
        $start = isset($attr['start']) ? $attr['start'] : 0;
        $php
            = <<<php
        <?php
        //初始化
        \$took['list']['$name'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($from)) {
            echo '$empty';
        } else {
            \$listId = 0;
            \$listShowNum=0;
            \$listNextId=$start;
            foreach ($from as \$$name) {
                //开始值
                if (\$listId<$start) {
                    \$listId++;
                    continue;
                }
                //步长
                if(\$listId!=\$listNextId){\$listId++;continue;}
                //显示条数
                if(\$listShowNum>=$row)break;
                //第几个值
                \$took['list']['$name']['index']++;
                //第1个值
                \$took['list']['$name']['first']=(\$listId == $start);
                //最后一个值
                \$took['list']['$name']['last']= (count($from)-1 <= \$listId);
                //总数
                \$took['list']['$name']['total']++;
                //增加数
                \$listId++;
                \$listShowNum++;
                \$listNextId+=$step
                ?>
php;
        $php .= $content;
        $php .= "<?php }}?>";

        return $php;
    }

    //标签处理
    public function _foreach($attr, $content)
    {
        $from = $attr['from'];
        $value = $attr['value'];
        $key  = !empty($attr['key'])?$attr['key']:'$key';
        $from       =   $this->autoBuildVar($from);

        $php = "<?php if(is_array({$from})): foreach ({$from} as {$key}=>{$value}): ?>";
        $php .= $content;
        $php .= '<?php endforeach; endif; ?>';
        return $php;
    }

    //if标签
    public function _if($attr, $content)
    {
        $condition = !empty($attr['condition']) ? $attr['condition'] : $attr['value'];
        $condition = $this->parseCondition($condition);
        return "<"."?php if(".$condition."){ ?".">".$content."<"."?php } ?".">";
    }

    //elseif标签
    public function _elseif($attr)
    {
        $condition = !empty($attr['condition']) ? $attr['condition'] : $attr['value'];
        $condition = $this->parseCondition($condition);
        return "<?php }else if(".$condition."){ ?>";
    }

    //else标签
    public function _else()
    {
        return "<?php }else{ ?>";
    }

    //empty标签
    public function _empty($attr, $content)
    {
        $value = $this->parseCondition($attr['value']);
        $php = "<?php if (empty({$value})){ ?>";
        $php .= $content;
        $php .= '<?php } ?>';
        return $php;
    }

    //notempty标签
    public function _notempty($attr, $content)
    {
        $value = $this->parseCondition($attr['value']);
        $php = "<?php if (!empty({$value})){ ?>";
        $php .= $content;
        $php .= '<?php } ?>';
        return $php;
    }

    //将URL常量定义为JS变量
    public function _jsconst()
    {
        //所有常量
        $const = get_defined_constants(true);
        //查找所以以http开始的常量
        $arr = preg_grep("/^http/i", $const['user']);
        $str = "<script type='text/javascript'>\n";
        foreach ($arr as $k => $v) {
            $k = str_replace('__', '', $k);
            $str .= $k . " = '$v';\n";
        }
        $str .= "</script>";
        return $str;
    }

    /**
     * 解析标签
     *
     * @param array $tag 标签
     * @param string $ViewContent 模板解析内容
     * @return mixed
     */
    public function parseTag($tag, $data, &$ViewContent, &$objView, &$objParser = null)
    {
        $objParser = ($objParser==null) ? $this : $objParser;
        if ($data['block']) {
            //块标签解析
            $preg = '#' . $this->left . '(?:' . $tag . '|' . $tag . '\s+(.*))' . $this->right . '(.*)' . $this->left[0] . '/' . substr($this->left, 1) . $tag . $this->right . '#isU';
        } else {
            //行标签处理
            $preg = '#' . $this->left . '(?:' . $tag . '|' . $tag . '\s+(.*))/' . $this->right . "#isU"; //独立正则
        }
        /**
         * 找到所有当前标签名的内容区域
         * 变量info说明: 0) 全部匹配内容 1) 属性部分 2) 内容部分
         */
        $status = preg_match_all($preg, $ViewContent, $info, PREG_SET_ORDER);
        if ($status) {
            foreach ($info as $k) {
                //属性解析
                if (empty($k[1])) {
                    $attr = array();
                } else {
                    $attr = $this->parseTagAttr($k[1]);
                }
                //标签内容
                $k[2]        = isset($k[2]) ? $k['2'] : '';
                $objParser   = method_exists($objParser, '_'.$tag) ? $objParser : $this;
                $content     = call_user_func_array(array($objParser, '_' . $tag), array($attr, $k[2], &$objView));
                $ViewContent = str_replace($k[0], $content, $ViewContent);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 解析标签属性
     *
     * @param string $attrStr 标签字符串
     * @return array 标签名如foreach
     */
    protected function parseTagAttr($attrStr)
    {
        $pregAttr = '#' . '([a-z_]+)=(["\'])(.*)\2#iU'; //属性正则
        //$info说明 0 完整内容, 1 引号, 3 属性值
        $status = preg_match_all($pregAttr, $attrStr, $info, PREG_SET_ORDER);
        if ($status) {
            $attr = array();
            foreach ($info as $k) {
                //解析属性值
                $attr[$k[1]] = $this->parseAttrValue($k[3]);
            }
            return $attr;
        } else {
            return array();
        }
    }

    /**
     * 解析属性值
     *
     * @param $attrValue 属性值
     * @return mixed
     */
    protected function parseAttrValue($attrValue)
    {
        //替换GT LT等
        foreach ($this->condition as $k => $v) {
            $attrValue = preg_replace("/\s+$k\s+/i", $v, $attrValue);
        }
        $attrValue = parent::parseAttrValue($attrValue);
        return $attrValue;
    }

    /**
     * 解析条件表达式
     * @access public
     * @param string $condition 表达式标签内容
     * @return array
     */
    protected function parseCondition($condition) {
        $condition = preg_replace('/\$(\w+):(\w+)\s/is','$\\1->\\2 ',$condition);
        $condition = preg_replace('/\$(\w+)\.(\w+)\s/is','(is_array($\\1)?$\\1["\\2"]:$\\1->\\2) ',$condition);
        return $condition;
    }

}