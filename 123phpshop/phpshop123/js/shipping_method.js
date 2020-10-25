function by_quantity(){
  	$(".by_quantity").show();
	$(".by_weight").hide();
}
function by_weight(){
  	$(".by_quantity").hide();
	$(".by_weight").show();
}

 
/**
	选择省份
**/
function select_province(province_name){
	
 //		这里检查是否勾选了省份，如果勾选了省份，那么将这个省份下面的城市全部勾选
	if($(".province[province_name="+province_name+"]").is(':checked')){
 		$(".city[province_name="+province_name+"]").attr("checked",'true');
		$(".district[province_name="+province_name+"]").attr("checked",'true');
 		add_area_selected_row('province_selected',province_name);
		add_area_to_hidden_input('province_selected',province_name);
 		return;
	}
	
  	$(".city[province_name="+province_name+"]").removeAttr("checked");
	$(".district[province_name="+province_name+"]").removeAttr("checked");
			remove_area_to_hidden_input('province_selected',province_name);
  		remove_area_selected_row('province_selected',province_name);
		return;
 }


function select_city(city_name){
	
	if($(".city[city_name="+city_name+"]").is(':checked')){
 		$(".district[city_name="+city_name+"]").attr("checked",'true');
		add_area_selected_row('city_selected',city_name);
		add_area_to_hidden_input('city_selected',city_name);
 		return;
	}
	
  	$(".district[city_name="+city_name+"]").removeAttr("checked");
		remove_area_to_hidden_input('city_selected',city_name);
 		remove_area_selected_row('city_selected',city_name);
		return;

}
 
function add_area_selected_row(row_class_selected,area_name){
 	//	检查是否已经添加了，如果已经添加了，那么退出
	//	如果没有添加，那么添加
	if(row_class_selected=='country_selected'){
		province_selected_value='*';
		city_selected_value='*';
		district_selected_value='*';
 	} 
	
	//	如果没有添加，那么添加
	if(row_class_selected=='province_selected'){
		province_selected_value=area_name;
		city_selected_value='*';
		district_selected_value='*';
 	} 
	
	if(row_class_selected=='city_selected'){
		var city=$(".city[city_name="+area_name+"]");
		province_selected_value=$(city).attr("province_name");
		city_selected_value=area_name;
		district_selected_value='*';
	} 
	
	if(row_class_selected=='district_selected'){
		var district=$(".district[district_name="+area_name+"]");
		province_selected_value=$(district).attr("province_name");
		city_selected_value=$(district).attr("city_name");
		district_selected_value=area_name;
	} 
  	
 	var area_selected_row_string='<tr class="selected_area_row" province_name="'+province_selected_value+'" city_name="'+city_selected_value+'" district_name="'+district_selected_value+'"><td class="province_selected">'+province_selected_value+'</td><td class="city_selected">';			      area_selected_row_string+=city_selected_value+'</td>';     
	  area_selected_row_string+='<td class="district_selected">'+district_selected_value+'</td>';
      area_selected_row_string+='</tr>';
 	  _remove_same_level_areas(row_class_selected,area_name);
      $("#area_selected").append(area_selected_row_string); 
 }

	//	在选择的区域列表中删除所选择的区域
function remove_area_selected_row(row_class_selected,area_name){
	
	
	if(row_class_selected=='country_selected'){
			$(".selected_area_row").remove();
 	} 
	
	//	检查是否已经删除了，如果已经删除了，那么退出
	
	
	//	如果没有删除，那么删除
	if(row_class_selected=='province_selected'){
			para_name='province_name';
 	} 
	
	if(row_class_selected=='city_selected'){
			province_selected_value='';
			para_name='city_name';
			district_selected_value='';
	} 
	
	if(row_class_selected=='district_selected'){
 			para_name='district_name';
	} 
	
	$(".selected_area_row["+para_name+"="+area_name+"]").remove();
 }

