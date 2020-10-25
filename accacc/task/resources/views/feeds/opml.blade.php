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
                    	OPML导入<a href="#opml"></a>
                    	<div style="float:right">
                    		[<a href="{{ url('feeds/explorer') }}">返回发现</a>]
                    		[<a href="{{ url('articles') }}">返回阅读</a>]
                    	</div>
                </div>
				
				<div class="card-body">
					<form action="/feeds/importOpml" method="post" enctype="multipart/form-data">
							<div class="form-row">
								{{ csrf_field() }}
								<div class="col-8">
								  <input type="file" class="" name="opml_file" id="customFile">
								</div>
								
								<div class="col">
									<button type="submit" class="btn btn-primary">立即导入</button>
								</div>
							</div>
					</form>
				</div>
			</div>
			
        </div>
    </div>
@endsection
