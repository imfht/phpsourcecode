CKEDITOR.dialog.add( 'txvideoDialog', function ( editor ) {
    b = CKEDITOR.plugins.get("txvideo").path+"icons/"+"wxlogo.png";

    return {
        title: '插入视频',
        minWidth: 400,
        minHeight: 200,

        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'video_url',
                        label: '输入腾讯视频地址',
                        validate: CKEDITOR.dialog.validate.notEmpty( "Abbreviation field cannot be empty" ),
                    },
                    {
                        type: 'html',
                        html: '<p> <img style="width:20%" src="'+ b +'"> </p> <p style="padding:5px"> 腾讯视频地址：<a target="_blank" href="http://v.qq.com" style="color:#00B2CE ">http://v.qq.com</a> </p> <p style="padding:5px"> 注意：同微信后台编辑器，只支持腾讯视频和微视。【微视暂不支持】 </p> <p style="padding:5px"> 作者：Plusman <a target="_blank" href="http://my.oschina.net/u/1992642/blog" style="color:#00B2CE ">http://my.oschina.net/u/1992642/blog</a> </p>',
                    }                    
                ],  
            },
        ],

        onOk: function() {
            var dialog = this;
            // 获取腾讯视频地址输入
            video_url = dialog.getValueOf('tab-basic','video_url');
            // http://v.qq.com/cover/f/fupn4zdjxnb3if4.html?vid=m0110ndi49i
            url_obj = GetRequest(video_url);

            if(url_obj.host != 'v.qq.com' && url_obj.host != 'weishi.qq.com' ){
                alert('视频不是来自于微视或者腾讯视频');
            }else if(url_obj.vid != undefined){

                tx_player = '<iframe class="video_iframe" style="position:relative; z-index:1;" height="200" width="300" frameborder="0" src="http://v.qq.com/iframe/player.html?vid={$vid}&amp;width=300&amp;height=200&amp;auto=0" allowfullscreen=""></iframe>';
                tx_player = tx_player.replace(/\{\$vid\}/gi,url_obj.vid);

                editor.insertHtml(tx_player);

            }else{
                alert('无法获取到视频ID');
            }   
        },
    };
});


