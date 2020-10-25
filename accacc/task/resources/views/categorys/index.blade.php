@extends('layouts.app')

@section('content')
<script type="text/javascript">
$(document).ready(function () {

	$(".delete_category").click(function(){
		category_value = $(this).attr("category_value");
		category_token = $(this).attr("category_token");
		category_type = $(this).attr("category_type");

		if (category_type == 'delete' && !confirm("确认要删除此分类咩？")) {
			return false;
		}
		
		$.ajax({
		    url: "{{ url('category') }}"+"/"+category_value,
		    type: 'DELETE',
		    data: {type:category_type,_token:category_token},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert(result_arr.msg);
				} else {
					$('#'+category_value).remove();
				}
		    }
		});
	});
});
</script>
    <div class="container">
    
        <div class=" col-md-12">
        	
        	@include('common.success')
        	
            <div class="card">
                <div class="card-header">
                    	新的分类
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New category Form -->
                    <form action="{{ url('category') }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- category Name -->
                        <div class="form-group row">
                            <label for="category-name" class="col-md-3 control-label">分类名称</label>
								
                            <div class="col-md-8">
	                               <input type="text" name="name" id="name" class="form-control" value="{{ old('category') }}">
                            </div>
                        </div>
                        
                        <!-- Add category Button -->
                        <div class="form-group row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>提交！
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    
                    @if (count($categorys) > 0)
                    <table class="table table-striped category-table">
                            <thead>
                                <th>分类列表</th>
                                <th>&nbsp;</th>
                            </thead>
                            <tbody>
                                @foreach ($categorys as $category)
                                    <tr id="{{$category->id}}">
                                        <td class="table-text"  width="90%">
                                        	<div class="preprepre">
                                        		{{ $category->name }}
                                        	</pre>
                                        </td>

                                        <td  width="1"  align='right'>
	                                        <a href="{{ url('category/'.$category->id)}}" style="color:blue"><img alt=""     style="width: 15px;" src="/img/icon/edit.png"></a>
	                                        	<a href="javascript:void(0)" class="delete_category" category_type="delete" category_value="{{ $category->id }}" category_token="{{ csrf_token() }}"  style="cursor:pointer;">
	                                        		<img alt=""     style="width: 15px;" src="/img/icon/delete.png">
	                                        	</a> 
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                         {!! $categorys->links() !!}
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
