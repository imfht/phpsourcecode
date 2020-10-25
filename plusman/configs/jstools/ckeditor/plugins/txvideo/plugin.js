CKEDITOR.plugins.add( 'txvideo', {
    icons: 'txvideo',
    init: function( editor ) {
        // Plugin logic goes here...
        editor.addCommand( 'txvideoDialog', new CKEDITOR.dialogCommand('txvideoDialog' ));

        editor.ui.addButton( 'Txvideo', {
            label: '插入腾讯视频',
            command: 'txvideoDialog',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'txvideoDialog', this.path + 'dialogs/txvideo.js' );
    }
});