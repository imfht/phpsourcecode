<!--{template header}-->
<link rel="stylesheet" media="all" href="{SITE_URL}static/css/common/commtag.css" />


<div id="pagelet-write"  class="container index mar-t-1 mar-b-1 " style="margin-top:0px;">


<div class="row main">
 <div class="site_notes side-box ">
<center><b>{if isset($touser)}您正在向<a href="{url user/space/$touser['uid']}">{$touser['username']}</a>提问{else}{/if}</b></center>
<hr>
 
</div>

<div class="col-md-24 b-r-line" style="font-size: 13px;">
 <form class="form-horizontal"  enctype="multipart/form-data" method="POST"  name="askform" id="askform"  >





  <div class="form-group">

          <div class="col-md-24">
            <input type="text" data-toggle="tooltip" data-placement="bottom" title="标题最长不超过50个字" autocomplete="OFF" maxlength="50" class="title" placeholder="标题简短，言简意赅，问号结尾" name="title" id="qtitle" value="{$word}" />

          </div>
        </div>

    <div class="form-group">
          <p class="col-md-24">问题补充(选填)</p>
          <div class="col-md-24">
           <div id="introContent">
                <!--{template editor}-->
                    </div>

          </div>
        </div>
          <div class="form-group">
            <div class="col-md-12 ">

            <span id="selectedcate" class="selectedcate label"></span>
                        <span><a class="btn btn-default" data-toggle="modal" data-target="#myLgModal" id="changecategory" href="javascript:void(0)">选择分类</a>
          </div>
          <div class="col-md-10 ">

             <span>财富值悬赏&nbsp;<select name="givescore" id="scorelist"><option selected="selected" value="0">0</option><option value="3">3</option><option value="5">5</option><option value="10">10</option><option value="15">15</option><option value="30">30</option><option value="50">50</option><option value="80">80</option><option value="100">100</option></select></span>
                        <!--{if $user['uid']}-->
                        <span><input type="checkbox" name="hidanswer" id="hidanswer" value="1" />&nbsp;匿名</span>
                        <!--{/if}-->
          </div>
        </div>

        <div class="form-group" >

          <div class="col-md-24 dongtai ">
          <div class="tags">
      
          </div>
            <input type="text" autocomplete="off"  data-toggle="tooltip" data-placement="bottom" title="" placeholder="检索标签，最多添加5个,添加标签更容易被回答" data-original-title="检索标签，最多添加5个" name="topic_tag" value=""  class="txt_taginput" >
            <i class="fa fa-search"></i>
                <span class="label hand choosetag" onclick="showcommontag()">选择热门标签</span>
           
           <div class="tagsearch">
        
          
           </div>
            
          </div>
      
