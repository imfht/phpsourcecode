define([app.assetUrl + '/js/util.js',
    app.assetUrl + '/js/report_detail.js'
], function(util, ReportDetail){
    return function(repid){
        var args = arguments,
            type = location.hash.slice(1).split('/')[0];
        done = typeof args[0] === 'function' ? args[0] : args[1];

        util.queue([function(done){
            ReportDetail.setConfig(repid, '', type);
            ReportDetail.showPort(function(data){
                appView.find('.detail-content').html( data );
                done();
            });
        },function(done){
            ReportDetail.getCommentView(function(data){
                appView.find('.detail-comment').html( data );
                done();
            });
        },function(done){
            ReportDetail.getReader(function(data){
                appView.find('.detail-reviewer').html( data );
                done();
            });
        }, function(done){
            ReportDetail.stamp(appView, repid, function(){
                done();
            });
        }], function(){
            done();
        });
        Ibos.events.add({
            removeReport: function(param, elem){
                Ui.confirm("确定要删除吗？", function(){
                    util.fetch('report/api/delreport', {
                        data: JSON.stringify({
                            repids: param.id
                        })
                    }).done(function(res){
                        if( res.isSuccess ){
                            Ui.tip(res.msg);
                            location.hash = "send";
                        }else{
                            Ui.tip(res.msg, 'danger');
                        }
                    });
                });
            }
        });
    };
});