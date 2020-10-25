<?php
/**
 * ExtJS 主要用来开发RIA富客户端的AJAX应用,主要用于创建前端用户界面,与后台技术无关的前端ajax框架。
 * 本扩展使用EXTJS4.2免费版本
 * 扩展主要功能是使YII框架能够渲染扩展定义的html标签(以ext:开头，如<ext:head></ext:head>)
 * 
 * @author shanzz
 *
 */
class EXT extends CController{
	public static $skin;
	public static $ext;
	const EXT4YII='ext4yii';
	
	public static $tagNum=0;//warn:!=count($Tags)
	
	public static $parentCurTag=null;//当前父TAG
	public static $Tags;//Tag列表
	public static $elementVOs=array();
	
	const VIEWPORT 	='viewport';
	const DATAVIEW	='dataview';
	const PANEL		='panel';
	const GRIDPANEL	='gridpanel';
	const TREEPANEL	='treepanel';
	const TABPANEL 	='tabpanel';
	const CONTAINER	='container';
	const TAB 		='tab';
	const DOCKED 	='docked';
	const MENU		='menu';
	const MENUITEM	='menuitem';
	const COLUMN	='column';
	const ACTIONCOLUMN	='actioncolumn';
	const WINDOW	='window';
	const FORMPANEL	='formpanel';
	const TEXTFIELD	='textfield';
	const TEXTAREAFIELD	='textareafield';
	const HIDDENFIELD	='hiddenfield';
	const CHECKBOXFIELD	='checkboxfield';
	const COMBOBOXFIELD	='combobox';
	const RADIOBOXFIELD	='radioboxfield';
	const DOCKEDITEM	='dockedItem';
	const TRIGGERFIELD	='triggerfield';
	const SELMODEL		='selmodel';
	const NUMBERFIELD	='numberfield';
	const DATEFIELD		='datefield';
	const DISPLAYFIELD	='displayfield';
	const FILEFIELD		='filefield';
	const FILLFIELD		='fillfield';
	const HTMLEDITOR	='htmleditor';
	const TREEPICKERFIELD	='treepickerfield';
	const BUTTON		='button';
	const TOOL			='tool';
	const COMPONENT		='component';
	const IFRAME		='widget.appuxiframe';
	const FIELDSET		='fieldset';
	
	public static function display($controller,$view,$data=null,$return=false){
		$viewFile=$controller->getViewFile($view);
		$fileContent=file_get_contents($viewFile);
		$fileContent=self::replaceContent($fileContent);
		if(isset($controller->getModule()->id)){
			$path=Yii::app()->getRuntimePath().'/extjs/'.$controller->getModule()->id.'/';
		}else{
			$path=Yii::app()->getRuntimePath().'/extjs/';
		}
		$path.=$controller->id.'/';
		@mkdir($path,null,true);
		$file=$path.md5($viewFile).".{$view}.php";
		file_put_contents($file,$fileContent);
		return $controller->renderFile($file,$data,$return);
	}
	
	public static function Tag_headStart($atts=''){
		require  dirname(__FILE__).'/tags/html/headStartTag.php';
	}
	public static function Tag_headEnd(){
		require  dirname(__FILE__).'/tags/html/headEndTag.php';
	}
	public static function Tag_include($atts=''){
		require  dirname(__FILE__).'/tags/html/includeLibTag.php';
	}
	public static function Tag_bodyStart($atts=''){
		require  dirname(__FILE__).'/tags/html/bodyStartTag.php';
	}
	public static function Tag_bodyEnd($atts=''){
		require  dirname(__FILE__).'/tags/html/bodyEndTag.php';
	}
	
	//成对标签
	public static function Tag_onreadyStart($atts=''){
		require  dirname(__FILE__).'/tags/ext/onreadyStartTag.php';
	}
	public static function Tag_onreadyEnd($atts=''){
		require  dirname(__FILE__).'/tags/ext/onreadyEndTag.php';
	}
	
