layui.define(['layer', 'form', 'laytpl'], function(exports) {
	"use strict";
	let $ = layui.$, form = layui.form, layarea = {
		_id: 0,
		config: {},
		set: function(options) {
			let that = this;
			that.config = $.extend({}, that.config, options);
			return that;
		},
		on: function(events, callback) {
			return layui.onevent.call(this, 'layarea', events, callback);
		}
	}, thisArea = function() {
		let that = this;
		return {
			layarea: function(files) {
				that.layarea.call(that, files);
			},
			config: that.config
		}
	}, Class = function(options) {
		let that = this;
		that.config = $.extend({}, that.config, layarea.config, options);
		that.render();
	};
	let areaList = {
		list_province: {},
		list_city: {},
		list_county: {}
	};
	Class.prototype.config = {
		elem: '',
		data: {},
		ready: function(result) {},
		change: function(result) {},
	};
	Class.prototype.index = 0;
	Class.prototype.render = function() {
		let that = this, options = that.config;
		options.elem = $(options.elem);
		options.data = {
			province: options.data.province,
			city: options.data.city,
			county: options.data.county,
			provinceCode: 0,
			cityCode: 0,
			countyCode: 0,
		};
		options.bindAction = $(options.bindAction);
		that.events();
	};
	Class.prototype.events = function() {
		let that = this, options = that.config, index;
		let provinceFilter = 'province-' + layarea._id;
		let cityFilter = 'city-' + layarea._id;
		let countyFilter = 'county-' + layarea._id;
		let provinceEl = options.elem.find('.province-selector');
		let cityEl = options.elem.find('.city-selector');
		let countyEl = options.elem.find('.county-selector');
		//search
		provinceEl.attr('lay-search', '');
		cityEl.attr('lay-search', '');
		countyEl.attr('lay-search', '');
		//filter
		if (provinceEl.attr('lay-filter')) {
			provinceFilter = provinceEl.attr('lay-filter');
		}
		if (cityEl.attr('lay-filter')) {
			cityFilter = cityEl.attr('lay-filter');
		}
		if (countyEl.attr('lay-filter')) {
			countyFilter = countyEl.attr('lay-filter');
		}
		provinceEl.attr('lay-filter', provinceFilter);
		cityEl.attr('lay-filter', cityFilter);
		countyEl.attr('lay-filter', countyFilter);
		//获取默认值
		if (provinceEl.data('value')) {
			options.data.province = provinceEl.data('value');
			options.data.provinceCode = getCode('province', options.data.province);
		}
		if (cityEl.data('value')) {
			options.data.city = cityEl.data('value');
			let code = getCode('city', options.data.city, options.data.provinceCode.slice(0, 2));
			options.data.cityCode = code;
		}
		if (countyEl.data('value')) {
			options.data.county = countyEl.data('value');
			options.data.countyCode = getCode('county', options.data.county, options.data.cityCode.slice(0, 4));
		}
		provinceEl.attr('lay-filter', provinceFilter);
		cityEl.attr('lay-filter', cityFilter);
		countyEl.attr('lay-filter', countyFilter);
		//监听结果
		form.on('select(' + provinceFilter + ')', function(data) {
			options.data.province = data.value;
			options.data.provinceCode = getCode('province', data.value);
			renderCity(options.data.provinceCode);
			options.change(options.data);
		});
		form.on('select(' + cityFilter + ')', function(data) {
			options.data.city = data.value;
			if (options.data.provinceCode) {
				options.data.cityCode = getCode('city', data.value, options.data.provinceCode.slice(0, 2));
				renderCounty(options.data.cityCode);
			}
			options.change(options.data);
		});
		form.on('select(' + countyFilter + ')', function(data) {
			options.data.county = data.value;
			if (options.data.cityCode) {
				options.data.countyCode = getCode('county', data.value, options.data.cityCode.slice(0, 4));
			}
			options.change(options.data);
		});
		renderProvince();
		function renderProvince() {
			let tpl = '<option value="">--选择省--</option>';
			let provinceList = getList("province");
			let currentCode = '';
			let currentName = '';
			provinceList.forEach(function(_item) {
				// if (!currentCode) {
				//   currentCode = _item.code;
				//   currentName = _item.name;
				// }
				if (_item.name === options.data.province) {
					currentCode = _item.code;
					currentName = _item.name;
				}
				tpl += '<option value="' + _item.name + '">' + _item.name + '</option>';
			});
			provinceEl.html(tpl);
			provinceEl.val(options.data.province);
			form.render('select');
			renderCity(currentCode);
		}
		function renderCity(provinceCode) {
			let tpl = '<option value="">--选择市--</option>';
			let cityList = getList('city', provinceCode.slice(0, 2));
			let currentCode = '';
			let currentName = '';
			cityList.forEach(function(_item) {
				// if (!currentCode) {
				//   currentCode = _item.code;
				//   currentName = _item.name;
				// }
				if (_item.name === options.data.city) {
					currentCode = _item.code;
					currentName = _item.name;
				}
				tpl += '<option value="' + _item.name + '">' + _item.name + '</option>';
			});
			options.data.city = currentName;
			cityEl.html(tpl);
			cityEl.val(options.data.city);
			form.render('select');
			renderCounty(currentCode);
		}
		function renderCounty(cityCode) {
			let tpl = '<option value="">--选择区--</option>';
			let countyList = getList('county', cityCode.slice(0, 4));
			let currentCode = '';
			let currentName = '';
			countyList.forEach(function(_item) {
				// if (!currentCode) {
				//   currentCode = _item.code;
				//   currentName = _item.name;
				// }
				if (_item.name === options.data.county) {
					currentCode = _item.code;
					currentName = _item.name;
				}
				tpl += '<option value="' + _item.name + '">' + _item.name + '</option>';
			});
			options.data.county = currentName;
			countyEl.html(tpl);
			countyEl.val(options.data.county);
			form.render('select');
		}
		options.ready(options.data);
		function getList(type, code) {
			let result = [];
			if (type !== 'province' && !code) {
				return result;
			}
			let list = areaList["list_" + type] || {};
			result = Object.keys(list).map(function(code) {
				return {
					code: code,
					name: list[code]
				};
			});
			//oversea
			if (code) {
				if (code[0] === '9' && type === 'city') {
					code = '9';
				}
				result = result.filter(function(item) {
					return item.code.indexOf(code) === 0;
				});
			}
			return result;
		}
		function getCode(type, name, parentCode = 0) {
			let code = '';
			let list = areaList["list_" + type] || {};
			let result = {};
			Object.keys(list).map(function(_code) {
				if (parentCode) {
					if (_code.indexOf(parentCode) === 0) {
						result[_code] = list[_code];
					}
				} else {
					result[_code] = list[_code];
				}
			});
			layui.each(result, function(_code, _name) {
				if (_name === name) {
					code = _code;
				}
			});
			return code;
		}
	};
	layarea.render = function(options) {
		let inst = new Class(options);
		layarea._id++;
		return thisArea.call(inst);
	};
	//暴露接口
	exports('layarea', layarea);
});
