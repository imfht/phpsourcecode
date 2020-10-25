define([
    app.assetUrl + '/js/report_default_ae.js',
    app.assetUrl + '/js/util.js'
], function(reportAe, util){
    return function(id, done){
        util.queue([
            function(done){
                util.fetch('report/api/formreport&repid='+id).done(function(res){
                    if(res.isSuccess){
                        done( reportAe.tmplCreate(res.data) );
                    }else{
                        Ui.tip(res.msg, 'danger');
                    }
                });
            }
        ], function(tpl){
            appView.html( tpl );
            reportAe.init();
            done();
        });
    };
});