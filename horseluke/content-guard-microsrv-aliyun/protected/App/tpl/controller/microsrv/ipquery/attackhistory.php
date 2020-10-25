<?php 

use SCH60\Kernel\StrHelper;
use Ipip\IP;


$funcAddIpLocation = function(&$attackData){
    foreach($attackData as $k => $row){
        $attackData[$k]['dst_ip_localsrv_location'] = IP::find($row['dst_ip']);
    }
};

$funcCountByIplocation = function($attackData){
    $country_list = array();
    $city_list = array();
    
    foreach($attackData as $k => $row){
        if(!is_array($row['dst_ip_localsrv_location'])){
            continue;
        }
        
        $loc = $row['dst_ip_localsrv_location'];
        
        if(!isset($country_list[$loc[0]])){
            $country_list[$loc[0]] = array('location' => $loc[0], 'count' => 0);
        }
        $country_list[$loc[0]]['count']++;
        
        if($loc[0] == $loc[1]){
            continue;
        }
        
        $city = $loc[2];
        if(empty($loc[2])){
            $city = $loc[1];
        }
        
        if(!isset($city_list[$city])){
            $city_list[$city] = array('location' => $city, 'count' => 0);;
        }
        $city_list[$city]['count']++;
        
    }
    
    if(!empty($country_list)){
        $country_list = array_values($country_list);
    }
    
    return array('country_list' => array_values($country_list), 'city_list' => array_values($city_list));
    
};

?>
<div class="container">

	<div class="row">
		<ol class="breadcrumb">
			<li>系统管理</li>
			<li>内容安全微服务</li>
			<li class="active">IP攻击历史记录查询</li>
		</ol>
	</div>

	<div class="row">
		<div class="col-md-12">
			<form class="form-inline" action="<?=StrHelper::url()?>" method="get">
			    <input type="hidden" name="r" value="microsrv/ipquery/attackhistory" />
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon">IP</div>
						<input type="text" class="form-control" id="inputip" name="ip" placeholder="ip" value="<?=StrHelper::O($ip);?>" />
					</div>
				</div>
				<button type="submit" class="btn btn-primary">查询历史攻击记录</button>
			</form>
		</div>
	</div>

	<div class="row">
	    <div class="col-md-12">&nbsp;</div>
	    <?php if(!$isOk): ?>
		<div class="col-md-12">
		        <?php if(!empty($error)): ?>
			        <div class="alert alert-danger" role="alert">内容安全微服务查询失败。错误详情：<?=StrHelper::O($error['error']. ' ('. $error['errorDetail']. ')');?></div>
			    <?php else: ?>
			        <div class="alert alert-info" role="alert">请输入待查询ip</div>
			    <?php endif; ?>
		</div>
		<div class="col-md-12">&nbsp;</div>
		<?php endif; ?>
	</div>

	
	<?php if($isOk && empty($result['items']['attacks'])): ?>
    <div class="row">
		<div class="alert alert-success" role="alert">本IP没有攻击历史记录</div>
    </div>
    <?php endif; ?>
	
	
	<?php if($isOk && !empty($result['items']['attacks'])): ?>
	
	<?php
	$attackdstGeoStat = array();
	if(!empty($result['items']['attacks'])){
	    $funcAddIpLocation($result['items']['attacks']);
	    $attackdstGeoStat = $funcCountByIplocation($result['items']['attacks']);
	}
	
	$jsSetting = array(
	    'needMap' => true,
	    'mapId' => 'mapx1111',
	    'attackdstGeoStat' => $attackdstGeoStat,
	);
	?>
	
    <div class="row">
        <script>window.actionOpenMapFlag = <?=json_encode($jsSetting)?>;</script>
		<div class="col-md-12" id="mapx1111" style="width: 100%; height:400px;">载入地图中</div>
    </div>
    
    <div class="row">
	    <div class="col-md-12">&nbsp;</div>
	    <div class="col-md-12">
	        <div class="alert alert-info" role="alert" id="tipsBlock" style="display:none;"></div>
	    </div>
	</div>
	
	<div class="row">
	    <div class="col-md-12">
	        <h3>受攻击地区统计</h3>
	    </div>
	    
		<div class="col-xs-12 col-md-6">
		        <table class="table table-hover">
		          <thead>
		            <tr>
		                <th>国家名称</th>
		                <th>计数</th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php foreach($attackdstGeoStat['country_list'] as $row): ?>
		            <tr>
		                <td><?=StrHelper::O($row['location'])?></td>
		                <td><?=StrHelper::O($row['count'])?></td>
		            </tr>
		            <?php endforeach; ?>
		          </tbody>
		        </table>
		</div>
		
		<div class="col-xs-12 col-md-6">
		        <table class="table table-hover">
		          <thead>
		            <tr>
		                <th>国内城市名称</th>
		                <th>计数</th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php foreach($attackdstGeoStat['city_list'] as $row): ?>
		            <tr>
		                <td><?=StrHelper::O($row['location'])?></td>
		                <td><?=StrHelper::O($row['count'])?></td>
		            </tr>
		            <?php endforeach; ?>
		          </tbody>
		        </table>
		</div>
		
	</div>
    
	<div class="row">
	    <div class="col-md-12">
	        <h3>详细清单</h3>
	    </div>
	    
		<div class="col-md-12">
		        <table class="table table-hover">
		          <thead>
		            <tr>
		                <th>攻击地点</th>
		                <th>攻击源ip</th>
		                <th>攻击源端口</th>
		                <th>被攻击ip</th>
		                <th>被攻击ip所在地</th>
		                <th>详情</th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php foreach($result['items']['attacks'] as $row): ?>
		            <tr>
		                <td><?=StrHelper::O($row['country']. ' - '. $row['region'])?></td>
		                <td><?=StrHelper::O($row['ip'])?></td>
		                <td><?=StrHelper::O($row['port'])?></td>
		                <td><?=StrHelper::O($row['dst_ip'])?></td>
		                <td><?=StrHelper::O(is_array($row['dst_ip_localsrv_location']) ? implode('-', $row['dst_ip_localsrv_location']) : $row['dst_ip_localsrv_location'])?></td>
		                <td><?=StrHelper::O($row['detail'])?></td>
		            </tr>
		            <?php endforeach; ?>
		          </tbody>
		        </table>
		</div>
	</div>
	<?php endif; ?>
	
	
	
    <div class="row">
	    <div class="col-md-12">&nbsp;</div>
		<div class="col-md-12">
		    <div class="alert alert-info" role="alert">
		        此处模拟程序接入内容安全微服务后，由微服务代为请求阿里云安全接口（alibaba.security.yundun.xingtu.ipattacks.query / 恶意IP事件接口），并返回数据。
		        <br />可输入114.112.90.54以进行测试。
		    </div>
		</div>
		<div class="col-md-12">&nbsp;</div>
	</div>

</div>

