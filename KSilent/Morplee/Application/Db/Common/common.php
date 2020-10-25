<?php
/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string 
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string 
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
	$key  = md5($key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char =  '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x=0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	$str = sprintf('%010d', $expire ? $expire + time() : 0);
	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
	}
	return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string 
 */
function think_ucenter_decrypt($data, $key){
	$key    = md5($key);
	$x      = 0;
	$data   = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data   = substr($data, 10);
	if($expire > 0 && $expire < time()) {
		return '';
	}
	$len  = strlen($data);
	$l    = strlen($key);
	$char = $str = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		}else{
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}


/**
 * 生成HTML表单
 * @param array $cell  数据
 * @param int $mid  新增\编辑
 */
function extra_create_html($cell,$mid){

	$htmlStr="<form method='post' action='".U()."' enctype='multipart/form-data' class='form-horizontal'>";
	$htmlStr.="<input type='hidden' name='_tid' value='".$cell['tid']."' />";
	$htmlStr.="<input type='hidden' name='_vid' value='".$cell['vid']."' />";
	$htmlStr.="<input type='hidden' name='_mid' value='".$mid."' />";
	$htmlStr.="<input type='hidden' name='_tname' value='".$cell['tname']."' />";

	foreach ($cell['cells'] as $key => $value) {
		
		//将有外键的视图列 转换为下拉列表
		if ($value['shareview']) {
			# code...
		} else {
			# code...
		}

		//将有字典的视图列 转换为下拉列表
		if ($value['vdic']) {
			# code...
		} else {
			# code...
		}
		switch ($value['flx']) {
			case 'text':
				$htmlStr.="
					<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
						<div class='controls'>
							<textarea " .($value['isreadonly'] ? "readonly" : "")." data-cms-isnotnull='" .$value['isnotnull']. "' data-cms-type=" .$value['flx']." data-cms-lcd=".$value['length']." type='text' id='".$value['vname']."' name='".$value['vname']."' style='width:490px;'>".$value['value']."</textarea>
							<span class='inpmsg'></span>
						</td>
					</div>";
				break;
            case "varchar":
            case "datetime":
            case "bigint":
            case "decimal":
            case "int":
                $htmlStr.="
                	<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
                		<div class='controls'>
                			<input " .($value['isreadonly'] ? "readonly" : ""). " data-cms-isnotnull='".$value['isnotnull']."'  data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." type='text' class='text input-large' id='".$value['vname']."' name='".$value['vname']."' value='".$value['value']."'>
                			<span class='inpmsg'></span>
                		</div>
                	</div>";
                break;
            case "richtext":
                $htmlStr.="
                	<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
						<div class='controls'>
                			<textarea ".($value['isreadonly'] ? "readonly" : "")." data-cms-isnotnull='".$value['isnotnull']."'  data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." type='text' style='width:100%;height:240px;' id='".$value['vname']."' name='".$value['vname']."'>".$value['value']."</textarea>
                			<span class='inpmsg'></span>
                		</div>
                	</div>";
                break;
            case "file":
                $htmlStr.="
                	<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
            			<div class='controls'>
            				<input type='file' data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." id='upload_file_".$value['vname']."'>
            				<input type='hidden' name=".$value['vname']." id='cover_id_".$value['vname']."' value='".$value['value']."'/>
            				<div class='upload-img-box'>
							</div>
            				<span class='inpmsg'></span>
            				<script>
            					$(function(){
            						upload_file('".$value['vname']."');
            					});
            				</script>
						</div>
                	</div>";
                break;
            case "hidden":
                $htmlStr.="<input data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." type='hidden' id='".$value['vname']."' name='".$value['vname']."' value='".$value['value']."' />";
                break;
            case "password":
                $htmlStr.="
                	<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
                		<div class='controls'>
                			<input ".($value['isreadonly'] ? "readonly" : "")." data-cms-isnotnull='".$value['isnotnull']."'  data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." type='password' id='".$value['vname']."' name='".$value['vname']."' value='".$value['value']."' />
                			<span class='inpmsg'></span>
                		</div>
                	</div>";
                break;
            case "image":
				$htmlStr.="
					<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."</label>
						<div class='controls'>
							<input type='file' data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." id='upload_picture_".$value['vname']."'>
							<input type='hidden' name=".$value['vname']." id='cover_id_".$value['vname']."' value='".$value['value']."'/>
							<div class='upload-img-box'>
								<div class='upload-pre-item'><img src='".$value['value']."'/></div>
							</div>
							<span class='inpmsg'></span>
							<script>
								$(function(){
									upload_picture('".$value['vname']."');
								});
							</script>
						</div>
					</div>";
                break;
			case "images":
				$htmlStr.="
                	<div class='form-item'>
						<label class='item-label'>".($value['isnotnull'] ? "<span style='color:red'>*&nbsp</span>" :"").$value['fnamec']."&nbsp;&nbsp;&nbsp;<a href=\"javascript:clear_more_Picture('".trim($value['vname'])."')\">清除</a></label>
						
            			<div class='controls'>
            				<input type='file' data-cms-type=".$value['flx']." data-cms-lcd=".$value['length']." id='upload_picture_".$value['vname']."'>
            				<input type='hidden' name=".$value['vname']." id='cover_id_".$value['vname']."' value='".$value['value']."'/>
            				<div class='upload-img-box'>
							</div>
            				<span class='inpmsg'></span>
            				<script>
            					$(function(){
            						upload_more_picture('".$value['vname']."');
            					});
            				</script>
						</div>
                	</div>";
				break;
			default:
				break;
		}
	}
	
	$htmlStr.="
		<div class='form-item'>
			<button class='btn submit-btn ajax-post' id='submit' type='submit' target-form='form-horizontal'>确 定</button>
			<button class='btn btn-return' onclick='javascript:history.back(-1);return false;'>返 回</button>
		</div></form>";


	$htmlStr.="
		<link href='".__ROOT__ ."/Public/". MODULE_NAME ."/css/jquery.datetimepicker.css' rel='stylesheet' />
		
		<script src='".__ROOT__ ."/Public/". MODULE_NAME ."/js/jquery.datetimepicker.js' charset='utf-8' type='text/javascript'></script>
		<script src='".__ROOT__ ."/Public/". MODULE_NAME ."/js/UEditor/ueditor.config.js' charset='utf-8' type='text/javascript'></script>
		<script src='".__ROOT__ ."/Public/". MODULE_NAME ."/js/UEditor/ueditor.all.min.js' charset='utf-8' type='text/javascript'></script>

		<script>
			$('input').each(function () {                                                                                                                                                                                                                                                                                                                                                              
	           var type = $(this).data('cms-type');                                                                                                                                                                                                                                                                                                                                                   
	           if (type == 'datetime') {                                                                                                                                                                                                                                                                                                                                                              
	               $(this).datetimepicker({lang:'ch'});                                                                                                                                                                                                                                                                                                                                                          
	           }                               
	                if($(this).attr('type')!='submit' && $(this).attr('type')!= 'button')   {                                                                                                                                                                                                                                                                                                                                    
	               $(this).attr('style', 'width:490px;');            
	                }                                                                                                                                                                                                                                                                                                                          
	       });                                                                                                                                                                                                                                                                                                                                                                                   
	       $('select').each(function () {                                                                                                                                                                                                                                                                                                                                                              
	               $(this).attr('style', 'width:390px;');                                                                                                                                                                                                                                                                                                                                                          
	       });                                                                                                                                                                                                                                                                                                                                                                                       
	                                                                                                                                                                                                                                                                                                                                                                                                  
	       $('textarea').each(function () {                                                                                                                                                                                                                                                                                                                                                              
	           var type = $(this).data('cms-type');                                                                                                                                                                                                                                                                                                                                                   
	           if (type == 'richtext') {                                                                                                                                                                                                                                                                                                                                                       
	               var id = $(this).attr('id');                                                                                                                                                                                                                                                                                                                                                       
	               $(this).attr('style', 'width:80%;height:240px;');                                                                                                                                                                                                                                                                                                                                
	               var um = UE.getEditor(id);                                                                                                                                                                                                                                                                                                                                                         
	           }                                                                                                                                                                                                                                                                                                                                                                                      
	       });

			$('form input').blur(function () {                                                                                                                                                                                                                                                                                                                                                         
	           var type = $(this).data('cms-type');                                                                                                                                                                                                                                                                                                                                                   
	           if (type != '') {                                                                                                                                                                                                                                                                                                                                                                      
	               if (type == 'int') {                                                                                                                                                                                                                                                                                                                                                               
	                   var reg = new RegExp('^[0-9]*$');                                                                                                                                                                                                                                                                                                                                              
	                   var obj = $(this);                                                                                                                                                                                                                                                                                                                                                             
	                   if (obj.val() != '') {                                                                                                                                                                                                                                                                                                                                                         
	                       if (!reg.test(obj.val())) { 
	                            $(this).next().text('请输入数字!');                                                                                                                                                                                                                                                                                                                                               
	                           $(this).addClass('errtext');                                                                                                                                                                                                                                                                                                                                
	                           $(this).focus();                                                                                                                                                                                                                                                                                                                                                       
	                           return false;                                                                                                                                                                                                                                                                                                                                                          
	                       }                                                                                                                                                                                                                                                                                                                                                                          
	                   }                                                                                                                                                                                                                                                                                                                                                                              
	               }
	                if(Number($(this).data('cms-lcd'))<$(this).val().length){                     
	                            $(this).next().text('长度超出了'+$(this).data('cms-lcd')+'个字符！');                                                                                                                                                                                                                                                                                                                                                
	                           $(this).addClass('errtext');                                                                                                                                                                                                                                                                                                        
	                           $(this).focus();                                                                                                                                                                                                                                                                                                                                                       
	                           return false; 
	                }                                                                                                                                                                                                                                                                                                                                                               
	           }                                                                                                                                                                                                                                                                                                                                                                                      
	       });                                                                                                                                                                                                                                                                                                                                                                                        
	                                                                                                                                                                                                                                                                                                                                                                                                  
	       function SumbitCheck() {                                                                                                                                                                                                                                                                                                                                                                   
	           var s = false;                                                                                                                                                                                                                                                                                                                                                                         
	           $('form input').each(function () {                                                                                                                                                                                                                                                                                                                                                     
	                if($(this).attr('type')!='hidden'){ 
	                   if($(this).data('cms-isnotnull')=='True'){                                                                                                                                                                                                                                                                                                                                                  
	                       if ($(this).val() == '') {        
	                            $(this).next().text('请输入内容');                                                                                                                                                                                                                                                                                                                                                
	                           $(this).addClass('errtext');                                                                                                                                                                                                                                                                                                                                                
	                           $(this).focus();                                                                                                                                                                                                                                                                                                                                                               
	                           s = true;                                                                                                                                                                                                                                                                                                                                                                      
	                           return false;                                                                                                                                                                                                                                                                                                                                                                  
	                       }
	                    }
	                }                                                                                                                                                                                                                                                                                                                                                                                     
	           });    
	           $(this).next().text('请输入内容');                                                                                                                                                                                                                                                                                                                                                                                
	           if (!s) {                                                                                                                                                                                                                                                                                                                                                                              
	            $('form').submit();
	           }                                                                                                                                                                                                                                                                                                                                                                                      
	       }
		</script>
		
	";
	return $htmlStr;
}

/**
 * 循环对比返回当然数据
 */
function GetTypeByName($name,$arraylist){

	foreach ($arraylist as $key => $value) {
		if ($value['fname']==$name) {
			return $value;
		}
	}
}

/**
 * 循环判断是否需要加密返回当然数据
 */
function GetPWDByName($name,$arraylist){

	foreach ($arraylist as $key => $value) {
		if ($value['fname']==$name&&$value['flx']=='password') {
			return $value;
		}
	}
}
