$(document).ready(function(){

  var myPlaylist = new jPlayerPlaylist({
    jPlayer: "#jplayer_N",
    cssSelectorAncestor: "#jp_container_N"
  }, [

	{
      title:"孙露 - 爱你真的好难",
      artist:"孙露",
      mp3:"http://localhost/Music/爱你真的好难.mp3",
      poster: "images/m0.jpg"
    },
    {
      title:"冷漠 - 皇帝的烦恼",
      artist:"冷漠",
      mp3:"http://localhost/Music/冷漠 - 皇帝的烦恼.mp3",
	  poster: "public/Home/images/m0.jpg"
    },
    {
      title:"孙露 - 拂袖",
      artist:"孙露",
      mp3:"http://localhost/Music/拂袖.mp3",
      poster: "images/m0.jpg"
    },
    {
      title:"孙露 - 女人泪",
      artist:"孙露",
      mp3:"http://localhost/Music/女人泪.mp3",
      poster: "images/m0.jpg"
    },
    {
      title:"冷漠 - 我的发小做了贼",
      artist:"冷漠",
      mp3:"http://localhost/Music/冷漠 - 我的发小做了贼.mp3",
      poster: "images/m0.jpg"
    },
    {
      title:"孙露 - 未了情",
      artist:"孙露",
      mp3:"http://localhost/Music/未了情.mp3",
      poster: "images/m0.jpg"
    },
	{
      title:"孙露 - 这一生说过我爱你",
      artist:"孙露",
      mp3:"http://localhost/Music/这一生说过我爱你.mp3",
      poster: "images/m0.jpg"
    },
    {
      title:"Word Up - Dj Forever Remix",
      artist:"孙露",
      mp3:"http://www.djx6.com/music/music_play/danqu/electrohousedutch/120.mp3",
      poster: "images/m0.jpg"
    }
  ], {
    playlistOptions: {
      enableRemoveControls: true,
      autoPlay: true
    },
    swfPath: "js/jPlayer",
    supplied: "webmv, ogv, m4v, oga, mp3",
    smoothPlayBar: true,
    keyEnabled: true,
    audioFullScreen: false
  });
  
  $(document).on($.jPlayer.event.pause, myPlaylist.cssSelector.jPlayer,  function(){
    $('.musicbar').removeClass('animate');
    $('.jp-play-me').removeClass('p-active');
    $('.jp-play-me').parent('li').removeClass('p-active');
  });

  $(document).on($.jPlayer.event.play, myPlaylist.cssSelector.jPlayer,  function(){
    $('.musicbar').addClass('animate');
  });

  $(document).on('click', '.jp-play-me', function(e){
    e && e.preventDefault();
    var $this = $(e.target);
    if (!$this.is('a')) $this = $this.closest('a');

    $('.jp-play-me').not($this).removeClass('p-active');
    $('.jp-play-me').parent('li').not($this.parent('li')).removeClass('p-active');

    $this.toggleClass('p-active');
    $this.parent('li').toggleClass('p-active');
    if( !$this.hasClass('active') ){
      myPlaylist.pause();
    }else{
      var i = Math.floor(Math.random() * (1 + 7 - 1));
      myPlaylist.play(i);
    }
    
  });



  // video

  $("#jplayer_1").jPlayer({
    ready: function () {
      $(this).jPlayer("setMedia", {
        title: "Big Buck Bunny",
        m4v: "http://flatfull.com/themes/assets/video/big_buck_bunny_trailer.m4v",
        ogv: "http://flatfull.com/themes/assets/video/big_buck_bunny_trailer.ogv",
        webmv: "http://flatfull.com/themes/assets/video/big_buck_bunny_trailer.webm",
        poster: "images/m41.jpg"
      });
    },
    swfPath: "js",
    supplied: "webmv, ogv, m4v",
    size: {
      width: "100%",
      height: "auto",
      cssClass: "jp-video-360p"
    },
    globalVolume: true,
    smoothPlayBar: true,
    keyEnabled: true
  });

var angle = 0;
setInterval(function(){
      angle+=3;
     $("#p-artist img").rotate(angle);
},50);
});