<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class GamesLoginController extends Controller{
	public function actionIndex(){
		$serverId=$_GET['serverid'];
		$gamesName=$_GET['gametype'];
		if($serverId && $gamesName && $_GET['gid']){
			/*
			 * 魔神战纪的接口
			 */
			if($gamesName=='mszj'){
				$gamesServerId=  Games::model()->findByAttributes(array('id'=>$_GET['gid']));
				$gamesApi=  GamesApi::model()->findByAttributes(array('gid'=>$_GET['gid']));
				$gamesServerId=unserialize($gamesServerId->server_id);
				$mid=Yii::app()->user->id;
				if($gamesServerId){
					$this->memberGames($mid,$_GET['gid'], $gamesServerId[--$serverId]);
					header("Location:http://res1.mszj.wowan365.com/bin/WebLaucher.html?userid=".$gamesApi->userid."&username=".$gamesApi->username."&time=".time()."&flag=8920bf1232e4f0d50fae4a494a2f8dab&cm=1&server_id=".$gamesServerId[--$serverId]."&country=%E5%8C%97%E4%BA%AC%E5%B8%82");
				}
				
			}
		}
	}

	public function memberGames($mid,$gid,$serverIdValue){

            $modelMemberGames = new MemberGames;
            
            $MemberGames=$modelMemberGames->findByAttributes(array('mid'=>$mid));               
               
            if($MemberGames){                      

                   
                   $MemberGamesArr=array();
                   
                   for($i=1;$i<=6;$i++)
                   {
                        $gidNum='gid'.$i;                       
                        if($MemberGames->$gidNum!==null){
				$MemberGamesArr[$i]= unserialize($MemberGames->$gidNum);
			} else{
				 continue;
			}
                   }
                   
                   
                   foreach($MemberGamesArr as $vo){
                     if($vo['gid']==$gid && $vo['serveridvalue']==$serverIdValue){
			return $vo;          			 
		   }
                                         
                     }
                  if($MemberGames->gnum>=6){
                      
                            $gidTest=serialize(array('gid'=>$gid,'serveridvalue'=>$serverIdValue)) ;
                        
                            $modelMemberGames->updateAll(array('gnum'=>'1','gid1'=>$gidTest),"mid={$mid}");
                            //echo 6;
                            return true;
                    }else{
                            $gnum=$MemberGames->gnum+1;
                            $prefixgId='gid'.$gnum;
                            $gidTest=serialize(array('gid'=>$gid,'serveridvalue'=>$serverIdValue)) ;
                            $modelMemberGames->updateAll(array('gnum'=>$gnum,$prefixgId=>$gidTest),"mid={$mid}");
                            
                            //echo $gnum;
                            return true;
                    }
            }else{
                    $modelMemberGames->mid=$mid;
                    $modelMemberGames->gnum=1;
                    $modelMemberGames->gid1=  serialize(array('gid'=>$gid,'serveridvalue'=>$serverIdValue)) ;
                    $modelMemberGames->save(false);
            }
	}
}
?>
