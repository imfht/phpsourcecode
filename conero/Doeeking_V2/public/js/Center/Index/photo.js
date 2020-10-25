$(function(){
    //
    //Cro.alertTest();    
    // 加载更多
    $('#photo_load_more').click(function(){
        var pEl = $(this).parent("p");
        var div = pEl.prev("div.photo_wall");
        var dataid = parseInt(div.attr("dataid"));
        var max = parseInt($('#page_max').text());
        if(dataid < max){
            $.post('/conero/center/index/ajax/photo.html',{item:'more_photos',page:dataid+1},function(data){
                if(data) pEl.before(data);
            });
        }
        else{
            Cro.modal_alert("没有更多照片了！");
            $(this).attr('disabled','disabled');
        }
    });
    // 表单显示与隐藏
    $('.from_toggle').click(function(){
        var dataid = $(this).attr('dataid');
        $('#'+dataid).toggleClass('hidden');
    });
    // URL 地址图片地址检测
    $('#url_imgs').change(function(){
        var url = $(this).val();
        if(url){
            var xhtml = '<a href="'+url+'" target="_blank"><img src="'+url+'" class="img-thumbnail"></a>';
            $('#form_console').html(xhtml);        
            var tmpArr = url.split('/');
            var len = tmpArr.length-1;
            var fileName = len>0? tmpArr[len]:'';
            $('#fileName').val(fileName);
        }
    });
});
// 沙箱机制- photo
var PT = {
    about: function(dom){
        dom = $(dom);
        var title = dom.attr("title");
        var img = dom.find("img");
        var row = dom.attr("v-row");
        var count = 40;
        var src = img.attr("src");
        var content = '<img src="'+src+'" class="img-thumbnail">'
                      + '<p class="text-center" style="background:#5bc0de;">'
                      + '<a href="javascript:void(0);" dataid="last"><span class="glyphicon glyphicon-arrow-left"></span></a> '
                      + '<span class="current">'+row+'</span>'
                      + ' / '
                      + '<span class="imgamount">'+count+'</span> '
                      + ' <a href="javascript:void(0);" dataid="next"><span class="glyphicon glyphicon-arrow-right"></span></a>'
                      + ' <a href="'+src+'" target="_blank" dataid="export"><span class="glyphicon glyphicon-export"></span></a>'
                      + '</p>'
                      ;
        var photoWall = dom.parents("div.photo_wall");
        var func = {        
            bindEvent: ['last','next'],                  
            last: function(){
                var pEl = $(this).parents("p.text-center");
                var current = parseInt(pEl.find("span.current").text());
                if(current>1){
                    current = current - 1;
                    var newDom = photoWall.find('a[v-row="'+current+'"]');
                    var body = pEl.parents("div.modal-body");
                    // 更新标题
                    body.parents("div.modal-dialog").find("div.modal-header").find("h4.modal-title").text(newDom.attr("title"));
                    // 更新图片                    
                    var newsrc = newDom.find("img").attr("src");
                    body.find('img').attr("src",newsrc);
                    body.find('[dataid="export"]').attr("href",newsrc);
                    //Cro.log(lastDom.attr("title"),lastDom.find("img").attr("src"),body.find('img'),body);
                    pEl.find("span.current").text(current);
                }
                else{
                    Cro.modal_alert("这已经是第一张照片了！");
                }
            },
            next:function(){
                var pEl = $(this).parents("p.text-center");
                var current = parseInt(pEl.find("span.current").text());
                var max = parseInt(pEl.find("span.imgamount").text());
                if(current < max){
                    current = current + 1;
                    var newDom = photoWall.find('a[v-row="'+current+'"]');
                    var body = pEl.parents("div.modal-body");
                    // 更新标题
                    body.parents("div.modal-dialog").find("div.modal-header").find("h4.modal-title").text(newDom.attr("title"));
                    // 更新图片
                    var newsrc = newDom.find("img").attr("src");
                    body.find('img').attr("src",newsrc);
                    body.find('[dataid="export"]').attr("href",newsrc);
                    pEl.find("span.current").text(current);
                }
                else{
                    Cro.modal_alert("这已经是第最后一张照片了！");
                }
            }
        };
        Cro.modal({
            large:true,
            title:title,
            content:content
        },null,func);
    }
};