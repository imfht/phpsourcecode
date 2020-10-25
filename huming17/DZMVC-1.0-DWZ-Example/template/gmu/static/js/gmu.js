/*
顶部菜单
*/
$('#nav').navigator();
(function () {
    var bar = $('#nav2 span.bar'),
    left = $('#nav2 ul').offset().left;
    $('#nav2').navigator({
        select: function( e, index, li ){
            bar.css({
                left: li.offsetLeft - left,
                width: li.childNodes[0].offsetWidth
            });
        },
        ready: function() {
            bar.appendTo($('#nav2').find('.ui-scroller'));
        }
    });

    $('#arrow').on('click', function(){
        $('#nav2').iScroll( 'scrollTo', 100, 0, 400, true );
    });
})();

/*
瀑布流加载函数
*/
function waterfall(mod,cate_id,search_keyword,init_page) {
        /*组件初始化js begin*/
        $('#info_detail').hide();
        $('.swiper-container').show();
        $('#info_list').show();
        var scroll_page = 1;
        var cate_id = cate_id;
        $('.ui-refresh').css('height', window.innerHeight - ($('header').height() || 42)).refresh({
            statechange: function (e, $elem, state, dir) {
                if (state == 'loading') {   //只修改loading的状态
                    e.preventDefault();
                    var refreshInfo = this._options.refreshInfo[dir];
                    refreshInfo['$icon'].removeClass().addClass('ui-loading');
                    refreshInfo['$label'].html(dir == 'up' ? '正在刷新中...' : '数据加载中...');
                }
            },
            load: function (dir, type) {
                var me = this;
                var get_url = '';
                if(cate_id){
                    get_url = "&id="+cate_id;
                    $('#page_cate_id').val(cate_id);
                }else{
                    cate_id = $('#page_cate_id').val();
                    get_url = "&id="+cate_id;
                }
                if(search_keyword){
                    get_url = get_url + "&search_keyword="+search_keyword + get_url;
                }
                $.getJSON('./index.php?mod=ajax&action=content&do=scroll_page&page=' + scroll_page + get_url, function (data) {
                    var $list = $('.data-list'),
                            html = (function (data) {      //数据渲染
                                var liArr = [];
                                $.each(data, function () {
                                    liArr.push(this.html);
                                });
                                return liArr.join('');
                            })(data);
                            scroll_page = scroll_page + 1;
                    $list[dir == 'up' ? 'prepend' : 'append'](html);
                    me.afterDataLoading(dir);    //数据加载完成后改变状态
                });
            }
        });
        if(init_page){
            var me = this;
            var dir;
            var get_url = '';
            if(cate_id != '' && cate_id != 'undefined'){
                get_url = "&id="+cate_id;
                $('#page_cate_id').val(cate_id);
            }else{
                cate_id = $('#page_cate_id').val();
                if(cate_id){
                    get_url = "&id="+cate_id;
                }
            }
            if(search_keyword){
                get_url = get_url + "&search_keyword="+search_keyword + get_url;
            }
            $('.data-list').html('');
            $.getJSON('./index.php?mod=ajax&action=content&do=scroll_page&page=' + scroll_page + get_url + '&delay=0', function (data) {
                var $list = $('.data-list'),
                        html = (function (data) {      //数据渲染
                            var liArr = [];
                            $.each(data, function () {
                                liArr.push(this.html);
                            });
                            return liArr.join('');
                        })(data);
                        scroll_page = scroll_page + 1;
                $list[dir == 'up' ? 'prepend' : 'append'](html);
                //me.afterDataLoading(dir);    //数据加载完成后改变状态
            });
        }
        //alert('end:'+scroll_page);
    /*组件初始化js end*/
}

function info_detail(info_id){
		$('.swiper-container').hide();
    $('#info_list').hide();
    $('#wrap').hide();
    $.getJSON('./index.php?mod=ajax&action=content&do=info_detail&info_id=' + info_id + '&delay=2', function (data) {
        $('#info_detail_header_title').html(data['title']);
        $('#info_detail_content').html(data['content']);
    });
    $('#info_detail').show();
}