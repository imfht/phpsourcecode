<?php if (!defined('THINK_PATH')) exit();?>
<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($updateUrl); ?>">
<input type="hidden" name="table" value="<?php echo ($table); ?>" />
<input type="hidden" name="field" value="<?php echo ($vo["name"]); ?>" />
<input type="hidden" name="name" value="<?php echo ($vo["name"]); ?>" />
<div class="row">
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_title">显示标题</label>
                <input  value="<?php echo ($vo["title"]); ?>"  name="title" type="text" placeholder="" id="field_title" class="form-control ">
                </div>
            </div>
    <div class=" col-md-8 col-sm-8 col-xs-8 col-lg-8">
                    <div class="form-group">
                    <label for="field_">数据类型</label>
                <select data-value="<?php echo ($vo["type"]); ?>" class="form-control" name="type">
            <option value="text">text</option>
            <option value="ueditor">ueditor</option>
            <option value="textarea">textarea</option>
            <option value="richrext">richrext</option>
            <option value="String">String</option>
            <option value="number">Number</option>
            <option value="boolean">Boolean</option>
            <option value="date">Date</option>
            <option value="file">File</option>
            <option value="array">Array</option>
            <option value="object">Object</option>
            <option value="pointer">Pointer</option>
            <option value="relation">Relation</option>
        </select>
                </div>
            </div>
    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4 pointer hide">
                    <div class="form-group">
                    <label for="field_pointer">Pointer</label>
                <input  value="<?php echo ($vo["pointer"]); ?>"  name="pointer" type="text" placeholder="请输入关联的模型" id="field_pointer" class="form-control ">
                </div>
            </div>
    <div class="clearfix"></div>

    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4">
                    <div class="form-group">
                    <label for="field_">列表页显示</label>
                <select data-value="<?php echo ($vo["showList"]); ?>" class="form-control" name="showList">
            <option value="1">显示</option>
            <option value="0">不显示</option>
        </select>
                </div>
            </div>

    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4">
                    <div class="form-group">
                    <label for="field_">新增页显示</label>
                <select data-value="<?php echo ($vo["showAdd"]); ?>" class="form-control" name="showAdd">
            <option value="1">显示</option>
            <option value="0">不显示</option>
        </select>
                </div>
            </div>
    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4">
                    <div class="form-group">
                    <label for="field_">修改页显示</label>
                <select data-value="<?php echo ($vo["showEdit"]); ?>" class="form-control" name="showEdit">
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
        $(this).val(v);
    })
    var p = $('[name=type]').val();
    if(p == 'pointer')
    {
        $('.pointer').removeClass('hide');
    }else{
        $('[name=pointer]').val('');
        $('.pointer').addClass('hide');
    }

    $('[name=type]').on('change',function(){
        var v = $(this).val();
        if(v == 'pointer')
        {
            $('.pointer').removeClass('hide');
        }else{
            $('[name=pointer]').val('');
            $('.pointer').addClass('hide');
        }
    })
})    
</script>