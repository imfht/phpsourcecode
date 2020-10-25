$(document).ready(function(){
	var cssSelector = { 
		jPlayer: "#jplayer_list_N", 
		cssSelectorAncestor: "#paly-list-box"
	};
	var options = { 
    	playlistOptions: {
			autoPlay: true,
			enableRemoveControls:true
		},		
    	swfPath:"js/jPlayer", 
    	supplied: "mp3" ,
		keyEnabled: true,
		ended: function () { 
			if ($('#list-set').hasClass("icn-one")){
				var index = $('li.jp-playlist-current').index();
				myPlaylist.play(index-1);
			}
		},
		loadstart:function (e) {//开始加载时调用函数
					
		},
		pause:function () {
			alert(index);
		},
		play:function () {			
			
		}
	};
	var myPlaylist = new jPlayerPlaylist(cssSelector, Array, options);//实例化jplayerlist
	$.post(JYMUSIC.APP+"/Music/getTopMusic.html", '',function(data){;
			if(data){
				//alert(data);
				for(var i=0; i<data.length; i++){
					if(data[i].singer_name == ''){data[i].singer_name="网络"}	
					var list = {
						title:data[i].name,
						artist:data[i].singer_name,
						id:data[i].id,
						mp3:data[i].music_url,							
					}				
					myPlaylist.add(list);					
				}				
			}
   	}, "json");
   		
   		//播放事件
	  	$(document).on('click', '.ply',  function(){
	  		var that  = $(this);
	  		if (that.hasClass("js-pause") ){//正在暂停	  			
	  			myPlaylist.play();
	  			that.removeClass('js-pause');
	  			that.addClass('pas');
	  		}else if(that.hasClass("pas")){//正在播放	  			
	  			that.removeClass('pas');
	  			that.addClass('js-pause');
	  			myPlaylist.pause();
	  		}else{
	  			//$('.jp-playlist li:first').addClass('jp-playlist-current');
				myPlaylist.play(0);
				that.addClass('pas');
			}
	  	});
	  	
	  	$(".nxt").click(function() {
			myPlaylist.next();
		});
		$(".prv").click(function() {
			myPlaylist.previous();
		});
	$(".icn-vol").click(function() {
			$('.m-vol').toggle();				
	});
	$(document).on('click' ,'.icn-loop',function() {
		$(this).addClass('icn-one');
		$(this).removeClass('icn-loop');
	});
	
	$(document).on('click','.icn-one',function() {
		$(this).addClass('icn-loop');
		$(this).removeClass('icn-one');
	});
	

});