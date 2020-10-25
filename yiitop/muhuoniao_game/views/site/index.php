<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery.valiLogin.js"></script>
<div id="container" >
  <div id="top">
    <!---------------------------------已登陆------------------------------------>
 
      <!-----------------------------------已登陆结束---------------------------------->

      <?php 
	  if(empty(Yii::app()->user->id)){
		 $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'action'=>Yii::app()->request->baseUrl.'/site/login',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
		'htmlOptions'=>array('enctype'=>'multipart/form-data','onSubmit'=>'return change()'),
	)); 
	
	    echo '  <div id="login">
      
        <table width="270" border="0">
          <tr>
            <td width="38">帐号</td>
            <td width="154">'.$form->textField($modelLogin,'username',array('autocomplete'=>'on')).'</td>
            <td width="64" rowspan="2"><div id="p1" style="width:50px; height:45px;">
                <input type="image" src="'.Yii::app()->request->baseUrl.'/images/login.jpg" value="登陆"  />
              </div></td>
          </tr>
          <tr>
            <td>密码</td>
            <td>'.$form->passwordField($modelLogin,'password').'</td>
          </tr>
          <tr>
            <td>验证码</td>
            <td colspan="2">';
			echo CHtml::activeTextField($modelLogin,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on'));
			$this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));
            echo  '</td>
          </tr>
          <tr style="padding-bottom:10px; padding-top:20px;">
            <td height="45">&nbsp;</td>
            <td>'.CHtml::activeCheckBox($modelLogin,'rememberMe').$form->label($modelLogin,'rememberMe').'
              <a  href="'.Yii::app()->request->baseUrl.'/email/" style="padding-left:30px;">忘记密码</a></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="p2" style="width:121px;height:41px;"><a href="'.Yii::app()->request->baseUrl.'/site/register/"><img src="'.Yii::app()->request->baseUrl.'/images/zhuce.jpg" style="border:none;" /></a></div></td>
            <td>&nbsp;</td>
          </tr>
        </table>
      
    </div>';
	if(Yii::app()->user->hasFlash('actionInfo')){
		echo " <div id='tishi'><h1><span>错误提示</span><a href='javascript:void(0)' onclick=\"$('#tishi').slideToggle();\"></a></h1><p> ".Yii::app()->user->getFlash('actionInfo')."</p></div>";
	}
	

	$this->endWidget();
	 }else{
		$memberMessage=Member::model()->getMemberMessage(Yii::app()->user->id);
		echo '<div id="login_after" style="display:block">
      <div id="login_after_self"> <img src="'.'http://918s-headimg.stor.sinaapp.com'.'/'.$memberMessage['headimg'].'" width="100" height="100" />
        <div class="self">
          <table width="140" border="0">
            <tr>
              <td><a href="'.Yii::app()->request->baseUrl.'/member/" class="name">'.Yii::app()->user->name.'</a></td>
              <td><a href="'.Yii::app()->request->baseUrl.'/member/update/id/'.Yii::app()->user->id.'">[设置]</a></td>
            </tr>
            <tr>
              <td colspan="2"><a href="#">个人积分：200</a></td>
            </tr>
            <tr>
              <td colspan="2"><a href="'.Yii::app()->request->baseUrl.'/site/logout">[退出]</a></td>
            </tr>
          </table>
        </div>
      </div>
      <div id="login_after_game">
        <p>玩过的游戏：</p>
        <ul>';
		$memberGamesarr=MemberGames::model()->getMemberGames(Yii::app()->user->id);
		if(count($memberGamesarr)>4){
			 krsort($memberGamesarr);
		};
		if($memberGamesarr){
			$i=1;
			foreach($memberGamesarr as $value){
				$value=unserialize($value);
				$i++;
				$memberGamesname=Games::model()->getGamesName($value['gid']);
				if($memberGamesname[0]){
					$memberGamesimages=Games::model()->getGamesImage($value['gid']);
					$GetGamesServerId=Games::model()->getGamesServerValue($value['gid'],$value['serveridvalue']);
					echo'<li><a href="'.Yii::app()->request->baseUrl.'/gameslogin/index?gametype='.$memberGamesname[1].'&serverid='.$GetGamesServerId.'"><img src="http://918s-game.stor.sinaapp.com/'.$memberGamesimages[1].'" /></a></li>';
					if($i>4){
						break;
					}
				}
				
			}
		}else{
			echo '<li>暂时没有玩过任何游戏！</li>';
		}
        echo '</ul>
      </div>
    </div>';
	 }
	
	 ?>
       
    <div id="flash">
      <script language="JavaScript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.imagerollover.js"></script>
		<div id="imagePlay">
		<ul>
			<li>
				<a href=""><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/shower_01.jpg" title="图片1" alt="图片1"/></a>
			</li>
			<li>
				<a href=""><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/shower_02.jpg" title="图片2" alt="图片2"/></a>
			</li>
			<li>
				<a href=""><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/shower_03.jpg" title="图片3" alt="图片3"/></a>
			</li>
			<li>
				<a href=""><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/shower_04.jpg" title="图片4" alt="图片4"/></a>
			</li>
		</ul>
	</div>
	<div id="spanPlay">
		<span class="on">
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mini_01.jpg" />
		</span>
		<span>
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mini_02.jpg" />
		</span>
		<span>
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mini_03.jpg" />
		</span>
		<span>
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mini_04.jpg" />
		</span>
	</div>
    </div>
   </div>
  <div id="hot">
    <h1>热门游戏</h1>
    <div id="hot_main">
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/modernizr-1.5.min.js"></script>
      <ul id="garagedoor">
        <li id="shutter1"><a href="official.html">Link 1</a></li>
        <li id="shutter2"><a href="official.html">Link 2</a></li>
        <li id="shutter3"><a href="official.html">Link 3</a></li>
        <li id="shutter4"><a href="official.html">Link 4</a></li>
      </ul>
      <script>
	
		var jQueryScriptOutputted = false;
		
		function initJQuery() {
		    
		    if (typeof(jQuery) == 'undefined') {
		    
		        if (!jQueryScriptOutputted) {
		            jQueryScriptOutputted = true;
		            
		            // Primitive way of loading scripts (no library yet)
		            document.write("<scr" + "ipt src=\"<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js\"></scr" + "ipt>");
		        }
		        setTimeout("initJQuery()", 50);
		        
		    } else {
		    	
		    	// jQuery way of loading scripts
		    	$.getScript('<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.backgroundPosition.js', function() {
		    		
		    		// Just for demo
		    		$("h2").text('This Browser is using a jQuery fallback for this effect.');
		         
		            // Set CSS in Firefox (Required to use the backgroundPosition js)
					$('#shutter1').css({backgroundPosition: '0px 0px'});
					$('#shutter2').css({backgroundPosition: '0px 0px'});
					$('#shutter3').css({backgroundPosition: '0px 0px'});
					$('#shutter4').css({backgroundPosition: '0px 0px'});
		
					// Animate the Shutter  
					$("#garagedoor a").hover(function() {	
					      $(this).parent().stop().animate({backgroundPosition: '(0px -165px)'}, 500);
					    }, function() {
					      $(this).parent().stop().animate({backgroundPosition: '(0px 0px)'}, 500);
					});
					
		    	});
		
		    }
		            
		}
		
		if (!Modernizr.csstransitions) {
			initJQuery();
		}
	</script>
    </div>
  </div>
  <div id="recommend">
    <h1>推荐游戏</h1>
    <div id="recommend_main">
      <ul>
		<?php 
     		$criteria = new CDbCriteria(array(
			'condition'=>'display=1 and flag=2',
			'limit'=>4,
			));	
     		$dataProvider = new CActiveDataProvider('Games',array(
     			'pagination'=>false,
     			'criteria'=>$criteria,
     		));
     		$this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'_recommend',
			'summaryText'=>'',
			)); 
		?>  

		  
      </ul>
    </div>
  </div>
  <div id="bottom" style="float:left;">
  <ul>
	  <?php 
     		$criteria = new CDbCriteria(array(
			'condition'=>'display=1',
			'limit'=>4,
			'order'=>'create_time DESC',
			));	
     		$dataProvider = new CActiveDataProvider('Games',array(
     			'pagination'=>false,
     			'criteria'=>$criteria,
     		));
     		$this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'_rand',
			'summaryText'=>'',
			)); 
		?>  

	  </ul>
  </div>
</div>


