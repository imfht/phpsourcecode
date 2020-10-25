<!--{template header}-->
<script type="text/javascript" src="{SITE_URL}static/js/jquery-ui/jquery-ui.js"></script>
<script src="{SITE_URL}static/js/admin.js" type="text/javascript"></script>

<style>
em{
	color:red;
}
</style>
<div style="width:100%; height:15px;color:#000;margin:0px 0px 10px;">
    <div style="float:left;"><a href="index.php?admin_main/stat{$setting['seo_suffix']}" target="main"><b>控制面板首页</b></a>&nbsp;&raquo;&nbsp;专题管理</div>
</div>
<!--{if isset($message)}-->
<table class="table">
    <tr>
        <td class="{if isset($type)}$type{/if}">{$message}</td>
    </tr>
</table>
<!--{/if}-->
   <a class="btn write-btn btn-success" target="_blank" href="{url user/addxinzhi}">
            <i class="fa fa-pencil"></i>写文章
        </a>
           <a class="btn write-btn btn-success"  href="{url admin_topic/shenhe}">
            <i class="fa fa-check"></i>文章审核
        </a>
            <a class="btn write-btn btn-success"  href="{url admin_topic/vertifycomments}">
            <i class="fa fa-check"></i>文章评论审核
        </a>
<form  method="post">
    <table class="table">
        <tbody>
            <tr class="header" ><td colspan="4">文章列表</td></tr>
            <tr class="altbg1"><td colspan="4">可以通过如下搜索条件，检索文章</td></tr>
            <tr>
                <td width="200"  class="altbg2">标题:<input class="txt form-control" name="srchtitle" {if isset($srchtitle)}value="{$srchtitle}" {/if}></td>
                <td  width="200" class="altbg2">作者:<input class="txt form-control" name="srchauthor" {if isset($srchauthor)}value="{$srchauthor}" {/if}></td>

                 <td  width="200" class="altbg2">分类:
                    <select class="form-control shortinput" name="srchcategory" id="srchcategory"><option value="0">--不限--</option>{$catetree}</select>
                </td>
            </tr>

            <tr>
              <td  rowspan="2" class="altbg2"><input class="btn btn-info" name="submit" type="submit" value="查询"></td>
              </tr>
        </tbody>
    </table>
</form>
[共 <font color="green">{$rownum}</font> 个文章]
<form name="answerlist"  method="POST">
    <table class="table">
        <tr class="header" align="center">
            <td width="10%"><input class="checkbox" value="chkall" id="chkall" onclick="checkall('tid[]')" type="checkbox" name="chkall"><label for="chkall">选择</label></td>
            <td  width="10%">博客作者</td>

            <td width="20%">博客名称</td>

             <td  width="10%">阅读量</td>
                <td  width="10%">评论数</td>
            <td  width="10%">编辑</td>
        </tr>



</table>
            <table  id="table1" class="table">
                    <!--{loop $topiclist $topic}-->
                <tr align="center" class="smalltxt">

                    <td width="10%" class="altbg2"><input class="checkbox" type="checkbox" value="{$topic['id']}" name="tid[]"></td>
                                      <td width="10%" class="altbg2">
                                      <strong>{$topic['author']}</strong></td>

                    <td width="20%" class="altbg2"><input name="order[]" type="hidden" value="{$topic['id']}"/><strong><a target="_blank" href="{url topic/getone/$topic['id']}">
                    {if $topic['state']==0}<label class="label label-warning">等待审核</label>{/if}{$topic['title']}
                    </a></strong></td>

                           <td width="10%" class="altbg2" align="center">{$topic['views']}</td>
                                  <td width="10%" class="altbg2" align="center">{$topic['articles']}</td>
                    <td width="10%" class="altbg2" align="center"><a target="_blank" href="{url admin_topic/edit/$topic['id']}">编辑</a></td>
                </tr>
         
      
       
   <!--{/loop}-->
    </table>
   <div class="pages">{$departstr}</div>


             <input name="ctrlcase" class="btn btn-success" type="button" onClick="buttoncontrol(1);" value="推送到百度">&nbsp;&nbsp;&nbsp;

    <input class="button" tabindex="3" onClick="buttoncontrol(2)" type="submit" value=" 删除" name="ctrlcase">
</form>
<div class="modal fade" id="baidutui">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
      <h4 class="modal-title">百度推送提醒</h4>
    </div>
    <div class="modal-body">
      <p>确定推送？此项操作只有配置了百度推送api地址有效！</p>
    </div>
    <div class="modal-footer">
     <button type="button" id="btntui" class="btn btn-primary">确定推送</button>
     <button type="button"  class="btn btn-primary" onclick="window.location.href='index.php?admin_setting/seo{$setting['seo_suffix']}'">去配置百度推送api地址</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>

    </div>
  </div>
</div>
</div>
<br>

<!--{template footer}-->
<script>
function buttoncontrol(num) {

	  if ($("input[name='tid[]']:checked").length == 0) {
          alert('你没有选择任何要操作的文章！');
          return false;
      }else{
    	   switch (num) {
           case 1:
           	$("#baidutui").modal("show");

           	$("#btntui").click(function(){
           		 document.answerlist.action = "index.php?admin_topic/baidutui{$setting['seo_suffix']}";
                   console.log( document.answerlist);
           		 document.answerlist.submit();
           	})

               break;
           case 2:
        	   if (confirm('确定删除问题？该操作不可返回！') == false) {
                   return false;
               } else {
               document.answerlist.action = "index.php?admin_topic/remove{$setting['seo_suffix']}";
               document.answerlist.submit();
               }
               break;

    	   }
      }
}
{if $srchcategory}
$(document).ready(function(){
    $("#srchcategory option").each(function(){
        if($(this).val()==$srchcategory){
            $(this).prop("selected","true");
        }
    });
});
{/if}
</script>