</div>
     {if $setting['openwxpay']==1}
               <div class="form-group    <!--{if $touser['mypay']>0}--> hide   <!--{/if}-->">


              <p class="col-md-24 "><i class="fa fa-credit-card"></i>被采纳打赏金额(选填):【可用余额{echo $user['jine']/100;}元】</p>

          <div class="col-md-10">
           <input type="text"  data-toggle="tooltip" data-placement="bottom" title="如果平台账户没有零钱请进入用户中心充值"  autocomplete="OFF" class="form-control" placeholder="打赏金额不超过200元" name="xianjin" id="qxianjin" value="" />

          </div>

        </div>

     {/if}

        <!--{if $setting['code_ask']&&$user['credit1']<$setting['jingyan']}-->
         <div class="form-group">

          <div class="col-md-24" style="margin-left:15px">
 {template code}
  </div>

        </div>
   <!--{/if}-->




          <div class="form-group">

          <div class="col-md-9" >
            <input type="hidden" name="asksid" id="asksid" value='{$_SESSION["asksid"]}'/>
                    <input type="hidden" name="cid" id="cid"/>
                    <input type="hidden" name="cid1" id="cid1" value="0"/>
                    <input type="hidden" name="cid2" id="cid2" value="0"/>
                    <input type="hidden" name="cid3" id="cid3" value="0"/>
                    <input type="hidden" value="{$askfromuid}" id="askfromuid" name="askfromuid" />
                          <input type="hidden" id="tokenkey" name="tokenkey" value='{$_SESSION["addquestiontoken"]}'/>
                       <!--{if $touser}-->
                         <!--{if $touser['mypay']>0}-->
                           <button type="button"  class="btn btn-fufei "  id="asksubmit"><i class="fa fa-qian"></i>付费{$touser['mypay']}元提问</button>
                          <!--{else}-->
                            <button type="button"  class="btn btn-success"  id="asksubmit">提交问题</button>
                            <!--{/if}-->

               <!--{else}-->
                            <button type="button"  class="btn btn-success"  id="asksubmit">提交问题</button>
                            <!--{/if}-->
          </div>

        </div>
  </form>
    <script>

                $("#asksubmit").click(function(){
                   
                	if(gettagsnum()>5){
                     alert("最多添加5个标签");
                     return false;
                	}
                	 var _tagstr=gettaglist();
                	
                	 var qtitle = $("#qtitle").val();

                	    var money_reg = /\d{1,4}/;
                        var _money = $("#qxianjin").val();
                        if('' == _money){
                        	_money=0;
                        }


                     if (bytes($.trim(qtitle)) < 8 || bytes($.trim(qtitle)) > 100) {
                    	 alert("问题标题长度不得少于4个字，不能超过50字！");

                         $("#qtitle").focus();
                         return false;
                     }
                     if ($("#selectedcate").html() == '') {

                     $('#myLgModal').modal('show');
                             return false;
                     }
                     {if $user['uid']}
                     //检查财富值是否够用
                     var offerscore = 0;
                     var selectsocre = $("#givescore").val();
                     if ($("#hidanswer:selected").val() == 1) {
                         offerscore += 10;
                     }
                     offerscore += parseInt(selectsocre);
                     if (offerscore > $user['credit2']) {
                    	  new $.zui.Messager("你的财富值不够!", {
                         	   close: true,
                         	    placement: 'center' // 定义显示位置
                         	}).show();

                             return false;
                     }
                     {/if}

                	 //var eidtor_content= editor.getContent();
                    	 var eidtor_content='';
                    	 if(typeof testEditor != "undefined"){
                       	  var tmptxt=$.trim(testEditor.getMarkdown());
                       	  if(tmptxt==''){
                       		  alert("回答内容不能为空");
                       		  return;
                       	  }
                       	  eidtor_content= testEditor.getHTML();
                         }else{
                       	  if (typeof UE != "undefined") {
                       			 eidtor_content= editor.getContent();
                       		}else{
                       			 eidtor_content= $.trim($("#editor").val());
                       		}
                         }
                	 var _hidanswer=0;
                	 if ($('#hidanswer').is(':checked')) {
                		 _hidanswer=1;
                	 }else{
                		 _hidanswer=0;
                	 }


                	  <!--{if $setting['code_ask']}-->
                	  var data={
                			  title:$("#qtitle").val(),
                			  description:eidtor_content,
                			  cid:$("#cid").val(),
                			  cid1:$("#cid1").val(),
                			  cid2:$("#cid2").val(),
                			  cid3:$("#cid3").val(),
                			  givescore:$("#scorelist").val(),
                			  hidanswer:_hidanswer,
                			  askfromuid:$("#askfromuid").val(),
                			  tokenkey:$("#tokenkey").val(),
                  			  jine:_money,
                  			  tags:_tagstr,
                  			code:$("#code").val()
                  	}
                	    <!--{else}-->
                		var data={
                				  title:$("#qtitle").val(),
                    			  description:eidtor_content,
                    			  cid:$("#cid").val(),
                    			  cid1:$("#cid1").val(),
                    			  cid2:$("#cid2").val(),
                    			  cid3:$("#cid3").val(),
                    			  tokenkey:$("#tokenkey").val(),
                    			  jine:_money,
                    			  tags:_tagstr,
                    			  givescore:$("#scorelist").val(),
                    			  hidanswer:_hidanswer,
                    			  askfromuid:$("#askfromuid").val()



                    	}
                	     <!--{/if}-->


                	$.ajax({
                        //提交数据的类型 POST GET
                        type:"POST",
                        //提交的网址
                        url:"{url question/ajaxadd}",
                        //提交的数据
                        data:data,
                        //返回数据的格式
                        datatype: "json",//"xml", "html", "script", "json", "jsonp", "text".
                        //在请求之前调用的函数
                        beforeSend:function(){
                        	  ajaxloading("提交中...");
                        },
                        //成功返回之后调用的函数
                        success:function(data){
                        	$(".progress").addClass("hide");
                        	var data=eval("("+data+")");
                           if(data.message=='ok'){
                        	   var tmpmsg='提问成功!';
                        	   if(data.sh=='1'){
                        		   tmpmsg='问题发布成功！为了确保问答的质量，我们会对您的提问内容进行审核。请耐心等待......';
                        	   }
                        	   new $.zui.Messager(tmpmsg, {
                        		   type: 'success',
                        		   close: true,
                           	    placement: 'center' // 定义显示位置
                           	}).show();
                        	   setTimeout(function(){

                                   window.location.href=data.url;
                               },1500);
                           }else{
                        	   new $.zui.Messager(data.message, {
                            	   close: true,
                            	    placement: 'center' // 定义显示位置
                            	}).show();
                           }


                        }   ,
                        //调用执行后调用的函数
                        complete: function(XMLHttpRequest, textStatus){
                        	 removeajaxloading();
                        },
                        //调用出错执行的函数
                        error: function(){
                            //请求出错处理
                        }
                     });
                })


                </script>


