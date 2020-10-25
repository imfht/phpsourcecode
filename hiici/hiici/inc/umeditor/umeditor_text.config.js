

(function () {

	var URL = window.UMEDITOR_HOME_URL || (function(){

		function PathStack() {

			this.documentURL = self.document.URL || self.location.href;

			this.separator = '/';
			this.separatorPattern = /\\|\//g;
			this.currentDir = './';
			this.currentDirPattern = /^[.]\/]/;

			this.path = this.documentURL;
			this.stack = [];

			this.push( this.documentURL );

		}

		PathStack.isParentPath = function( path ){
			return path === '..';
		};

		PathStack.hasProtocol = function( path ){
			return !!PathStack.getProtocol( path );
		};

		PathStack.getProtocol = function( path ){

			var protocol = /^[^:]*:\/*/.exec( path );

						  return protocol ? protocol[0] : null;

						  };

						  PathStack.prototype = {
						  push: function( path ){

						  this.path = path;

						  update.call( this );
						  parse.call( this );

						  return this;

						  },
						  getPath: function(){
						  return this + "";
						  },
						  toString: function(){
						  return this.protocol + ( this.stack.concat( [''] ) ).join( this.separator );
						  }
						  };

						  function update() {

						  var protocol = PathStack.getProtocol( this.path || '' );

						  if( protocol ) {

			//根协议
			this.protocol = protocol;

			//local
			this.localSeparator = /\\|\//.exec( this.path.replace( protocol, '' ) )[0];

			this.stack = [];
			} else {
			protocol = /\\|\//.exec( this.path );
			protocol && (this.localSeparator = protocol[0]);
			}

			}

			function parse(){

			var parsedStack = this.path.replace( this.currentDirPattern, '' );

			if( PathStack.hasProtocol( this.path ) ) {
			parsedStack = parsedStack.replace( this.protocol , '');
			}

			parsedStack = parsedStack.split( this.localSeparator );
			parsedStack.length = parsedStack.length - 1;

			for(var i= 0,tempPath,l=parsedStack.length,root = this.stack;i<l;i++){
			tempPath = parsedStack[i];
			if(tempPath){
			if( PathStack.isParentPath( tempPath ) ) {
			root.pop();
			} else {
			root.push( tempPath );
			}
			}

			}


			}

		var currentPath = document.getElementsByTagName('script');

		currentPath = currentPath[ currentPath.length -1 ].src;

		return new PathStack().push( currentPath ) + "";


	})();

	window.UMEDITOR_CONFIG = {

		UMEDITOR_HOME_URL : URL

			,imageUrl:URL+"php/imageUp.php"             
			,imagePath:URL + "php/"                    
			,imageFieldName:"upfile"                  


			,toolbar:[
			'emotion image video | link unlink',
		'bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |',
		'insertorderedlist insertunorderedlist | selectall cleardoc | fontfamily fontsize' ,
		'| justifyleft justifycenter justifyright justifyjustify |',
		'| horizontal fullscreen', 'drafts', 'formula', 'undo redo'
			]

	};

	if (767 >= $('div.container').width()) {
		window.UMEDITOR_CONFIG.toolbar = ['emotion image video | link unlink', '| fullscreen'];
	}
})();
