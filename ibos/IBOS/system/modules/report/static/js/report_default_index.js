define(['text!'+ app.assetUrl +'/templates/default_index.html',
    app.assetUrl + '/js/util.js'
], function(tpl, util){
    var rootScope = app.g('rootScope');
    var UserTmplList = {
        getUserTmpl: function(callback){
            util.fetch('report/api/usertemplate', {
                data: JSON.stringify({'apiType':'web'})
            }).done(function(res){
                if( res.isSuccess ){
                    callback && callback(res.data);
                }else{
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        render: function(callback){
            this.getUserTmpl(function(data){
                appView.html( $.template('tmpl_list_tpl', {data: data, rootScope: rootScope}) );
                callback && callback();
            });
        }
    };
    return function(done){
        appView.html( tpl );

        UserTmplList.render(function(){
            done();
        });
    };
});