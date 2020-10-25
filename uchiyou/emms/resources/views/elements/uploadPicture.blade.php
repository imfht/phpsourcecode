{{-- 上传文件 --}}
<div class="modal" id="modal-file-upload">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" action="/picture/upload"
				class="form-horizontal" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h4 class="modal-title">Upload New File</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="file" class="col-sm-3 control-label"> File </label>
						<div class="col-sm-8">
							<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="form-group">
						<label for="file_name" class="col-sm-3 control-label"> Optional
							Filename </label>
						<div class="col-sm-4">
							<input type="text" id="file_name" name="file_name"
								class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Cancel</button>
					<button type="submit" class="btn btn-primary">Upload File</button>
				</div>
			</form>
		</div>
	</div>
</div>
