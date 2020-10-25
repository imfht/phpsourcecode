/**
 * 品牌查询接口
 */
$(".brand_id_select_search").bsSuggest({
	searchFields: [ "brand_name"],
	showHeader: true,
	showBtn: true,     //不显示下拉按钮
	getDataMethod: "url",   //获取数据的方式，总是从 URL 获取
	idField: "brand_id",
	keyField: "brand_name",
	clearable: true,
	autoDropup: true,       												//自动判断菜单向上展开
	allowNoKeyword: false,  													//是否允许无关键字时请求数据。为 false 则无输入时不执行过滤请求		
	effectiveFields:["brand_id","brand_name"],
	effectiveFieldsAlias: {brand_id: "序号", brand_name: "品牌名称"},
	url: brand_id_select_search,
	allowNoKeyword: true, 

}).on('onDataRequestSuccess', function (e, result) {
	console.log('onDataRequestSuccess: ', result);
}).on('onSetSelectValue', function (e, keyword, data) {
	//console.log('onSetSelectValue: ',keyword);
	//console.log('onSetSelectValue: ',data);
	$('form input[name="band_id"]').val(data.band_id);
}).on('onUnsetSelectValue', function () {
	//console.log("onUnsetSelectValue");
});