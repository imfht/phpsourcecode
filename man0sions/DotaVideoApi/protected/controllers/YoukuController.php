<?php
class YoukuController extends Controller {

    /**
     * @param int $page
     * @param string $name
     */
	public function actionGetYoukuList($cid=97,$page=1,$name='大陆'){
		$url = "http://www.youku.com/v_olist/c_".$cid."_g__a_".$name."_sg__mt__lg__q__s_1_r_0_u_0_pt_0_av_0_ag_0_sg__pr__h__d_1_p_$page.html";
//		$url = "http://www.youku.com/v_olist/c_96_g__a__sg__mt__lg__q__s_1_r_0_u_0_pt_0_av_0_ag_0_sg__pr__h__d_1_p_$page.html";
		$html = Main::curl2($url);
		preg_match_all("#p-thumb\">([\s\S]+?)<div class=\"yk-col3\">#",$html,$arr);
		$res = Main::apiCodeInit();
		if(empty($arr[1]))
		{
			$res['status']['errorinfo'] = 'data empty';
			die(CJSON::encode($res));

		}
		$r = array();
		foreach($arr[1] as $key=>$val)
		{
			preg_match("#<img src=\"([^\"]+)\" alt=\"([^\"]+)\">#",$val,$p1);
			preg_match("#<em>([^<]+)</em>([^<]+)</span>[\s\S]+p-status\">([^<]+)</span>[\s\S]+p-meta-title\"><a href=\"[^\"]+id_z([^\"]+)\.html\"#",$val,$p2);
			preg_match("#主演:[\s\S]+href=\"[^\"]+uid_([^\"]+)\.html\" target=\"_blank\">([^<]+)<[\s\S]+播放:</label><span class=\"p-num\">([^<]+)<#",$val,$p3);

			$r[$key]['img'] = $pic = $p1[1];
			$r[$key]['title'] = $name = $p1[2];
			$r[$key]['subtitle'] = @($p2[1].$p2[2]);
			$r[$key]['stripe'] = $last_count = @$p2[3];
			$r[$key]['tid'] =$id = @$p2[4];
			$r[$key]['uid'] =$uid = @$p3[1];
			$r[$key]['actor'] =$actor = @$p3[2];
			$r[$key]['click_count'] = $viecnt = @$p3[3];


		}
		$res = Main::apiCodeInit(1);
		$res['data'] = $r;
		die(CJSON::encode($res));
	}

	/**
	 * 获取搜酷视频列表
	 * @param $data
	 */

	public function actionGetSokuList($data){

		$res = Main::apiCodeInit(0);

		$dcname = md5(__METHOD__.$data);
		$dcache = Yii::app()->cache->get($dcname);
		if($dcache)
		{
			die(CJSON::encode($dcache));
		}
		$json = CJSON::decode($data);
		$json['q'] = urlencode($json['q']);

//				var_dump($json);die();
		$od = @$json['od'] ? $json['od'] : 1; //1-综合拍讯,2-最新发布,3-最多播放
		$q = @$json['q'] ? trim($json['q'])  : 'dota'; //关键词
		$cp = @$json['cp']?$json['cp']:1; //当前页
		$ps = @$json['ps']?$json['ps']:20; //page size
		$lt = @$json['lt']?$json['lt']:0; //1(0-10分钟),2(10-30分钟),3(30-60分钟),4(>60分钟)
		$limitdate = @$json['limitdate']?$json['limitdate']:0;
		$url = "http://www.soku.com/search_video/q_".($q)."_orderby_".$od."_limitdate_".$limitdate."?site=14&_lg=10&lengthtype=$lt&page=$cp";

		$html = file_get_contents($url);
		$cname = $q."_users";
		preg_match_all("#class=\"v-thumb\">([\s\S]+?)<div class=\"v\"#",$html,$hlist);
		$userdata = Yii::app()->cache->get($cname);
		foreach($hlist[1] as $key=>$val)
		{

			$l = array();
			preg_match("#img alt=\"([^\"]+?)\"[^>]+?src=\"([^\"]+?)\"#",$val,$p1);
			preg_match("#v-time\">([^<>]+?)<#",$val,$p2);
			preg_match("#_log_vid=\"([^\"]+?)\"#",$val,$p3);
			preg_match("#username\">([\s\S]+?)</a#",$val,$p4);
			preg_match("#href=\"http://i\.youku\.com/u/([^\"]+)\"#",$val,$p7);
			preg_match("#pub\">([^<>]+?)<#",$val,$p5);
			preg_match("#r\">([^<>]+?)<#",$val,$p6);
			$l['name'] = $p1[1];
			$l['pic'] = $p1[2];
			$l['duration'] = $p2[1];
			$l['vid'] = $p3[1];
			$uname = @trim(strip_tags($p4[1]));
			$l['uname'] = @trim(strip_tags($p4[1]));
			$l['userinfo'] = @$userdata[$uname];
			$l['click_count'] = @$p5[1];
			$l['time'] = @$p6[1];

			$da[] = $l;
		}
		$res = Main::apiCodeInit(1);
		$res['data'] = $da;
		Yii::app()->cache->set($dcname,$res,10*60);

		die(CJSON::encode($res));



	}