	public static function Tag_viewportStart($atts=''){
		self::doStartTag($atts, self::VIEWPORT);
	}
	public static function Tag_viewportEnd(){
		require  dirname(__FILE__).'/tags/ext/viewportTag.php';
		self::doEndTag($id);
	}
	public static function Tag_panelStart($atts=''){
		self::doStartTag($atts, self::PANEL);
	}
	public static function Tag_panelEnd(){
		require  dirname(__FILE__).'/tags/ext/panelTag.php';
		self::doEndTag($id);
	}
	public static function Tag_gridPanelStart($atts=''){
		self::doStartTag($atts, self::GRIDPANEL);
	}
	public static function Tag_gridPanelEnd(){
		require  dirname(__FILE__).'/tags/ext/grid/gridPanelTag.php';
		self::doEndTag($id);
	}
	public static function Tag_treePanelStart($atts=''){
		$tag=self::doStartTag($atts, self::TREEPANEL);
		$atts=$tag['atts'];
		$atts['animate']=isset($atts['animate'])?$atts['animate']:"false";
		$atts['rootVisible']=isset($atts['rootVisible'])?$atts['rootVisible']:"true";
		$atts['rootExpanded']=isset($atts['rootVisible'])?$atts['rootVisible']:"true";
		$atts['useArrows']=isset($atts['useArrows'])?$atts['useArrows']:"true";
		$atts['cascade']=isset($atts['cascade'])?$atts['cascade']:"false";
		$atts['singleClick']=isset($atts['singleClick'])?$atts['singleClick']:"true";
		$atts['treegrid']=isset($atts['treegrid'])?$atts['treegrid']:"false";
		if(isset($atts['url'])){
			$atts['rootId']=isset($atts['rootId'])?$atts['rootId']:"0";
			$atts['rootText']=isset($atts['rootText'])?$atts['rootText']:"根节点";
		}
		if(!isset($atts['border'])){
			$parentTag=self::getParentTag($tag['tagId']);
			if($parentTag['type']==self::VIEWPORT){
				$atts['border']="false";
			}
		}
		if(isset($atts['rootAttribute'])){
			$atts['rootAttribute']=trim($atts['rootAttribute'],',');
		}
		self::addTagParams($tag['tagId'],'atts',$atts,false);
	}
	public static function Tag_treePanelEnd(){
		require  dirname(__FILE__).'/tags/ext/tree/treePanelTag.php';
		self::doEndTag($id);
	}
	public static function Tag_dockedStart($atts=''){
		self::doStartTag($atts, self::DOCKED,false);
	}
	public static function Tag_dockedEnd(){
		require  dirname(__FILE__).'/tags/ext/dockedTag.php';
		self::doEndTag($id);
	}
	public static function Tag_menuStart($atts=''){
		self::doStartTag($atts, self::MENU, false);
	}
	public static function Tag_menuEnd(){
		require  dirname(__FILE__).'/tags/ext/menuTag.php';
		self::doEndTag($id);
	}
	
	public static function Tag_actioncolumnStart($atts=''){
		self::doStartTag($atts, self::ACTIONCOLUMN, true);
	}
	public static function Tag_actioncolumnEnd(){
		require  dirname(__FILE__).'/tags/ext/grid/columnTag.php';
		self::doEndTag($id);
	}
	
