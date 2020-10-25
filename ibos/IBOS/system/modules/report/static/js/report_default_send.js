define([
    app.assetUrl + '/js/util.js',
    app.assetUrl + '/js/report_list.js',
    app.assetUrl + '/js/report_detail.js'
], function(util, Report, ReportDetail){
    return function(done){
        util.queue([function(done){
            require(['text!'+ app.assetUrl +'/templates/default_send.html'], function(tpl){
                appView.html( tpl );
                done();
            });
        }, function(done){
            Report.Info.init("send", function(){
                Report.List.init();
                Report.Info.search();
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
                            Report.List.draw();
                        }else{
                            Ui.tip(res.msg, 'danger');
                        }
                    });
                });
            },
            removeReports: function(){
                var ids = U.getCheckedValue('report[]');
                if(ids){
                    Ui.confirm("确定要删除选中的项吗？", function(){
                        util.fetch('report/api/delreport', {
                            data: JSON.stringify({
                                repids: U.getCheckedValue('report[]')
                            })
                        }).done(function(res){
                            if( res.isSuccess ){
                                Ui.tip(res.msg);
                                Report.List.draw();
                            }else{
                                Ui.tip(res.msg, 'danger');
                            }
                        });
                    });
                }else{
                    Ui.tip(Ibos.l("SELECT_AT_LEAST_ONE_ITEM"), "warning");
                }
            }
        });
    };
});