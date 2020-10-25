/**
 * Created by Administrator on 2017/9/26 0026.
 */
$(function(){
    var data = {} ;
    var flag = false ;
    $('[data-role="doInitUser"]').click(function(){
        if(flag) return ;
        flag = true ;
        var $this = $(this) ;
        $this.css('disabled', 'disabled') ;
        if($('[data-role="init-loading"]').length > 0){
            $('[data-role="init-loading"]').remove() ;
        }
        var url = $this.attr('data-url') ;
        var div = '<a class="init-loading" data-role="init-loading">' +
            '<img src="./Application/Admin/Static/images/loading_icon.gif" />' +
            '<span class="loading-persent"><span class="loading-number">0</span>%</span>' +
            '<span style="color:red;">   未完成初始化，关闭或刷新该页面，容易导致数据错乱~</span></a>' ;
        $this.after(div) ;
        data.type = 1 ;
        doInitUser($this, url) ;
    });
    var step_percent = 0 ;
    var percent = 0 ;//每次post后添加百分比
    var post_number = 0 ;
    var total = 0 ;
    function doInitUser($this, url) {
        $.post(url, data, function(res){
            post_number++ ;
            if(res.status == 1) {
                $('.loading-number').html(100) ;
                $this.css('disabled', false) ;
                setTimeout(function(){
                    $('[data-role="init-loading"]').remove() ;
                },2000) ;
                flag = false ;
                toast.success(res.info) ;
            }else if(res.status == 3){
                if(total == 0) {
                    if(res.step_number == undefined){
                        res.step_number = 0 ;
                    }
                    total = res.step_number ;
                    percent = 100/Number(res.step_number) ;
                    step_percent = percent ;
                }else{
                    step_percent = step_percent + percent ;
                }
                if(step_percent > 100) {
                    step_percent = 100 ;
                }
                $(document).find('.loading-number').html(Math.round(step_percent)) ;
                setTimeout(function(){
                    data.type = 0 ;
                    doInitUser($this, url) ;
                }, 100);
            }else if(res.status == 2){
                $('.loading-number').html(100) ;
                setTimeout(function(){
                    $('[data-role="init-loading"]').remove() ;
                }, 2000);
                toast.success(res.info) ;
            }else if(res.status == -1){

            }else{
                toast.error(res.info) ;
            }
        });
    }

});