{if $setting['register_on']=='1'}
{if $user['uid']>0&&$user['active']!=1}
<div class="modal fade" id="emailtip">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
      <h4 class="modal-title">温馨提示</h4>
    </div>
    <div class="modal-body">
      <p>由于网站设置，需要设置邮箱并且激活邮箱才能提问,回答，发布文章等一系列操作,<a class="text-danger mar-ly-1" href="{url user/editemail}"> 点击激活邮箱!</a></p>

    </div>

  </div>
</div>
</div>
<script>
$("#emailtip").modal({
	backdrop : 'static',
    show     : true
})
</script>
{/if}
{/if}
        <div class="modal fade" id="myLgModal">
  <div class="modal-dialog modal-md" style="width: 460px; top: 50px;">
    <div class="modal-content">

     <div id="dialogcate">
        <table class="table ">
            <tr valign="top">
                <td width="125px">
                    <select  id="category1" class="catselect" size="8" name="category1" ></select>
                </td>
                <td align="center" valign="middle" width="25px"><div style="display: none;" id="jiantou1">>></div></td>
                <td width="125px">
                    <select  id="category2"  class="catselect" size="8" name="category2" style="display:none"></select>
                </td>
                <td align="center" valign="middle" width="25px"><div style="display: none;" id="jiantou2">>>&nbsp;</div></td>
                <td width="125px">
                    <select id="category3"  class="catselect" size="8"  name="category3" style="display:none"></select>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                <span>
                    <input  type="button" class="btn btn-success" value="确&nbsp;认" onclick="selectcate();"/></span>
                    <span>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </span>

                </td>
            </tr>
        </table>
    </div>

    </div>

    </div>

    </div>
</div>

</div>

</div>


