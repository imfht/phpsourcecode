<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
	
function TestWrite($d)
{
    $tfile = '_openwms.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp) return false;
    else
    {
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}
?>