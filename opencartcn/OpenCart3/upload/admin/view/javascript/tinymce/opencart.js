
tinymce.init({
	selector: '.tinymce',
	language:editor_language, //en or zh_CN
	menubar: false,
	convert_urls: false,
	relative_urls: false,
	inline: false,
	content_css: false,
	height: 500,
	plugins: [
		'textcolor colorpicker media link lists table hr image imagetools code',
	],
	toolbar1: "undo redo | bold italic underline strikethrough superscript subscript removeformat | forecolor backcolor | mybutton media link | bullist numlist",
	toolbar2: "formatselect fontselect fontsizeselect | outdent indent alignleft aligncenter alignright alignjustify | table | hr | code",
	setup: function(editor){
		editor.addButton('mybutton', {
	      	title: 'Select Image',
	      	icon:'image' ,
	      	onclick: function () {
	        	CKFinder.modal( {
					chooseFiles: true,
					width: 800,
					height: 600,
					onInit: function( finder ) {
						finder.on( 'files:choose', function( evt ) {
							var files = evt.data.files.toArray();
							for(var i = 0; i < files.length; i++){
								editor.insertContent("<img src='" + files[i].getUrl() + "'/>");
							}
						});
						finder.on( 'file:choose:resizedImage', function( evt ) {
							var files = evt.data;
							editor.insertContent("<img src='" + files['resizedUrl'] + "'/>");
						});
					}
				});
	      	}
	    });
	}
});