	public static function Tag_windowStart($atts=''){
		$tag=self::doStartTag($atts, self::WINDOW);
		$atts=$tag['atts'];
		if(!isset($atts['constrain'])){
			$atts['constrain']="true";
		}
		if(!isset($atts['closeAction'])){
			$atts['closeAction']="hide";
		}
		if(!isset($atts['modal'])){
			$atts['modal']="true";
		}
		if(isset($atts['opacity'])){
			if($atts['opacity']>1.0){
				unset($atts['opacity']);
			}
			$alpha=intval($atts['opacity']*100);
			$opacityString = "opacity: '{$atts['opacity']}', filter: 'alpha(opacity={$alpha})'";
			if(!isset($atts['style'])){
				$atts['style']=$opacityString;
			}else{
				$atts['style'].=','.$opacityString;
			}
		}
		$atts['style']=isset($atts['style'])?$atts['style']:'';
		if(!isset($atts['frame']) || (isset($atts['frame']) && $atts['frame']!="true")){
			$atts['bodyStyle']=self::getMyStyleOrBodyStye($atts['style']);
		}
		self::addTagParams($tag['tagId'],'atts',$atts,false);
	}
	public static function Tag_windowEnd(){
		require  dirname(__FILE__).'/tags/ext/windowTag.php';
		self::doEndTag($id);
	}
	public static function Tag_formPanelStart($atts=''){
		$tag=self::doStartTag($atts, self::FORMPANEL);
		$atts=$tag['atts'];
		if(!isset($atts['border'])){
			$atts['border']="false";
		}
		if(!isset($atts['width'])){
			$atts['width']="200";
		}
		if(!isset($atts['layout'])){
			$atts['layout']="column";
		}
		if(!isset($atts['bodyPadding'])){
			$atts['bodyPadding']="5";
		}
		if(!isset($atts['constrain'])){
			$atts['constrain']="true";
		}
		if($atts['layout']=='column' && !isset($atts['rowSpace'])){
			$atts['rowSpace']="5";
		}
		self::addTagParams($tag['tagId'],'atts',$atts,false);
	}
	public static function Tag_formPanelEnd(){
		require  dirname(__FILE__).'/tags/ext/form/formPanelTag.php';
		self::doEndTag($id);
	}
	public static function Tag_tabPanelStart($atts=''){
		self::doStartTag($atts, self::TABPANEL);
	}
	public static function Tag_tabPanelEnd(){
		require  dirname(__FILE__).'/tags/ext/tabPanelTag.php';
		self::doEndTag($id);
	}
	public static function Tag_tabStart($atts=''){
		self::doStartTag($atts, self::TAB);
	}
	public static function Tag_tabEnd(){
		require  dirname(__FILE__).'/tags/ext/tabTag.php';
		self::doEndTag($id);
	}
	public static function Tag_containerStart($atts=''){
		self::doStartTag($atts, self::CONTAINER);
	}
	public static function Tag_containerEnd(){
		require  dirname(__FILE__).'/tags/ext/containerTag.php';
		self::doEndTag($id);
	}
	public static function Tag_dataviewStart($atts=''){
		$tag=self::doStartTag($atts, self::DATAVIEW);
		$atts=$tag['atts'];
		$atts['autoLoad']=isset($atts['autoLoad'])?$atts['autoLoad']:"true";
		$atts['fields']=isset($atts['fields'])?$atts['fields']:"[]";
		$atts['animated']=self::getIEVersion()>0?"false":isset($atts['animated'])?$atts['animated']:false;
		$atts['autoLoad']=isset($atts['autoLoad'])?$atts['autoLoad']:"false";
		$atts['multiSelect']=isset($atts['multiSelect'])?$atts['multiSelect']:"false";
		$atts['duration']=isset($atts['duration'])?$atts['duration']:"500";
		$atts['autoScroll']=isset($atts['autoScroll'])?$atts['autoScroll']:"true";
		self::addTagParams($tag['tagId'],'atts',$atts,false);
	}
	public static function Tag_dataviewEnd(){
		require  dirname(__FILE__).'/tags/ext/viewTag.php';
		self::doEndTag($id);
	}
	public static function Tag_fieldsetStart($atts=''){
		$tag=self::doStartTag($atts, self::FIELDSET);
		$atts=$tag['atts'];
		$atts['collapsible']=isset($atts['collapsible'])?$atts['collapsible']:"true";
		$atts['layout']=isset($atts['layout'])?$atts['layout']:"column";
		$parentTag=self::getParentTag($tag['tagId']);
		if($parentTag && $parentTag['atts']['layout']=="column"){
			$atts['columnWidth']=isset($atts['columnWidth'])?$atts['columnWidth']:"1";
			if(!isset($atts['rowSpace']) && isset($parentTag['atts']['rowSpace'])){
				$atts['rowSpace']=$parentTag['atts']['rowSpace'];
			}
		
		}
		self::addTagParams($tag['tagId'],'atts',$atts,false);
	}
	public static function Tag_fieldsetEnd(){
		require  dirname(__FILE__).'/tags/ext/form/fieldSetTag.php';
		self::doEndTag($id);
	}
	
	
	//单个标签
	public static function Tag_dockedItem($atts=''){
		require  dirname(__FILE__).'/tags/ext/dockedItemTag.php';
	}
	public static function Tag_triggerField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/triggerFieldTag.php';
	}
	public static function Tag_selModel($atts=''){
		require  dirname(__FILE__).'/tags/ext/selModelTag.php';
	}
	public static function Tag_menuItem($atts=''){
		require  dirname(__FILE__).'/tags/ext/menuItemTag.php';
	}
	public static function Tag_column($atts=''){
		require  dirname(__FILE__).'/tags/ext/grid/columnSingleTag.php';
	}
	public static function Tag_action($atts=''){
		require  dirname(__FILE__).'/tags/ext/grid/actionTag.php';
	}
	public static function Tag_hiddenField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/hiddenFieldTag.php';
	}
	public static function Tag_textField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/textFieldTag.php';
	}
	public static function Tag_textAreaField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/textAreaFieldTag.php';
	}
	public static function Tag_numberField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/numberFieldTag.php';
	}
	public static function Tag_checkboxField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/checkboxFieldTag.php';
	}
	public static function Tag_comboBox($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/comboBoxFieldTag.php';
	}
	
	public static function Tag_dateField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/dateFieldTag.php';
	}
	public static function Tag_displayField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/displayFieldTag.php';
	}
	public static function Tag_fileField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/fileFieldTag.php';
	}
	public static function Tag_fillField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/fillFieldTag.php';
	}
	public static function Tag_htmlEditor($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/htmlEditorTag.php';
	}
	public static function Tag_radioboxField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/radioboxFieldTag.php';
	}
	public static function Tag_treePickerField($atts=''){
		require  dirname(__FILE__).'/tags/ext/form/treePickerFieldTag.php';
	}
	public static function Tag_button($atts=''){
		require  dirname(__FILE__).'/tags/ext/buttonTag.php';
	}
	public static function Tag_tool($atts=''){
		require  dirname(__FILE__).'/tags/ext/toolTag.php';
	}
	public static function Tag_component($atts=''){
		require  dirname(__FILE__).'/tags/ext/componentTag.php';
	}
	public static function Tag_iframe($atts=''){
		require  dirname(__FILE__).'/tags/ext/iframeTag.php';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	private static function getIEVersion(){
	    if (empty($_SERVER['HTTP_USER_AGENT'])){    //当浏览器没有发送访问者的信息的时候
	        return 0;
	    }
   		$agent= $_SERVER['HTTP_USER_AGENT']; 
   		if(strpos($agent,'rv:11.0')){
   			return 11;
   		}
	    if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)){
	    	return $regs[1];
	    }
	    return 0;
	}
	public static function getExtPath(){
		if(!self::$ext){
			self::setupExt();
		}
		return self::$ext;
	}
	private static function setupExt() {
		$uid=substr(md5(self::EXT4YII), 0,8);
		self::$ext='/assets/'.$uid;
		$dst=Yii::app()->basePath.'/..'.self::$ext;
		if(file_exists($dst.'/info.txt')){
			return;
		}
		$src=dirname(__FILE__).'/ext';
		self::copyDir($src, $dst);
	}
	private static function getExtSkin(){
		return isset(Yii::app()->user->skin)?Yii::app()->user->skin:'classic';
	}
	private static function getIcon($icon){
		if($icon){
			return self::getExtPath().'/icon/'.$icon;
		}else{
			return '';
		}
		
	}
	private static function copyDir($src,$dst) {  // 原目录，复制到的目录
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					self::copyDir($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	
	private static function getUUID4Tag($id=null,$autoInsert=true,$tagType=''){
		if(!$id){
			self::$tagNum++;
			$id="_id_".substr(md5(self::$tagNum), 0,8);
		}
		if($autoInsert){
			$tag=self::addTag($id,$tagType);
			self::addItem2Parent($tag);
		}
		return $id;
	}
	private static function haveListeners($id){
		if(isset(self::$Tags[$id]['listenerList']) && count(self::$Tags[$id]['listenerList']))return true;
		if(isset(self::$Tags[$id]['afterRenderRegisterDockedList']) && count(self::$Tags[$id]['afterRenderRegisterDockedList']))return true;
		if(isset(self::$Tags[$id]['afterRenderRegisterList']) && count(self::$Tags[$id]['afterRenderRegisterList']))return true;
		return false;
	}

	//新增Tag，并标识Tag的父Tag
	private static function addTag($tagId,$tagType='',$atts=''){
		$tag=array("tagId"=>$tagId,"type"=>$tagType,"parentTag"=>self::$parentCurTag,"atts"=>$atts);
		self::$Tags[$tagId]=$tag;
		return $tag;
	}
	//给父TAG增加Item，Item即是自己这个Tag
	private static function addItem2Parent($tag){
		if(isset($tag['parentTag']) && $tag['parentTag']){
			self::$Tags[$tag['parentTag']]['items'][]=$tag;
		}
	}
	private static function addAfterRenderRegisterList2Parent($tagId,$type){
		$parentTagId=self::$Tags[$tagId]['parentTag'];
		self::$Tags[$parentTagId]['afterRenderRegisterList'][]=array("id"=>$tagId,"type"=>$type);
	}
	private static function addListenerList2Parnet($tagId,$type){
		$parentTagId=self::$Tags[$tagId]['parentTag'];
		self::$Tags[$parentTagId]['listenerList'][]=array("id"=>$tagId,"type"=>$type);
	}
	private static function getPairTag($tagType){
		foreach (array_reverse(self::$Tags) as $tag){
			if(isset($tag['type']) && $tag['type']==$tagType){
				return $tag;
			}
		}
		return null;
	}
	private static function getParentTag($tagId){
		$parentTagId=isset(self::$Tags[$tagId]['parentTag'])?self::$Tags[$tagId]['parentTag']:null;
		if($parentTagId && isset(self::$Tags[$parentTagId]))
			return self::$Tags[$parentTagId];
		return null;
	}
	private static function getParentTagByTagType($tagId,$type){
		while (true){
			$parentTagId=isset(self::$Tags[$tagId]['parentTag'])?self::$Tags[$tagId]['parentTag']:null;
			if($parentTagId && isset(self::$Tags[$parentTagId])){
				if(self::$Tags[$parentTagId]['type']==$type){
					return self::$Tags[$parentTagId];
				}else{
					$tagId=$parentTagId;
				}
			}else {
				return null;
			}
		}
		
	}
	private static function addTagAttribute($tagId,$key,$val){
		if(!isset(self::$Tags[$tagId])){
			return;
		}
		self::$Tags[$tagId]['atts'][$key]=$val;
	}
	private static function addTagParams($tagId,$key,$val,$is_array=true){
		if(!isset(self::$Tags[$tagId])){
			return;
		}
		if($is_array){
			self::$Tags[$tagId][$key][]=$val;
		}else{
			self::$Tags[$tagId][$key]=$val;
		}
	}
	private static function resolveAtts($atts){
		preg_match_all('/([^\s=]+)=(["\s]?)([^"]+)\2(?=\s|$|>)/is',$atts,$matches);
		$ret=array();
		if(count($matches)==4){
			foreach ($matches[1] as $i=> $att){
				$ret[$att]=$matches[3][$i];
			}
		}
		return $ret;
	}
	
	/* 
	 * ++仅仅对于容器类型控件进行的start处理++
	 * 容器控件须有Start和End方法
	 * 在Start中生成ID、获得参数并添加到全局Tags列表中
	 * 添加时的TagType用于寻找标签对
	 * 如为容器控件，则设控件为当前的父控件，一定要在控件添加到全局Tags后再设父控件
	 * */
	private static function doStartTag($atts,$tagType,$add2Parent=true){
		$atts=self::resolveAtts($atts);
		$tagId=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,false);
		$tag=self::addTag($tagId, $tagType,$atts);
		if($add2Parent){
			self::addItem2Parent($tag);
		}
		self::$parentCurTag=$tagId;
		return $tag;
	}
	private static function doEndTag($tagId){
		$parentTag=self::getParentTag($tagId);
		if($parentTag){
			self::$parentCurTag=$parentTag['tagId'];
		}
		//把当前的父ID
	}
	private static function getMyStyleOrBodyStye($style){
		$myStyle = "backgroundColor:'#FFFFFF'";
		if($style){
			$myStyle.=','.$style;
		}
		return $myStyle;
	}
	private static function getMyOnclick($onclick){
		if(!$onclick){
			return $onclick;
		}
		if(substr($onclick, 0,1)=="#"){
			$onclick=substr($onclick, 1);
			$onclick="function(){" . $onclick . "}";
			return $onclick;
		}
		if(substr($onclick, 0,1)=="!"){
			return substr($onclick, 1);
		}
		if(strpos($onclick, "(")===false){
			$onclick.="()";
		}
		if(strpos($onclick, ";")===false){
			$onclick.=";";
		}
		
		$onclick="function(){" . $onclick . "}";
		return $onclick;
	}
	private static function getMyWidth($width){
		if(!$width)return $width;
		if(substr($width, 0,1)=="-"){
			$width = "document.body.clientWidth" . $width;
		}
		if($width=="auto"){
			return "'auto'";
		}
		return $width;
	}
	private static function getMyHeight($height){
		if(!$height)return $height;
		if(substr($height, 0,1)=="-"){
			$width = "document.body.clientHeight" . $height;
		}
		if($height=="auto"){
			return "'auto'";
		}
		return $height;
	}
	private static function getRenderTo($renderTo){
		if($renderTo){
			if(substr($renderTo, 0,1)=="!"){
				$renderTo=substr($renderTo, 1);
			}else{
				$renderTo="'{$renderTo}'";
			}
		}
		return $renderTo;
	}
	private static function replaceContent($fileContent){
		$matches=null;
		preg_match_all('/<script([^>]*)>/is',$fileContent,$matches);
		foreach ($matches[0] as $i=> $matche){
			$fileContent=str_replace($matche, "", $fileContent);
		}
		$fileContent=str_replace("</script>","",$fileContent);
		
		$funs=(get_class_methods('EXT'));
		usort($funs,function ($a, $b){
			$a=rtrim($a,"Start End");
			$b=rtrim($b,"Start End");
			if(strlen($a) < strlen($b)){
				return 1;
			}
			return -1;
		});
		foreach ($funs as $fun){
			if(substr($fun, 0,4)=="Tag_"){
				$tag=substr($fun, 4);
				if(substr($tag, -5)=="Start"){
					$tag=substr($fun, 4,-5);
					preg_match_all('/<ext:'.$tag.'([^>]*)>/is',$fileContent,$matches);
					foreach ($matches[0] as $i=> $matche){
						$str=self::bulidParams($matches[1][$i]);
						$fileContent=str_replace($matche, "<?php EXT::{$fun}({$str});?>", $fileContent);
					}
				}else if(substr($tag, -3)=="End"){
					$tag=strtolower(substr($fun, 4,-3));
					$fileContent=str_ireplace("</ext:$tag>","<?php EXT::{$fun}();?>",$fileContent);
				}else{
					preg_match_all('/<ext:'.$tag.'([^>]*)>/is',$fileContent,$matches);
					foreach ($matches[0] as $i=> $matche){
						$str=self::bulidParams($matches[1][$i]);
						$fileContent=str_replace($matche, "<?php EXT::{$fun}({$str});?>", $fileContent);
					}
				}
				
			}
		}
		return $fileContent;
	}
	private static function bulidParams($str){
		$str=trim($str,"/ \x20 \n \r \r\n\ \n\r");//前后空格去除
		$str=str_replace("\r\n", " ", $str);
		$str=str_replace("\n\r", " ", $str);
		$str=str_replace("\n", " ", $str);
		$str=str_replace("\r", " ", $str);
		$str=str_replace("\t", " ", $str);
		$str=str_replace("'", "\'", $str);//单引号添加转义
		preg_match_all('/\${(.*?)}/is',$str,$matches);
		//var_dump($matches);
		foreach ($matches[0] as $i=>$mathce){
			$str=str_replace($mathce, '\'.$'.$matches[1][$i].'.\'', $str);
		}
		$str=strlen($str)>0?"'{$str}'":"";//空值无需添加
		return $str;
	}
	
}
