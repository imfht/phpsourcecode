<?php
namespace app\common\fun;

//对百度编辑器的一些数据处理
class Ueditor{

    public static function show($content='',$pagetype=''){
        
        $content = Content::bbscode($content);
        
        if ($pagetype!='show') {
            return $content;
        }
        
        if(strstr($content,'<embed type=')){
            $content = preg_replace_callback('/<embed type="application\/x-shockwave-flash" class="edui-faked-video"([^>]+)src="([^"]+)"([^>]+)>/is',array(self,'get_embed_url'),$content);
        }
        
        if(strstr($content,'<video class="edui-upload-video')){
            $content = preg_replace_callback('/<video class="edui-upload-video([^>]+)>([^<]*)<source src="([^"]+)" type="video\/([^"]+)"\/>([^<]*)<\/video>/is',array(self,'get_video_url'),$content);
        }
        
        if(strstr($content,'[iframe_mv]')&&strstr($content,'[/iframe_mv]')){
            $content = preg_replace_callback('/\[iframe_mv\](.*?)\[\/iframe_mv\]/is',array(self,'get_iframe_mv'),$content);
        }
        
        if(strstr($content,'<section data-labelpath="')){
            $content = preg_replace_callback('/<section data-labelpath="([^"]+)" ([^>]+)>(.*?)<\/section>/is',array(self,'get_quote'),$content);
        }
        
        return $content;
    }
    
    /**
     * 解释站内引用主题
     * @param array $array
     * @return string
     */
    private static function get_quote($array=[]){
        list($path,$sysname,$id,$mid) = explode(',',$array[1]);
  
        if ( class_exists("app\\".$sysname."\\index\\Labelmodels") ) {
            $url = urls($sysname.'/labelmodels/show');
        }elseif( class_exists("plugins\\".$sysname."\\index\\Labelmodels") ){
            $url = purl($sysname.'/labelmodels/show');
        }else{
            $url = urls('index/labelmodels/show');
        }        
        
        $url .= "?path={$path}&topic_quote=".mymd5("$sysname,$mid,$id");
        $rand = rands(5);
        return "<div class='topic-quote' id='quote-{$rand}'></div>
<script type='text/javascript'>
$.get('{$url}',function(res){
	if(res.code==0){
		$('#quote-{$rand}').html(res.data.content);
	}
})
</script>";
    }
    
    private static function get_iframe_mv($array=[]){        
        if(IN_WAP===true){
            $width = '100%';
            $height = '250';
        }else{
            $width = 600;
            $height = 400;
        }
        return Player::iframe_player($array[1],$width,$height);
    }
    
    private static function get_video_url($array=[]){
        $url = $array[3];
        preg_match('/width="([\d]+)" height="([\d]+)"/is', $array[1],$ar);
        $width = $ar[1];
        $height = $ar[2];
        return fun('player@play',$url,$width,$height);
    }
    
    private static function get_embed_url($array=[]){
        $url = $array[2];
        preg_match('/width="([\d]+)" height="([\d]+)"/is', $array[3],$ar);
        $width = $ar[1];
        $height = $ar[2];
        return fun('player@play',$url,$width,$height);
    }
     
}