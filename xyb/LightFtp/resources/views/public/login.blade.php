@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">LightFtp登录</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="{{ url('public/login') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">FTP地址或域名</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="ip" value="{{ $ip }}"/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">FTP用户名</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="user" value="{{ $user }}" />
							</div>
						</div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">FTP密码</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="pass">
                            </div>
                        </div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">登　录</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
