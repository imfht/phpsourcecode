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
                    	订阅微博<a href="#weibo"></a>
                    	<div style="float:right">
                    		[<a href="{{ url('feeds/explorer') }}">返回发现</a>]
                    		[<a href="{{ url('articles') }}">返回阅读</a>]
                    	</div>
                </div>
				
				<div class="card-body">
					<form action="/feed" method="post">
							<div class="form-row">
								{{ csrf_field() }}
								<div  class="col-12">
									<label for="disabledSelect">微博用户ID<br/><small>(进入博主的微博主页，控制台执行 <code>/uid=(\d+)/. exec(document.querySelector('.opt_box .btn_bed').getAttribute('action-data'))[1]</code>)</small></label>
									<input type="text" value="" class="form-control" name="weibo_user_id" placeholder="请输入微博userid"/> 
								</div>
								<div  class="col-12">
									  <label for="disabledSelect">选择分类</label>
									  <select id="disabledSelect" name="category_id" class="form-control">
										@foreach ($categorys as $category)
											<option value="{{ $category->id }}">{{ $category->name }}</option>
										@endforeach
									  </select>
								</div>
								<input type="hidden" value="weibo" name="feed_type"/>
								<input type="hidden" value="weibo" name="feed_name"/>
								<input type="hidden" value="weibo" name="url"/>
								<div class="col">
									<button type="submit" class="btn btn-primary">马上订阅</button>
								</div>
							</div>
					</form>
				</div>
			</div>
			
        </div>
    </div>
@endsection
