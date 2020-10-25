<h2>添加关键字：</h2>
<hr class="mb10"></hr>

<script type="text/javascript" src="__APPURL__/js/articlecom.js"></script>
<script type="text/javascript">
        $(function(){
    	//判断初始值
		    var val = $('select[name="type"] option:selected').val();
    		if(val=='2'){
    			 $("#text").hide();
				 $("#article").show();
				 $("#arts-select").show();
    		}else{
			     $("#text").show();
				 $("#article").hide();
				 $("#arts-select").hide();
			}
		//是否是多图文显示
		var articleid = $('#articleid').val();
		var url = '{url("index/com_article_showone")}';
    	$('#mularticle').attr('src',url+'&id='+articleid);
		//选择类型事件
		$('select[name="type"]').bind("change",function(){
    		var val = $('select[name="type"] option:selected').val();
    		if(val=='2'){
    			 $("#text").hide();
				 $("#article").show();
				$("#arts-select").show();
				 //openscset();
    		}else{
			     $("#text").show();
				 $("#article").hide();
				 $("#arts-select").hide();
			}
    	});
		$('#arts-select').click(function(){
    		openscset();
    	});
    });
    //弹窗选择图文
    function openscset(){
		pophtml('<iframe src="{url("index/com_article_select")}" style="width:880px;height:470px;border:none;background-color: #dfdfdf;" width="880px" height="475px"></iframe>',920,510,true);
	}
	
    //返回选择图文id
   function setselid(id){
    	$('#articleid').attr('value',id);
		var url = '{url("index/com_article_showone")}';
    	$('#mularticle').attr('src',url+'&id='+id);
    }
	
	//返回选择图文id
   function setselid(id){
    	$('#articleid').attr('value',id);
		var url = '{url("index/com_article_showone")}';
    	$('#mularticle').attr('src',url+'&id='+id);
    }
</script>
  <FORM method="post" action="#">
    <DIV class="form_box">
        <TABLE>
            <TR>
              <TH>关键字：</TH>
              <TD><INPUT class="input w400" type="text" name="keyword"></TD>
            </TR>
            <TR>
              <TH>回复类型：</TH>
              <TD><SELECT name="type">
                  <OPTION selected value=1>文字消息</OPTION>
				  <OPTION value=2>图文消息</OPTION>
                </SELECT>
                <div class='button' id="arts-select" onclick="openscset()">选择图文</div>
			</TD>
            </TR>
			<TR id='text'>
              <TH>文字消息：</TH>
              <TD><TEXTAREA class="textarea w400 h80" name="content"></TEXTAREA></TD>
            </TR>
			<TR id='article'>
              <TH>图文消息：</TH>
              <TD><INPUT id="articleid" class="input w400" type="hidden" name="articleid">
			  <iframe id="mularticle" src="" height="500px" width="380px" ></iframe>
			  </TD>
            </TR>
        </TABLE>
      </DIV>
      <DIV class=btn>
        <INPUT class="button" value="确定" type="submit">
        <INPUT class="button" value="重置" type="reset">
      </DIV>
  </FORM>