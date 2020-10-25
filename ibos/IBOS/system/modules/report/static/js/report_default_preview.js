define([
    app.assetUrl + '/js/report_default_ae.js',
    app.assetUrl + '/js/util.js'
], function(reportAe, util){

    return function(id, done){
        util.queue([
            function(done){
                util.fetch('report/api/formreport&tid='+id).done(function(res){
                    if(res.isSuccess){
                        done( reportAe.tmplCreate(res.data) );
                    }else{
                        Ui.tip(res.msg, 'danger');
                    }
                });
            }
        ], function(tpl){
            var $node = $(tpl);
            $node.find('input').attr('readonly', true);
            $node.find('select').attr('readonly', true);
            $node.find('textarea').attr('readonly', true);
            $node.find('#type_report').remove();
            $node.find('button').remove();
            appView.html( $node );
            reportAe.init();
            done();
        });
    };
});