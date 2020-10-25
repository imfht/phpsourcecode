<?php 
namespace Home\Model;
use Think\Model;
class RecomModel extends Model{
	
	/*
	 * 皮尔逊相关 算法 
	 * 参数1：$prefs：所获取的数据集。
	 * 格式：array(视频id/用户id=>array(用户id/视频id=>评分))
	 * 参数2，参数3：$obj1,$obj2：参与比较的对象
	 */
	protected function pearson($prefs,$obj1,$obj2){
		$si=array();
// 		echo $obj1.$obj2;
		$si = array_intersect_key($prefs[$obj1], $prefs[$obj2]) ;
//  		foreach ($prefs[$obj1] as $item=>$value)
//  			if(array_key_exists($item,$prefs[$obj2]))
//  				$si[$item]=1;
		$n=count($si);
// 		p($si);
		if($n==0) return 0;
		$sum1=0;
		$sum2=0;
		$sum1sq=0;
		$sum2sq=0;
		$psum=0;
		foreach ($si as $i=>$value){
			$sum1+=$prefs[$obj1][$i];
			$sum1sq+=pow($prefs[$obj1][$i],2);
		}
		foreach ($si as $i=>$value){
			$sum2+=$prefs[$obj2][$i];
			$sum2sq+=pow($prefs[$obj2][$i], 2);
		}
		foreach ($si as $i=>$value){
			$psum+=$prefs[$obj1][$i]*$prefs[$obj2][$i];
		}
		
		$num = $psum-($sum1*$sum2/$n);
// 		echo ' '.$num;
		$den = sqrt(($sum1sq-pow($sum1,2)/$n)*($sum2sq-pow($sum2, 2)/$n));
		if($den==0) 
			return 1;
		$r = $num/$den;
		return $r;
	}
	
	/*
	 * 获取与$obj1相似度最高的$n个对象
	 * 参数1：$prefs：所获取的数据集。
	 * 格式：array(视频id/用户id=>array(用户id/视频id=>评分))
	 * 参数2：$obj1：所参与的对象
	 * 参数3：$n：头n条数据
	 */
	public function topMatches($prefs,$obj1,$n=5){
		$score=array();
		foreach ($prefs as $key=>$value){
// 			p($key);
// 			p($obj1);
			if($key!=$obj1)
				$score[$key]=$this->pearson($prefs,$obj1,$key);
// 			p($score);
		}
		asort($score);
		$score=array_reverse($score,True);
		return array_slice($score,0,$n,True);
	}
	
	/*
	 * 数据集翻转：即基于用户评价翻转为基于视频评价
	 * 参数1：$prefs：所获取的数据集。
	 * 格式：array(视频id/用户id=>array(用户id/视频id=>评分))
	 * 返回格式：与上相反：array(用户id/视频id=>array(视频id/用户id=>评分))
	 */
	public function transformPrefs($prefs){
		$result=array();
		foreach ($prefs as $key=>$item){
			foreach ($item as $key2=>$value){
				$result[$key2][$key]=$value;
			}
		}
		return $result;
	}
	
	/*
	 * 基于视频的过滤，构造所有视频相似度前$n的个视频的数据集
	 * 参数1：$prefs：所获取的数据集。
	 * 格式：array(视频id/用户id=>array(用户id/视频id=>评分))
	 * 返回格式：array(视频id=>array(视频id=>相似度))
	 */
	public function calculateSimilarItems($prefs,$n=5){
		$result=array();
// 		echo 'prefs：';
// 		p($prefs);
		foreach($prefs as $key=>$value){
			$result[$key]=$this->topMatches($prefs,$key,$n);
		}
// 		p($result);
		return $result;
	}
	
	/*
	 * 获得用户的视频推荐
	 * 加权平均值：所有（已观看视频评价*未观看视频与其相似度）的和/所有相似度的总和）：
	 * 参数1：$users(基于用户的视频推荐)所获取的数据集>
	 * 格式：array(用户id=>array(视频id=>评分))
	 * 参数2：$itemMatch：物品的比较数据集
	 * 格式：array(视频id=>array(视频id=>相似度))
	 * 参数3：$user：给该用户推荐视频
	 */
	public function getRecommendedItems($users,$itemMatch,$user){
		$userRating = $users[$user];
		$scores=array();
		$totalSim=array();
// 		p($userRating);
// 		p($itemMatch);
		foreach ($userRating as $item=>$rating){
			foreach ($itemMatch[$item] as $item2=>$similarity){
				if(array_key_exists($item2, $userRating)) continue;
				
				$scores[$item2]=isset($score[$item2])?$scores[$item2]:0;
				$scores[$item2]+=$similarity*$rating;
				
				$totalSim[$item2]=isset($totalSim[$item2])?$totalSim[$item2]:0;
				$totalSim[$item2]+=$similarity;
			}
		}
		$rankings=array();
		foreach ($scores as $item=>$score)
			$rankings[$item]=$score/$totalSim[$item];
		asort($rankings);
		$rankings=array_reverse($rankings,true);
		return $rankings;
	}
	
	/*
	 * 默认无参时功能：
	 * 构造数据集，采集用户的偏好。
	 * 返回格式：array(视频id=>array(用户id=>评分))
	 * 当$vid!=0时功能：
	 * 参数$vid：指定视频id。
	 * 返回该视频有关用户评分。
	 * 返回格式：array(用户id=>评分)
	 */
	public function getInformation($vid=0){
		$result=array();
		if($vid==0){
			$item = $this->select();
			foreach ($item as $value){
				$arr = explode(',', $value['per']);
				$video = array();
				foreach ($arr as $item){
					$str=explode(':', $item);
					$key = $str[0];
					$per = $str[1];
					$video[$key]=$per;
				}
				$result[$value['vid']]=$video;
			}
		}
		else {
			$item = $this->field('per')->where(array('vid'=>$vid))->find();
			if($item){
				$arr = explode(',', $item['per']);
				foreach ($arr as $value){
					$uid=explode(':',$value)[0];
					array_push($result, $uid);
				}
			}
		}
		return $result;
	}
	

		
}
?>