function add_area_to_hidden_input(row_class_selected,area_name){
	//	检查是否已经添加了，如果已经添加了，那么退出
	
	//	如果没有添加，那么添加
	
	if(row_class_selected=='country_selected'){
		var area_string="*_*_*;";
		$("input[name=area]").val(area_string);
		return;
 	} 
	
	if(row_class_selected=='province_selected'){
			para_name='province_name';
 	} 
	
	if(row_class_selected=='city_selected'){
			province_selected_value='';
			para_name='city_name';
			district_selected_value='';
	} 
	
	if(row_class_selected=='district_selected'){
 			para_name='district_name';
	} 
	
 	var province_name=	$(".selected_area_row["+para_name+"="+area_name+"]").attr("province_name");
	var city_name=		$(".selected_area_row["+para_name+"="+area_name+"]").attr("city_name");
    var district_name=	$(".selected_area_row["+para_name+"="+area_name+"]").attr("district_name");
   
 	//	这里需要检查添加的等级，如果是省份的话，那么还有这个省的市和县级名称都要删除
	if(row_class_selected=='province_selected'){
		var _filter_string=province_name+"_";
		_filter_area(_filter_string);
 	}
	
	
	//	如果用户选择的是城市的话，那么就需要删除这个城市下面所有的区县
	if(row_class_selected=='city_selected'){
		var _filter_string=province_name+"_"+city_name+"_";
		_filter_area(_filter_string);
 	}
	
		//	如果是城市的话，那么还有这个城市的3级地址名称都要删除
 	var area_string=$("input[name=area]").val()+province_name+"_"+city_name+"_"+district_name+";";
	$("input[name=area]").val(area_string);
	
}

function _filter_area(area_name){
	//		准备参数
		var new_area_string="";
		var areas_array=$("input[name=area]").val().split(";");
 //		将input中所有的位置都以；进行分割
		for(i=0;i<areas_array.length-1;i++){
 	//				如果这些数组中的值不包含这个地址的话，那么直接连接
			if(areas_array[i].indexOf(area_name)<0){
					new_area_string=new_area_string+areas_array[i]+";";
			}
		}
   		$("input[name=area]").val(new_area_string);
		return;
}

function remove_area_to_hidden_input(row_class_selected,area_name){
	//	检查是否已经删除了，如果已经删除了，那么退出
	
	//	如果没有删除，那么删除
	
	if(row_class_selected=='country_selected'){
			$("input[name=area]").val('');
 	}
	
 	if(row_class_selected=='province_selected'){
			para_name='province_name';
 	} 
	
	if(row_class_selected=='city_selected'){
			province_selected_value='';
			para_name='city_name';
			district_selected_value='';
	} 
	
	if(row_class_selected=='district_selected'){
 			para_name='district_name';
	} 
	
 	var province_name=$(".selected_area_row["+para_name+"="+area_name+"]").attr("province_name");
	var city_name=$(".selected_area_row["+para_name+"="+area_name+"]").attr("city_name");
    var district_name=$(".selected_area_row["+para_name+"="+area_name+"]").attr("district_name");
 	var area_string=province_name+"_"+city_name+"_"+district_name+";";
	var input_area_name=$("input[name=area]").val();
 
	// 	直接删除这个地址
 	var newstr=input_area_name.replace(area_string,"");  
 
	 //	这里需要检查添加的等级，如果是省份的话，那么还有这个省的市和县级名称都要删除
	if(row_class_selected=='province_selected'){
		var _filter_string=province_name+"_";
		_filter_area(_filter_string);
		return;
	}
	
	
	//	如果用户选择的是城市的话，那么就需要删除这个城市下面所有的区县
	if(row_class_selected=='city_selected'){
		var _filter_string=province_name+"_"+city_name+"_";
		_filter_area(_filter_string);
		return;
	}
	
	
	$("input[name=area]").val(newstr);
	
}

function _remove_same_level_areas(row_class_selected,area_name){
	
	// 如果是省份的话，那么检查已经选择的地区之中有没有这个省份的，如果的话，那么删除所有的这个省份的城市或区县
	
	if(row_class_selected=="country_selected"){
		$(".selected_area_row").remove();	
	}
	
	if(row_class_selected=="province_selected"){
		$(".selected_area_row[province_name="+area_name+"]").remove();	
	}
	
	// 如果是区县，那么检查同意城市的区县为×的区域，如果有的话，那么删除，另外也需要检查是否有所在的省份，
 	if(row_class_selected=="city_selected"){
		$(".selected_area_row[city_name="+area_name+"]").remove();	
	}
	
}


function select_district(district_name){
 	 if($(".district[district_name="+district_name+"]").is(':checked')){
 		add_area_selected_row('district_selected',district_name);
		add_area_to_hidden_input('district_selected',district_name);
 		return;
	}
	
	remove_area_to_hidden_input('district_selected',district_name);
 	remove_area_selected_row('district_selected',district_name);
	return; 
}

function select_all(){
 	if($("#country").is(':checked')==true){
   		 $("#areas_box input[type=checkbox][id!=country]").attr("checked",'true');
		 	add_area_selected_row('country_selected');
			add_area_to_hidden_input('country_selected');
		    return;
	}
	
	 $("#areas_box input[type=checkbox][id!=country]").removeAttr("checked");
	 remove_area_to_hidden_input('country_selected');
  	 remove_area_selected_row('country_selected');
}


 