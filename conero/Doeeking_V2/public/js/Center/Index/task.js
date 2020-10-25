var app = Cro.extends(function(th){
    this.pageInit = function(){
        var endTaskList = [];
        // 结束选择任务 - 提交
        $('#task_end_tip').click(function(){
            var saveList = [];
            var endChk = $('.mk_task_end'),chk;
            for(var k =0; k<endChk.length; k++){
                if(endChk[k].checked == true){
                    chk = $(endChk[k]);
                    saveList.push(chk.parents('tr').attr("dataid"));
                }
            }
            th.log(saveList);
            if(saveList.length > 0){
                location.href = '/conero/center/index/save/task.html?task_end_tip=' + Base64.encode(saveList.join(','));
            }
            th.modal_alert('您还没有选择的可结束的提醒任务！');
        });      
        $('.mk_task_end').change(function(){     
            var endChk = $('.mk_task_end'),chk;
            for(var k =0; k<endChk.length; k++){
                chk = endChk[k];
                if(chk.checked == true){
                    $('#task_end_tip').attr("class","btn btn-info btn-sm");
                    return;
                }
            }
            $('#task_end_tip').attr("class","btn btn-defaut btn-sm");
            endTaskList = [];
        });
        // 数据删除确认
        $('.task_del_link').click(function(){
            var url = $(this).attr('href');
            th.confirm('您确定删除数据吗？',function(){
                location.href = url;
            });
            return false;
        });
    }
});
$(function(){
    app.pageInit();
});