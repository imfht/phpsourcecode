

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

	/**
	 * 配置项主体。注意，此处所有涉及到路径的配置别遗漏URL变量。
	 */
	window.UMEDITOR_CONFIG = {

		//为编辑器实例添加一个路径，这个不能被注释
		UMEDITOR_HOME_URL : URL

			//图片上传配置区
			,imageUrl:URL+"php/imageUp.php"             //图片上传提交地址
			,imagePath:URL + "php/"                     //图片修正地址，引用了fixedImagePath,如有特殊需求，可自行配置
			,imageFieldName:"upfile"                   //图片数据的key,若此处修改，需要在后台对应文件修改对应参数


			//工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的从新定义
			,toolbar:[
			'emotion image video',
		'undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |',
		'link unlink | insertorderedlist insertunorderedlist | selectall cleardoc | fontsize' 
			]

			,autoClearinitialContent:true //是否自动清除编辑器初始内容，注意：如果focus属性设置为true,这个也为真，那么编辑器一上来就会触发导致初始化的内容看不到了

	};

	if (767 >= $('div.container').width()) {
		window.UMEDITOR_CONFIG.toolbar = ['emotion image video | link unlink', '| fullscreen'];
	}
})();
