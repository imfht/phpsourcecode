<?php if (!defined('THINK_PATH')) exit();?>
<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($updateUrl); ?>">
<input type="hidden" name="name" value="<?php echo ($vo["name"]); ?>" />
<div class="row">
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_title">显示标题</label>
                <input  value="<?php echo ($vo["title"]); ?>"  name="title" type="text" placeholder="" id="field_title" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_">模型类型</label>
                <select data-value="<?php echo ($vo["type"]); ?>" class="form-control" name="type">
            <option value="artice">文章</option>
            <option value="news">图文</option>
            <option value="album">相册</option>
            <option value="class">分类</option>
            <option value="dic">字典</option>
        </select>
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_">导航显示</label>
                <select data-value="<?php echo ($vo["is_nav"]); ?>" class="form-control" name="is_nav">
            <option value="1">显示</option>
            <option value="0">不显示</option>
        </select>
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_intro">备注</label>
                <input  value="<?php echo ($vo["intro"]); ?>"  name="intro" type="text" placeholder="" id="field_intro" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>


<script type="text/javascript">
$(function(){
    $('select[data-value]').each(function(i,d){
        var v = $(this).data('value');
        if(v) $(this).val(v);
    })
})    
</script>