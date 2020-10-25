@extends('layouts.app')

@section('content')
<script type="text/javascript">
$(document).ready(function () {

	$("#check_url").click(function(){
		
	});
});
</script>
    <div class="container">
    
    	<div class="col-md-12">
    		@include('common.success')
            <div class="card">
                <div class="card-header">
                    	配置说明
                    	<div style="float:right">
                    		<a href="{{'/'}}">[返回]</a>
                    	</div>
                </div>

                <div class="card-body">
                	<div style="float:left">
                		<img alt=""  class="" src="/img/kindle.jpg" width="150px">
                	</div>
                	<div>
                		<p>
						步骤:
						</p>
						
	                	<p>
							IOS手机方案1、复制下方地址使用Safari浏览器打开即可
							
						</p>
						
						<p>
	                		IOS手机方案2、设置-》账户与密码-》添加账户-》其他-》添加已订阅的日历-》复制粘贴下方地址即可
						</p>
						
						<p>
							Android手机方案1、待补充...
						</p>
                	</div>
                </div>
            </div>

        </div>
    
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    	个人日历地址
                </div>

                <div class="card-body">

                    <a href="{{$person_cal_url}}">{{$person_cal_url}}</a>
                    
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    	公共日历地址
                </div>

                <div class="card-body">
					@foreach ($cals as $cal)
							<div class="col-md-12" >
								<span class = "col-md-3">
									{{$cal['theme']}}
								</span>
								<span class = "col-md-9">
									 <a href="{{$cal['url']}}">{{$cal['url']}}</a>
								</span>
							</div>
					@endforeach
                    
                    
                </div>
            </div>
        </div>
    </div>
@endsection
