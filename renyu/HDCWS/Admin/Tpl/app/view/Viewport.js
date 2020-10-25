Ext.define('HDCWS.view.Viewport', {

    extend : 'Ext.container.Viewport',

    layout : 'anchor',

    items : [Ext.create('HDCWS.view.Main.DeskTop', {id : 'desktop'}), Ext.create('HDCWS.view.Main.TaskBar')]

});