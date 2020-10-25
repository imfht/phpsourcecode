@extends('layouts.app')



@section('content')

<script type="text/javascript">
$(document).ready(function () {

});
</script>

    <div class="container">
    
        <div class=" col-md-12">
        	@include('common.success')
            
			<div class="card" style="margin-top: 10px;">
                <div class="card-header">
                    	订阅微信公众号(稍后恢复订阅)<a href="#weixin"></a>
                    	<div style="float:right">
                    		[<a href="{{ url('feeds/explorer') }}">返回发现</a>]
                    		[<a href="{{ url('articles') }}">返回阅读</a>]
                    	</div>
                </div>
				
				<div class="card-body">
					<form action="/feed" method="post">
						<fieldset disabled>
							<div class="form-row">
								<div  class="col-12">
									<label for="disabledSelect">公众号ID</label>
									<input type="text" value="" class="form-control" name="weixin_id" placeholder="请输入公众号ID"/>
								</div>
								
								<div  class="col-12">
									  <label for="disabledSelect">选择分类</label>
									  <select id="disabledSelect" name="category_id" class="form-control">
										@foreach ($categorys as $category)
										<option value="{{ $category->id }}">{{ $category->name }}</option>
										@endforeach
									  </select>
								</div>
								<input type="hidden" value="weixin" name="feed_type"/>
								<input type="hidden" value="weixin" name="feed_name"/>
								<input type="hidden" value="weixin" name="url"/>
								
								<div class="col">
									<button type="submit" class="btn btn-primary">马上订阅</button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			
    	</div>
	</div>
@endsection
