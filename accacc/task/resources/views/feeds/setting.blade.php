@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>

    <div class="container">
    
        <div class=" col-md-12">
        	@include('common.success')
            <div class="card">
                <div class="card-header">
                    	分类展示
                    	<div style="float:right">
                    		<a href="{{'/feeds'}}">[添加订阅]</a>
                    		<a href="{{'/articles'}}">[返回]</a>
                    	</div>
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')
                    
                    @if (count($nav_infos) > 0)
                    	<div id="multi">
                    		@foreach ($nav_infos as $nav_info)
                                <div id="{{ $nav_info['category_info']['category_id'] }}" class="tile category_id_info">
                    					<legend class="tile__name">{{ $nav_info['category_info']['category_name'] }}</legend>
                    					
                                		@foreach($nav_info['list'] as $feed)
	                                			<div class="tile__list col-md-12"> 
		                                				<div class="feed_sub_id_info" ori_category_id="{{ $nav_info['category_info']['category_id'] }}" id="{{ $feed['feed_sub_id'] }}">
			                                				<span class="col-md-6">
				                                				 {{ $feed['feed_name'] }}
			                                				</span>
			                                				<span class="col-md-5 text-right">
				                                				<a href="{{ url('feed/'.$feed['feed_sub_id'])}}" style="color:blue"><img alt=""     style="width: 15px;" src="/img/icon/edit.png"></span>
					                                        	<a href="javascript:void(0)" class="delete_feed" task_type="delete" feed_value="{{ $feed['feed_sub_id'] }}" feed_token="{{ csrf_token() }}"  style="cursor:pointer;">
					                                        		<img alt="" style="width: 15px;" src="/img/icon/delete.png">
						                        				</a> 
			                                				</span>
		                                				</div>
			                            		</div>
                                		@endforeach
  								</div>
                        	@endforeach
                    	</div>
                    @endif
                    
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
		var multi =  document.getElementById('multi');
		Sortable.create(multi, {
			  animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
			  handle: ".tile__name", // Restricts sort start click/touch to the specified element
			  draggable: ".tile", // Specifies which items inside the element should be sortable
			  onEnd: function (evt){
				     var item = evt.item; // the current dragged HTMLElement

				     //说明已经调整了分类的顺序 进行更新
				     if(evt.oldIndex != evt.newIndex){
				    	 var valArr = new Array;
				    	 $(".category_id_info").each(function(i){
				    		valArr[i] = $(this).attr('id');
				    	 });
				    	 var vals = valArr.join(',');//转换为逗号隔开的字符串

				    	 $.ajax({
				 		    url: "{{ url('categorys/sort') }}",
				 		    type: 'POST',
				 		    data: {"category_ids":vals, "_token":"{{ csrf_token() }}"},
				 		    success: function(result) {
				 		    	result_arr = JSON.parse(result);
				 				if(result_arr.code != 9999){
				 					alert('处理失败，请稍后再试');
				 				}
				 		    }
				 		});
				     }
			  }
		});
	
		[].forEach.call(multi.getElementsByClassName('tile__list'), function (el){
			Sortable.create(el, {
				group: 'photo',
				animation: 150, // Specifies which items inside the element should be sortable
				onEnd: function (evt){
					     var item = evt.item; // the current dragged HTMLElement
					     //说明已经更换了分类

					     var ori_category_id = $('#'+item.id).attr('ori_category_id');
					     var now_category_id = $('#'+item.id).parent().parent().attr('id');


					     var change_feed_sub_id = "";
					     var change_feed_sub_category = "";
					     //更换了分类说明已经将此分类进行重新排序
					     if(ori_category_id != now_category_id){
					    	 $('#'+item.id).attr('ori_category_id', now_category_id);
					    	 change_feed_sub_id = item.id;
					    	 change_feed_sub_category = now_category_id;
					     } else if(evt.oldIndex == evt.newIndex){
// 							return '';
					     }

					     var valArr = new Array;
				    	 $("#"+now_category_id+" .feed_sub_id_info").each(function(i){
				    		valArr[i] = $(this).attr('id');
				    	 });
				    	 var vals = valArr.join(',');//转换为逗号隔开的字符串

				    	 if(vals == '') return '';

				    	 $.ajax({
				 		    url: "{{ url('feeds') }}"+"/sort",
				 		    type: 'POST',
				 		    data: {"feed_sub_ids":vals, "_token":"{{ csrf_token() }}" , "change_feed_sub_id":change_feed_sub_id, "change_feed_sub_category":change_feed_sub_category},
				 		    success: function(result) {
				 		    	result_arr = JSON.parse(result);
				 				if(result_arr.code != 9999){
				 					alert('处理失败，请稍后再试');
				 				} else {
// 				 					$('#'+feed_value).remove();
				 				}
				 		    }
				 		});
					}
				});
			});

	</script>
@endsection