	/**
	 * 获取优酷自频道的用户和最新视频
	 * @param $data
	 */
	public function actionGetSokuUser($data){
		$res = Main::apiCodeInit(0);

//		$data = Main::decryKey($data);
//		if(!$data)
//			die(CJSON::encode($res));

		$json = CJSON::decode($data);
		$json['q'] = urlencode($json['q']);
		$q = @$json['q'] ? trim($json['q']) : 'dota'; //关键词
		$cname = $q."_users";


		$da = $this->getUsers($json,$cname);
		$res = Main::apiCodeInit(1);
		$res['data'] = $da;

		die(CJSON::encode($res));

	}

	/**
	 * @param $data
	 */
	public function actionGetSokuAllUser($data){
		$res = Main::apiCodeInit(0);

//		$data = Main::decryKey($data);
//		if(!$data)
//			die(CJSON::encode($res));

		$json = CJSON::decode($data);
		$json['q'] = urlencode($json['q']);

		$q = @$json['q'] ? trim($json['q']) : 'dota'; //关键词
		$cname = $q."_users";

		$pg = @$json['pg'] ? intval($json['pg']) : 100;
		for($i=1;$i<=$pg;$i++)
		{
			$json['cp'] = $i;
			$da  = $this->getUsers($json,$cname);
			$this->saveUsers($cname,$da);


		}

		$res = Main::apiCodeInit(1);
		$res['data'] = Yii::app()->cache->get($cname);

		die(CJSON::encode($res));

	}

	/**
	 * @param $json
	 * @return array
	 */

