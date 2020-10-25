<?php if (!defined('THINK_PATH')) exit();?><div class="page animated bounceInDown">



<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">云文件</div></div><div id="modelObj-action" class="block-controls"><a grid-btn grid-delete class="btn disabled btn-danger btn-sm" href="<?php echo ($delUrl); ?>">删除</a></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            
<div class="table-responsive">
    <table id="modelObj" class="table table-bordered table-striped table-hover">
        <thead>
            <th w_render="fileUrl">文件名</th>
            <th w_index="type">文件类型</th>
            <th w_index="size">文件大小</th>
        </thead>
    </table>
</div>
<div id="modelObj-pager" class="widget-footer"></div>
                        </div>
                        </div>
                    </div>
                </div>

<script type="text/javascript">
// var modelObj;
$(function(){
    var pageSize = 10;
    if (!pageSize) pageSize = 10
    var url = 0;
    if (!url) url = "<?php echo U('AcFile/index');?>";
    modelObj = $.fn.bsgrid.init('modelObj', {
        url: url,
        pageSize: pageSize,
        // autoLoad:false,
        displayBlankRows:false,
        event: {
            selectRowEvent: function (record, rowIndex, trObj, options) {
                
                var id = modelObj.getRecordIndexValue(record, 'id');
                $('#modelObj-action').find('a[grid-btn]').each(function(idx,it){
                    $(this).removeClass('disabled');
                    var u = $(this).attr('href');
                    u = U('id',id,u);
                    $(this).attr('href',u);
                });
                
            },
            unselectRowEvent: function (record, rowIndex, trObj, options) {
                var name = modelObj.getRecordIndexValue(record, 'name');
                $('#modelObj-action').find('a[grid-btn]').each(function(idx,it){
                    $(this).addClass('disabled');
                    var u = $(this).attr('href');
                    u = U('name','',u);
                    $(this).attr('href',u);
                })
            }
        },
    });


    $('#modelObj-action').find('[grid-dialog]').on('click',function(event){
        event.preventDefault();
        var box = $('<div style="max-height:500px;overflow-y: auto;" class="row"></div>');
        var size = $(this).attr('dialog-size');
        if (!size) size = 'size-wide';
        var url = $(this).attr('href');
        var title = $(this).attr('dialog-title');
        box.load(url);
        BootstrapDialog.show({
            message: box,
            size:size,
            title:title,
            buttons: [{
                label: '确定',
                action: function(dialogRef) {
                    var form = dialogRef.getModalBody().find('form');
                    if (form.length > 0) {
                        var url = form.attr('action');
                        var data = form.serialize();
                        $.post(url,data,function(req){
                            $.bootstrapGrowl(req.info);
                            if (req.status == 1) {
                                dialogRef.close();
                                modelObj.refreshPage();
                            }
                        })
                    }else{
                        dialogRef.close();
                        modelObj.refreshPage();
                    }
                }
            },{
                label: '取消',
                action: function(dialogRef){
                    dialogRef.close();
                }
            }]
        });
    })


    $('#modelObj-action').find('[grid-delete]').confirmation({
        container: 'body',
        href:false,
        onConfirm:function(event, element){
            event.preventDefault();
            el  = $(element);
            var url = el.attr('href');
            $.get(url,function(req){
                $.bootstrapGrowl(req.info);
                var st = req.status;
                modelObj.refreshPage();
            })
        }
    });



})
</script>



<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block upload">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">文件上传</div></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <input multiple="true" id="inputFile" name="file" type="file" class="file-loading">
                        </div>
                        </div>
                    </div>
                </div>

</div>

<script type="text/javascript">
$(function(){
    $("#inputFile").fileinput({
        uploadUrl: "http://d.apicloud.com/mcm/api/file", 
        showUpload:false,
        uploadExtraData:{
            type:'image/jpeg',
        },
        ajaxSettings:{
            headers:{
                "Accept":"*/*",
                "X-APICloud-AppId":"<?php echo (session('app_id')); ?>",
                "X-APICloud-AppKey":"<?php echo (session('appKey')); ?>",
            }
        }
    });

    $('#inputFile').on('fileuploaded', function(event, data, previewId, index) {
        modelObj.refreshPage();
    });

})

function fileUrl(record, rowIndex, colIndex, options) {
    var name = modelObj.getRecordIndexValue(record, 'name');
    var url = modelObj.getRecordIndexValue(record, 'url');

    return '<a target="_blank" href="'+url+'" >'+name+'</a>';
} 
   
</script>