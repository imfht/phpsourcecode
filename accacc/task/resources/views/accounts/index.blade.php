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
            <div class="card">
                <div class="card-header">
                    	账户配置
                </div>

                <div class="card-body">
					@foreach ($oauths as $key=>$oauth)
							
							<div class="col-md-12" >
								<span class = "col-md-3">
									{{$key}}
								</span>
								<span class = "col-md-9">
									
									 <a href="http://task.congcong.us/login/third/{{$key}}">
									@if(empty($oauth))
										去授权
									@else
										重新授权
		                        	@endif
									 </a>
								</span>
							</div>
					@endforeach
                    
                    
                </div>
            </div>
        </div>
    </div>
@endsection
