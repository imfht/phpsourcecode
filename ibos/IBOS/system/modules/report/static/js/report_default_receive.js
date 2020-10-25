var app = Ibos.app;
define([
    app.assetUrl + '/js/util.js',
    app.assetUrl + '/js/report_list.js',
    app.assetUrl + '/js/report_detail.js',
    'userSelect'
], function(util, Report, ReportDetail){
    return function(done){
        var initPage = function(){
            require(['text!'+ app.assetUrl +'/templates/default_send.html'], function(tpl){
                appView.html( tpl );
            });

            Report.Info.init("receive", function(){
                Report.List.init({
                    type: 'receive'
                });
                Report.Info.search();
                done();
            });
        };
        initPage();

        Ibos.events.add({
            back: function(){
                Report.List.draw({
                    type: "receive"
                });
                $('[data-click="unread"]').show();
                $('[data-click="back"]').hide();
                $('[data-click="allread"]').hide();
            },
            unread: function(param, elem){
                Report.List.draw({
                    type: "unread"
                });
                $('[data-click="unread"]').hide();
                $('[data-click="back"]').css({display: 'inline-block'});
                $('[data-click="allread"]').css({display: 'inline-block'});
            },
            allread: function(){
                util.fetch('report/api/allread', {
                    data: JSON.stringify({ repids: 0 })
                }).done(function(res){
                    if( res.isSuccess ){
                        initPage();
                        Ui.tip(res.msg);
                    }else{
                        Ui.tip(res.msg, 'danger');
                    }
                });
            }
        });
    };
});