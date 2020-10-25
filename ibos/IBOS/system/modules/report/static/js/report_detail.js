define(['text!' + app.assetUrl + '/templates/default_detail.html',
    app.assetUrl + '/js/util.js'
], function(content, util){
    var reportId = 0,
        reportType = "",
        reportOrigin = "";
        
    var getTpl = function(data){
        return $.template(reportOrigin ? 'detail_list_header_tpl' : 'detail_header_tpl', {
            data: $.extend(data, {type: reportType}), 
            content: $.template('detail_content_tpl', data)
        });
    };
    var showPort = function(callback){
        appView.append( content );
        util.fetch('report/api/showreport&repid='+reportId + '&type=' + reportType).done(function(res){
            if( res.isSuccess ){
                callback && callback( getTpl(res.data) );
            }else{
                Ui.tip(res.msg, 'dangder');
            }
        });
    };

    var getCommentView = function(callback){
        util.fetch('report/api/getcommentview', {
            data: {
                repid: reportId,
                inajax: 1
            },
            contentType: 'application/x-www-form-urlencoded'
        }).done(function(res){
            callback && callback( $.template('detail_comment_tpl', {content: res.data}) );
        });
    };

    var getReader = function(callback){
        util.fetch('report/api/getreader', {
            data: JSON.stringify({
                repid: reportId
            })
        }).done(function(res){
            var users = [];
            for(var dept in res.data){
                var u = res.data[dept];
                for(var i=0, len=u.length; i<len; i++){
                    users.push(u[i]);
                }
            }
            callback && callback( $.template('detail_review_tpl', {data: users}) );
        });
    };

    var setStamp = function(data, callback){
        util.fetch('report/api/setstamp', {
            data: JSON.stringify(data)
        }).done(function(res){
            if(res.isSuccess){
                callback && callback();
            }else{
                Ui.tip(res.msg, 'danger');
            }
        });
    };
    var stamp = function($detail, repid, callback){
        // 图章
        var $commentBtn = $detail.find("[data-act='addcomment']");
        var $stampBtn = $detail.find('[data-toggle="stampPicker"]');

        if($stampBtn.length) {
            Ibosapp.stampPicker($stampBtn, Ibos.app.g('stamps'));
            $stampBtn.on("stampChange", function(evt, data) {
                // Preview Stamp
                setStamp({
                    repid: repid,
                    stampid: data.value
                }, function(){
                    var stamp = '<img src="' + data.stamp + '" width="150px" height="90px" />',
                        smallStamp = '<img src="'+ data.path + '" width="60px" height="24px" />',
                        $parentRow = $stampBtn.closest("div");

                    $("#preview_stamp_" + repid).html(stamp);
                    $('#report_stamp_' + repid).attr('src', data.path);
                    $parentRow.find(".preview_stamp_small").html(smallStamp);
                    $.extend($commentBtn.data("param"), { "stamp": data.value });
                });
            });
        }
        callback && callback();
    };

    return {
        setConfig: function(id, origin, type){
            reportId = id;
            reportOrigin = origin ? origin : '';
            reportType = type ? type : 'send'
        },
        showPort: showPort,
        getCommentView: getCommentView,
        getReader: getReader,
        stamp: stamp,
        toggle: function($el, act){
            var toggleSpeed = 100,
                $item = $el.parents("li").eq(0),
                $summary = $item.find(".rp-summary"),
                $detail = $item.find(".rp-detail"),
                $act = act === "hide";

            $detail[ $act ? 'slideUp' : 'slideDown' ](toggleSpeed);
            $summary[$act ? 'slideDown' : 'slideUp'](toggleSpeed);
            $item[$act ? 'removeClass' : 'addClass']("open");
        }
    };
});
//初始化表情函数
function initCommentEmotion($context) {
        //按钮[data-node-type="commentEmotion"]
    $('[data-node-type="commentEmotion"]', $context).each(function(){
        var $elem = $(this),
            $target = $elem.closest('[data-node-type="commentBox"]').find('[data-node-type="commentText"]');
            $elem.ibosEmotion({ target: $target });
        }
    );
}