	private function getUsers($json,$cname){
		$q = @$json['q'] ? trim($json['q']) : 'dota'; //关键词
		$cp = @$json['cp']?$json['cp']:1; //当前页
		$cdata = Yii::app()->cache->get($cname.$cp);
		if($cdata)
		{
			return $cdata;
		}
		$url = "http://www.soku.com/search_user/q_".$q."_orderby_1_limitdate_0?site=14&page=$cp";

		$html = Main::curl2($url,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0');
		preg_match_all("#class=\"user_item\">([\s\S]+?)</ul>\s+</div>\s+</div>#",$html,$hlist);
		foreach($hlist[1] as $key=>$val)
		{
			$l = $vlist = array();
			preg_match("#href=\"([^\"]+?)\"[^\n]+_log_title=\"([^\"]+?)\"><img src=\"([^\"]+?)\"#",$val,$p1);
			preg_match_all("#<li>([\s\S]+?)</li>#",$val,$p2);
			preg_match("#com/u/([^/]+)#",$p1[1],$p3);
			preg_match("#<p class=\"intr\" title=\"([^\"]+)\"#",$val,$p4);
			preg_match("#播放：<span class=\"c_main\">([^<>]+)<#",$val,$p5);
			preg_match("#粉丝：<span class=\"c_main\">([^<>]+)<#",$val,$p6);
			foreach($p2[0] as $k=>$v)
			{
				$vl = array();
				preg_match("#s_target\"><img src=\"([^\"]+?)\"#",$v,$p11);
				preg_match("#s_time\">([^<>]+?)</#",$v,$p12);
				preg_match("#href=\"http://v.youku.com/v_show/id_([^\"]+?)\.html\" target=\"_blank\" title=\"([^\"]+?)\"#",$v,$p13);
				preg_match("#ico__statplay\"></i>([^<>]+?)</[\s\S]+?=\"fr\">([^<>]+?)</#",$v,$p14);

				$vl['pic'] = $p11[1];
				$vl['duration'] = $p12[1];
				$vl['vid'] = $p13[1];
				$vl['name'] = $p13[2];
				$vl['click_count'] = $p14[1];
				$vl['time'] = $p14[2];
				$vlist[] = $vl;

			}


//			$l['uurl'] = $p1[1];
			$l['uname'] = $p1[2];
			$l['upic'] = $p1[3];
			$l['vlist'] = $vlist;
			$l['uid'] = $p3[1];
			$l['intr'] = $p4[1];
			$l['click_count'] = $p5[1];
			$l['fans'] = $p6[1];
			$da[] = $l;
		}
		Yii::app()->cache->set($cname.$cp,$da);
		return $da;
	}

	/**
	 * @param $cname
	 * @param $arr
	 */
	private function saveUsers($cname,$arr)
	{
		$data = Yii::app()->cache->get($cname);
		$newArr = array();
		foreach ($arr as $key => $val)
		{
			unset($val['vlist']);
			$uname = $val['uname'];
			$newArr[$uname] = $val;
		}
		foreach ($data as $key => $val)
		{
			unset($val['vlist']);

			$uname = $val['uname'];

			$newArr[$uname] = $val;
		}

		Yii::app()->cache->set($cname,$newArr);
	}

	/**
	 * 获取该用户所有的视频
	 * @param $data
	 */
	public function actionGetYokuUserPlayList($data){
		$res = Main::apiCodeInit(0);

		$dcname = md5(__METHOD__,$data);

		$dcache = Yii::app()->cache->get($dcname);
		if($dcache)
		{
			die(CJSON::encode($dcache));

		}
		$json = CJSON::decode($data);
		$uid = @$json['uid'] ? trim($json['uid']) : 'UNDI1NTMxMjMy'; //用户id
		$cp = @$json['cp']?$json['cp']:1; //当前页
		$od = @$json['od'] ? $json['od'] : 1; //1-最新发布,2-最多播放,3-最多评论,4-最多收藏

		$url = "http://i.youku.com/u/$uid/videos/fun_ajaxload/?__rt=1&__ro=&page_num=$cp&page_order=$od";
		$html = file_get_contents($url);
//				var_dump($html);die();

		preg_match_all("#class=\"v va\">([\s\S]+?)class=\"v-num\">([^<>]+)</#",$html,$hlist);
		foreach($hlist[1] as $key=>$val)
		{
			$l = array();
			preg_match("#src=\"([^\"]+?)\"[^<>]+alt=\"([^\"]+?)\"#",$val,$p1);
			preg_match("#class=\"v-time\">([^<>]+?)</#",$val,$p2);
			preg_match("#href=\"http://v.youku.com/v_show/id_([^\"]+?)\.html#",$val,$p4);
			preg_match("#v-time\">([^<>]+?)</#",$val,$p5);

			$l['name'] = $p1[2];
			$l['pic'] = $p1[1];
			$l['duration'] = $p2[1];
			$l['vid'] = $p4[1];
			$l['user'] = $uid;
			$l['time'] = trim($p5[1]);
			$l['click_count'] = $hlist[2][$key];
			$da[] = $l;
		}
//		var_dump($da);die();
		$res = Main::apiCodeInit(1);
		$res['data'] = $da;
		Yii::app()->cache->set($dcname,$res,10*60);

		die(CJSON::encode($res));
	}



}