<style>
<!--
.btn.focus, .btn:focus, .btn:hover {
    color: #f5f5f5;
    text-decoration: none;
}
.choosetag {
    display: inline;
    padding: .2em .6em .2em;
    font-size: 75%;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background-color: grey;
    border-radius: .25em;
}
.md_taglist{

min-height:200px;
max-height:300px;
overflow-y:scroll;
}
.md_taglist .md_tagitem{
    display:inline-block;
    text-align: center;
    margin:10px;
}
.md_taglist .md_tagitem .label{
    position: relative;
    display: inline-block;
    height: 30px;
    padding: 0 12px;
    font-size: 14px;
    line-height: 30px;
    color: #0084FF;
    vertical-align: top;
    border-radius: 100px;
    background: rgba(0, 132, 255, 0.1);
cursor:pointer;
}
.md_taglist .md_tagitem .active{
border:dashed 2px #ea644a;
}
-->
</style>
 <div class="modal fade" id="commontag" >
  <div class="modal-dialog modal-md" style="width: 460px; top: 50px;">
    <div class="modal-content">
          <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
        <h4 class="modal-title">选择热门标签</h4>
      </div>
        <div class="modal-body">
            <div class="md_taglist">
                     {eval  $comtaglist=$this->getlistbysql("select * from ".$this->db->dbprefix."tag  order by tagquestions desc,tagarticles desc  limit 0,100");}
        {if $comtaglist}
          <!--{loop $comtaglist $index $comtag}-->
               <div class="md_tagitem">
                                    <label tagid="{$comtag['id']}" class="label " >{$comtag['tagname']}</label> 
               </div>
                <!--{/loop}-->
                    {/if} 
            </div>
            
            <center><button class="btn btn-primary" type="button" style="background:#3280fc;" onclick="hidecommontag()">关闭</button></center>
         </div>
    </div>
  </div>
</div>


<!--用户中心结束-->
<script type="text/javascript">
function showcommontag(){
	$("#commontag").modal("show");
}
function hidecommontag(){
	$("#commontag").modal("hide");
}
$(".md_taglist .md_tagitem .label").click(function(){
	var _tagname=$.trim($(this).html());
	var _tagid=$.trim($(this).attr("tagid"));
	if(gettagsnum()>=5){
		alert("标签最多添加5个");
		return false;
	}
	if(checktag(_tagname)){
		alert("标签已存在");
		return false;
	}
	$(".dongtai .tags").append('<div class="tag"><span tagid="'+_tagid+'">'+_tagname+"</span><i class='fa fa-close'></i></div>");
	$(".dongtai .tags .tag  .fa-close").click(function(){
		$(this).parent().remove();
	});
	$(".tagsearch").html("");
	$(".tagsearch").hide();
	$(".txt_taginput").val("");
	});
function delHtmlTag(str)
{
    return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
}
function trim(str) {
	  return str.replace(/(^\s+)|(\s+$)/g, "");
	}
