<?php
class getadBlock extends PT_Base{

    public function run($param) {
        if (isset($param['key'])){
            //指定key
            $key=$this->input->param('key','en','',$param);
            $info=$this->model('ad')->field('key')->where(array('key'=>$key))->find();
            if ($info){
                return '<script type="text/javascript" src="'.PT_DIR . '/public/' . $this->config->get('addir') . '/' . $info['key'] . '.js"></script>';
            }else{
                return '没有匹配到对应的广告';
            }
        }elseif (isset($param['width']) && isset($param['height'])){
            //根据尺寸适配
            $width=$this->input->param('width','int',0,$param);
            $height=$this->input->param('height','int',0,$param);
            $order=$this->input->param('order','int',0,$param);
            $list=$this->model('ad')->field('key')->where(array('width'=>$width,'height'=>$height))->order('id asc')->select();
            if ($list){
                if ($order==0){
                    //随机返回
                    return '<script type="text/javascript" src="'.PT_DIR . '/public/' . $this->config->get('addir') . '/' . $list[array_rand($list)]['key'] . '.js"></script>';
                }else{
                    // 指定序号
                    --$order;
                    if (isset($list[$order])){
                        return '<script type="text/javascript" src="'.PT_DIR . '/public/' . $this->config->get('addir') . '/' . $list[$order]['key'] . '.js"></script>';
                    }
                }
            }
            // 找不到匹配的 生成图片
            if ($width && $height){
                $file=$this->createimg($width,$height);
                return "<img src='{$file}' width='{$width}' height='{$height}' />";
            }else{
                return '';
            }

        }else{
            return '区块参数错误';
        }
    }

    protected function parseAd($info) {
        if ($info['type']==1){
            return $info['code'];
        }else{
            return '<script type = "text/javascript">'.$info['code'].'</script>';
        }
    }

    protected function createimg($width,$height){
        $file = PT_ROOT . '/public/' . $this->config->get('addir') . '/' . $width.'_'.$height . '.png';
        if (!is_file($file)){
            F($file,'');
            $img = imagecreatetruecolor($width, $height);
            $color = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $color);
            $white = imagecolorallocate($img, 255, 255, 255);
            $red = imagecolorallocate($img, 180, 180, 180);

            $style = array($red, $red, $red, $red, $red, $white, $white, $white, $white, $white);
            imagesetstyle($img, $style);
            //上
            imageline($img, 5, 5, $width-6, 5, IMG_COLOR_STYLED);
            imageline($img, 5, 6, $width-6, 6, IMG_COLOR_STYLED);
            imageline($img, 5, 7, $width-6, 7, IMG_COLOR_STYLED);
            //下
            imageline($img, 5, $height-5, $width-6, $height-5, IMG_COLOR_STYLED);
            imageline($img, 5, $height-6, $width-6, $height-6, IMG_COLOR_STYLED);
            imageline($img, 5, $height-7, $width-6, $height-7, IMG_COLOR_STYLED);
            //左team
            imageline($img, 5, 5, 5, $height-6, IMG_COLOR_STYLED);
            imageline($img, 6, 5, 6, $height-6, IMG_COLOR_STYLED);
            imageline($img, 7, 5, 7, $height-6, IMG_COLOR_STYLED);
            //右
            imageline($img, $width-5, 5, $width-5, $height-6, IMG_COLOR_STYLED);
            imageline($img, $width-6, 5, $width-6, $height-6, IMG_COLOR_STYLED);
            imageline($img, $width-7, 5, $width-7, $height-6, IMG_COLOR_STYLED);

            imagepng($img,$file);
            imagedestroy($img);
            //打文字
            $image=new image($file);
            $size=min($width/20,$height/4);
            $text='广告尺寸: '.$width.'*'.$height;
            if ($size<12){
                $size=12;
                $text=$width.'*'.$height;
            }
            $image->text($text,PT_ROOT.'/public/font/'.$this->config->get('water_font'),$size,'#999999',image::IMAGE_WATER_CENTER);
            imagepng($image->img,$file);
        }
        return PT_DIR.'/public/' . $this->config->get('addir') . '/' . $width.'_'.$height . '.png';
    }
}