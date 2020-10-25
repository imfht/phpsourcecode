<?php
/*
 * 点选图片验证码, 必须开启gd库和session
 * author zhaosheng
 * date 2017-04-20
 */
class VerifyPic {

    private $width = 320; //图片宽度
    private $height = 100; //图片高度
    private $square_w = 20; //文字看成正方形，此为正方形边长
    private $rigth_ratio = 0.10; //点选范围正确率,0-1
    private $word_num = 7; //总的文字个数
    private $word_inv_num = 3; //有效文字个数

    public $font_file = ''; //字体
    public $zh_words = ''; //常用中文字典
    public $bg_dir = ''; //背景库

    const VerPicDir = '/data/web/verifyPic'; //当前库目录配置

    function __construct() {
        $this->font_file = VerifyPic::VerPicDir .'/source/ant4.ttf'; //字体
        $this->zh_words = VerifyPic::VerPicDir .'/source/zh_dictionary.php'; //常用中文字典
        $this->bg_dir = VerifyPic::VerPicDir .'/source/verifypics/'; //背景库

        if (!function_exists("imagecreate")) {
            throw new Exception("gd library unusable");
        }
        if (!session_id()) {
            throw new Exception("no session_id");
        }
    }

    //获取图片验证码
    public function getImage() {
        //随机生成一串中文
        $randstr_arr = $this->getRndWords($this->word_num);

        //字体
        $font_type = $this->font_file;

        $pic_file = $this->bg_dir . rand(1,40) . '.jpg';
        $im = imagecreatefromstring(file_get_contents($pic_file));
        $strposs = array();
        //文字
        for($i=0;$i<count($randstr_arr);$i++){
            if(function_exists("imagettftext")){
                $strposs[$i]['s'] = $randstr_arr[$i];
                $strposs[$i]['x'] = $i*45 + mt_rand(0,30); //x,y为文字左下角坐标
                $strposs[$i]['y'] = mt_rand(25,95);
                $fontColor = ImageColorAllocate($im, mt_rand(0,250),mt_rand(0,250),mt_rand(0,250));
                $strposs[$i]['d'] = mt_rand(-45,45); //旋转角度
                imagettftext($im, $this->square_w, $strposs[$i]['d'], $strposs[$i]['x'], $strposs[$i]['y'], $fontColor, $font_type, $strposs[$i]['s']);
            }
            else{
                throw new Exception("nosupport imagettftext");
            }
        }

        //选择三个位置保存到session
        $keys = array_rand($strposs, $this->word_inv_num);
        foreach ($keys as $k) {
            $sess_data[] = $strposs[$k];
        }
        $_SESSION['VerifyPicLocation'] = json_encode($sess_data);

        header("Pragma:no-cache\r\n");
        header("Cache-Control:no-cache\r\n");
        header("Expires:0\r\n");
        //输出特定类型的图片格式，优先级为 gif -> jpg ->png
        if(function_exists("imagejpeg")){
            header("content-type:image/jpeg\r\n");
            imagejpeg($im);
        }else{
            header("content-type:image/png\r\n");
            imagepng($im);
        }
        usleep(500000); //延迟0.5秒,不然session保存不了
        ImageDestroy($im);
    }

    /*
     * 用户点选位置校验
     * @param $dot array 用户点选三个位置坐标 格式：
     *       array (
     *         0 => array ('x' => 206,'y' => 58),
     *         1 => array ('x' => 206,'y' => 58),
     *         2 => array ('x' => 206,'y' => 58),
     *       )
     * @return boolean true or false
     */
    public function checkPositions($dot) {
        $sess_data = json_decode($_SESSION['VerifyPicLocation'], true);
        $_SESSION['VerifyPicLocation'] = ''; //验证一次就清掉，防止被刷
        if (is_array($dot) && count($dot) == $this->word_inv_num) {
            $flag = true;
            foreach ($dot as $k=>$v) {
                //x坐标为数字，并且在范围内
                if (!(is_numeric($v['x']) && $v['x'] <= $this->width && $v['x'] >= 0)) {
                    $flag = false;
                    break;
                }
                //y坐标为数字，并且在范围内
                if (!(is_numeric($v['y']) && $v['y'] <= $this->height && $v['y'] >= 0)) {
                    $flag = false;
                    break;
                }

                $half_w = $this->square_w/2;
                $client1Area = $this->calcuAreaDot(0, $this->square_w, $v['x'], $v['y']); 
                $server1Area = $this->calcuAreaDot($sess_data[$k]['d'], $this->square_w, $sess_data[$k]['x'], $sess_data[$k]['y']);
                $area = array_intersect($client1Area, $server1Area);

                //count($area) 为重合面积，this->square_w * (2 + $this->square_w) 为总面积
                $ratio = count($area) / ($this->square_w * (2 + $this->square_w)); 
                $_data[] = ($ratio*100). '%';

                //小于正确率
                if ($ratio < $this->rigth_ratio) {
                   $flag = false; 
                   break;
                }

            }
            return $flag;
        }
        else {
            return false;
        }
    }

    /*
     * 获取当前session文字及顺序
     * @return array
     */
    public function getWordsOrder() {
        if (!$_SESSION['VerifyPicLocation']) {
            return -1;
        }
        else {
            $sess_data = json_decode($_SESSION['VerifyPicLocation'],true);
            foreach ($sess_data as $v) {
                $arr[] = $v['s'];
            }
            return $arr;
        }
    }
    /*
     * 计算正方形面积覆盖的坐标整数点
     * @param $d int 旋转角度-90~90
     * @param $b int 正方形边长
     * @param $x int 正方形左下角x轴
     * @param $y int 正方形左下角y轴
     * @return array 整数散点位置集合
     */
    public function calcuAreaDot($d, $b, $x, $y) {
        if (!$b || !$x || !$y) return array();
        $d = $d == 0 ? 1 : $d; //简化算法，角度为0约等于角度为1
        $s = deg2rad(abs($d)); //转为弧度
        //遍历y轴
        $dots = array(); //散点集合
        for ($i=0; $i<=$b; $i++) {
            //遍历x轴
            for ($j=0; $j<=$b; $j++) {
                if ($d > 0) {
                    //计算y轴坐标
                    $tan = tan($s);
                    $a = $tan * $i;
                    $y1 = hypot($i, $a);
                    $c = $j - $a;
                    $y2 = sin($s) * $c;
                    $_y = $y + $y1 +$y2;
                    //计算x轴
                    $x1 = $y2 / $tan;
                    $_x = $x + $x1;
                }
                else {
                    //计算x轴
                    $a = tan($s) * $j;
                    $x1 = hypot($j, $a);
                    $c = $i - $a;
                    $x2 = sin($s) * $c;
                    $_x = $x + $x1 + $x2;
                    $y1 = $x2 / tan($s);
                    $_y = $y + $y1;
                }
                $dots[] = round($_x) .'-'. round($_y); //取整
            }
        }
        return $dots;
    }

    /*
     * 获取随机中文字串
     * @param int $len 长度
     * @return array
     */
    private function getRndWords($len) {
        include_once($this->zh_words);
        $arr = array_rand($zh_dictionary, $len);
        if ($len == 1) {
            $data[] = $zh_dictionary[$arr];
            return $data;
        }
        foreach ($arr as $v) {
            $data[] = $zh_dictionary[$v];
        }
        return $data;
    }

}