$(".txt_taginput").on(" input propertychange",function(){
	 var _txtval=$(this).val();
	 if(_txtval.length>1){
	
		 //检索标签信息
		 var _data={tagname:_txtval};
		 var _url="{url tags/ajaxsearch}";
		 function success(result){
			 console.log(result)
			 if(result.code==200){
				 console.log(_txtval)
				  $(".tagsearch").html("");
				for(var i=0;i<result.taglist.length;i++){
			
					 var _msg=result.taglist[i].tagname
					 
			           $(".tagsearch").append('<div class="tagitem" tagid="'+result.taglist[i].id+'">'+_msg+'</div>');
				}
				if(result.taglist.length>0){
					$(".tagsearch").show();
				}else{
					
					$(".tagsearch").hide();
				
				}
				
				$(".tagsearch .tagitem").click(function(){
					var _tagname=$.trim($(this).html());
					var _tagid=$.trim($(this).attr("tagid"));
					if(gettagsnum()>=5){
						alert("标签最多添加5个");
						return false;
					}
					if(checktag(_tagname)){
						alert("标签已存在");
						return false;
					}
					$(".dongtai .tags").append('<div class="tag"><span tagid="'+_tagid+'">'+_tagname+"</span><i class='fa fa-close'></i></div>");
					$(".dongtai .tags .tag  .fa-close").click(function(){
						$(this).parent().remove();
					});
					$(".tagsearch").html("");
					$(".tagsearch").hide();
					$(".txt_taginput").val("");
					});
		        
			 }
			 
		 }
		 ajaxpost(_url,_data,success);
	 }else{
			$(".tagsearch").html("");
			$(".tagsearch").hide();
	 }
})
	function checktag(_tagname){
		var tagrepeat=false;
		$(".dongtai .tags .tag span").each(function(index,item){
			var _tagnametmp=$.trim($(this).html());
			if(_tagnametmp==_tagname){
				tagrepeat=true;
			}
		})
		return tagrepeat;
	}
	function gettaglist(){
		var taglist='';
		$(".dongtai .tags .tag span").each(function(index,item){
			var _tagnametmp=$.trim($(this).attr("tagid"));
			taglist=taglist+_tagnametmp+",";
			
		})
		taglist=taglist.substring(0,taglist.length-1);
	
		return taglist;
	}
	function gettagsnum(){
      return $(".dongtai .tags .tag").length;
	}
	$(".tagsearch .tagitem").click(function(){
		var _tagname=$.trim($(this).html());
		if(gettagsnum()>=5){
			alert("标签最多添加5个");
			return false;
		}
		if(checktag(_tagname)){
			alert("标签已存在");
			return false;
		}
		$(".dongtai .tags").append('<div class="tag"><span>'+_tagname+"</span><i class='fa fa-close'></i></div>");
		$(".dongtai .tags .tag  .fa-close").click(function(){
			$(this).parent().remove();
		});
		$(".tagsearch").html("");
		$(".tagsearch").hide();
		$(".txt_taginput").val("");
		});
	$(".dongtai .tags .tag  .fa-close").click(function(){
		$(this).parent().remove();
	});
window.onload=function(){
	$("#askform .title ").focus();
	$(".tagchoose").click(function(){
		var _title=$("#qtitle").val();
		 var eidtor_content='';
  		if(isueditor==1){
  			  eidtor_content = editor.getContent();
  		}else{
  			eidtor_content = editor.wang_txt.html();
  		}
  		var mystr1=delHtmlTag(_title);
  		var mystr2=delHtmlTag(eidtor_content);
  		var mystr=trim(mystr2+mystr1);
  		mystr=mystr.replace(" ；","")
  		mystr=mystr.replace("&NBSP；","")
  		mystr=mystr.replace(" ；","")  //等几种 不同大小写情况
  		mystr = mystr.replace(/&nbsp;/ig,'');
  		console.log(mystr);
  		tagchoose(mystr);
	})
}
	var category1 = {$categoryjs[category1]};
    var category2 = {$categoryjs[category2]};
    var category3 = {$categoryjs[category3]};
        $(document).ready(function() {

      //  initcategory(category1);
            initcategory(category1);
            fillcategory(category2, $("#category1 option:selected").val(), "category2");
            fillcategory(category3, $("#category2 option:selected").val(), "category3");
    });




    function selectcate() {
        var selectedcatestr = '';
        var category1 = $("#category1 option:selected").val();
        var category2 = $("#category2 option:selected").val();
        var category3 = $("#category3 option:selected").val();
        if (category1 > 0) {
            selectedcatestr = $("#category1 option:selected").html();
            $("#cid").val(category1);
            $("#cid1").val(category1);
        }
        if (category2 > 0) {
            selectedcatestr += " > " + $("#category2 option:selected").html();
            $("#cid").val(category2);
            $("#cid2").val(category2);
        }
        if (category3 > 0) {
            selectedcatestr += " > " + $("#category3 option:selected").html();
            $("#cid").val(category3);
            $("#cid3").val(category3);
        }
        $("#selectedcate").html(selectedcatestr);
        $("#changecategory").html("更改");
        $('#myLgModal').modal('hide');
    }



</script>

<!--{template footer}-->