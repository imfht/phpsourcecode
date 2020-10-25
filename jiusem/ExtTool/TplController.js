Ext.define('MyApp.controller.{NAME}Controller', {
    extend: 'Ext.app.Controller',
    init: function() {
		this.control({
			//example
            'button[name=cancel]': {
                click:'closeWin'
            }
        });
    },
	//example
	closeWin:function(btn){
		btn.up('window').close();
	}



});