function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

function isIE() {
 if (!!window.ActiveXObject || "ActiveXObject" in window) return true;
}

$(document).ready(function() {
	//Form Submit for IE Browser
    if (isIE()) {
        $('button[type=\'submit\']').on('click', function() {
            $("form[id*='form-']").submit();
        });
    }

	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

    $('form > .nav-tabs li a').each(function () {
      var identity = $(this).attr('href');
      if (identity.length === 1 || identity.substring(0, 1) !== '#') {
        return;
      }
      if ($(identity).find('.text-danger').length) {
         $(this).addClass('has-error');
         $(this).prepend('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;');
       }
    });

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});

	// https://github.com/opencart/opencart/issues/2595
	$.event.special.remove = {
		remove: function(o) {
			if (o.handler) {
				o.handler.apply(this, arguments);
			}
		}
	}

	// tooltip remove
	$('[data-toggle=\'tooltip\']').on('remove', function() {
		$(this).tooltip('destroy');
	});

	// Tooltip remove fixed
	$(document).on('click', '[data-toggle=\'tooltip\']', function(e) {
		$('body > .tooltip').remove();
	});

    if ($(window).width() >= 768) {
        if (localStorage.getItem('menu_active') === 'false') {
    		$('#column-left').removeClass('active');
        } else {
            $('#column-left').addClass('active');
        }
    }

    $('#button-menu').on('click', function(e) {
        e.preventDefault();

        $('#column-left').toggleClass('active');
		if ($(window).width() >= 768) {
            localStorage.setItem('menu_active', $('#column-left').hasClass('active'));
        }
    });

    // Set last page opened on the menu
    $('#menu a[href]').on('click', function() {
        sessionStorage.setItem('menu', $(this).attr('href'));
	});

	if (!sessionStorage.getItem('menu')) {
		$('#menu #dashboard').addClass('active');
	} else {
		// Sets active and open to selected page in the left column menu.
		$('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parent().addClass('active');
	}

	$('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li > a').removeClass('collapsed');

	$('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('ul').addClass('in');

	$('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li').addClass('active');

	// Image Manager
	$(document).on('click', 'a[data-toggle=\'image\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover'); // element has bs popover?
        var $button = '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
        if($element.hasClass('product-img')){
            $button += ' <button type="button" onMouseOver="$(this).tooltip(\'show\')" id="button-main" data-toggle="tooltip" data-placement="top" data-original-title="' + text_main_image + '" class="btn btn-success"><i class="fa fa-laptop"></i></button>';
        }
		e.preventDefault();

		// destroy all image popovers
		$('a[data-toggle="image"]').popover('destroy');

		// remove flickering (do not re-add popover when clicking for removal)
		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return $button;
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
            var act = $element.attr('btn-act');
            CKFinder.modal( {
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function( finder ) {
                    finder.on( 'files:choose', function( evt ) {
                        var files = evt.data.files.toArray();
                        var files_array = new Array()
                        for(var i = 0; i < files.length; i++){
                            files_array[i] = files[i].getUrl();
                        }
                        if(files_array.length > 0){
                            $.ajax({
                                url: 'index.php?route=common/filemanager/ckfinder&user_token=' + getURLVar('user_token') + '&restore=1&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
                                data : 'files=' + files_array,
                                type : 'post',
                                dataType : 'json',
                                success : function(json){
                                    if(json['code'] == 1){
                                        if(act == 'main_img'){
                                            var main_img = json['result'][0];
                                            var new_main_src = main_img['thumb'];
                                            var new_main = '<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail" btn-act="' + act + '">';
                                            new_main += '<img src="' + new_main_src + '" alt="" title="" data-placeholder="' + $element.attr('data-placeholder') + '" />';
                                            new_main += '<input type="hidden" name="image" value="' + main_img['image'] + '" id="input-image" />';
                                            new_main += '</a>';
                                            $('#thumb-image').parent('td').html(new_main);
                                            if(json['result'].length > 1){
                                                var image_row = $('#images').find('tbody tr').length;
                                                for(var i = 1; i < json['result'].length; i++){
                                                    html  = '<tr id="image-row' + image_row + '">';
                                                    html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail product-img"><img src="' + json['result'][i]['thumb'] + '" alt="" title="" data-placeholder="' + placeholder + '" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="' + json['result'][i]['image'] + '" id="input-image' + image_row + '" /></td>';
                                                    html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="' + text_sort + '" class="form-control" /></td>';
                                                    html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="' + text_delete + '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                                                    html += '</tr>';

                                                    $('#images tbody').append(html);
                                                    image_row++;
                                                }
                                            }
                                        }else{
                                            var image_row = $('#images').find('tbody tr').length;
                                            var sub_img = json['result'][0];
                                            var new_sub_src = sub_img['thumb'];
                                            $element.find('img').attr('src', new_sub_src);
                                            $element.next('input').val(sub_img['image']);
                                            for(var i = 1; i < json['result'].length; i++){
                                                html  = '<tr id="image-row' + image_row + '">';
                                                html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail product-img"><img src="' + json['result'][i]['thumb'] + '" alt="" title="" data-placeholder="' + placeholder + '" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="' + json['result'][i]['image'] + '" id="input-image' + image_row + '" /></td>';
                                                html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="' + text_sort + '" class="form-control" /></td>';
                                                html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="' + text_delete + '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                                                html += '</tr>';

                                                $('#images tbody').append(html);
                                                image_row++;
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                    finder.on( 'file:choose:resizedImage', function( evt ) {
                        var file = evt.data;
                        console.log(file['resizedUrl']);
                        $.ajax({
                            url: 'index.php?route=common/filemanager/ckfinder&token=' + getURLVar('token') + '&restore=1&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
                            data : 'files=' + file['resizedUrl'],
                            type : 'post',
                            dataType : 'json',
                            success : function(json){
                                if(json['code'] == 1){
                                    if(act == 'main_img'){
                                        var main_img = json['result'][0];
                                        var new_main_src = main_img['thumb'];
                                        var new_main = '<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail" btn-act="' + act + '">';
                                        new_main += '<img src="' + new_main_src + '" alt="" title="" data-placeholder="' + $element.attr('data-placeholder') + '" />';
                                        new_main += '<input type="hidden" name="image" value="' + main_img['image'] + '" id="input-image" />';
                                        new_main += '</a>';
                                        $('#thumb-image').parent('td').html(new_main);
                                        if(json['result'].length > 1){
                                            var image_row = $('#images').find('tbody tr').length;
                                            for(var i = 1; i < json['result'].length; i++){
                                                html  = '<tr id="image-row' + image_row + '">';
                                                html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="' + json['result'][i]['thumb'] + '" alt="" title="" data-placeholder="' + placeholder + '" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="' + json['result'][i]['image'] + '" id="input-image' + image_row + '" /></td>';
                                                html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="排序" class="form-control" /></td>';
                                                html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                                                html += '</tr>';

                                                $('#images tbody').append(html);
                                                image_row++;
                                            }
                                        }
                                    }else{
                                        var image_row = $('#images').find('tbody tr').length;
                                        var sub_img = json['result'][0];
                                        var new_sub_src = sub_img['thumb'];
                                        $element.find('img').attr('src', new_sub_src);
                                        $element.next('input').val(sub_img['image']);
                                        for(var i = 1; i < json['result'].length; i++){
                                            html  = '<tr id="image-row' + image_row + '">';
                                            html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="' + json['result'][i]['thumb'] + '" alt="" title="" data-placeholder="' + placeholder + '" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="' + json['result'][i]['image'] + '" id="input-image' + image_row + '" /></td>';
                                            html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="排序" class="form-control" /></td>';
                                            html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                                            html += '</tr>';

                                            $('#images tbody').append(html);
                                            image_row++;
                                        }
                                    }
                                }
                            }
                        });
                    });
                }
            });
            $element.popover('destroy');
		});

        $('#button-main').on('click', function(){
            if($element.attr('id') != 'thumb-image'){
                var placeholder = $('#thumb-image').find('img').attr('data-placeholder');
                var new_current = '<td class="text-left">';
                new_current += '<a href="" id="' + $element.attr('id') + '" data-toggle="image" class="img-thumbnail product-img" btn-act="' + $element.attr('btn-act') + '">';
                new_current += '<img src="' + $('#thumb-image').find('img').attr('src') + '" alt="" title="" data-placeholder="' + placeholder + '" />';
                new_current += '</a>';
                var h_value = $('#thumb-image').find('img').attr('src').split('cache/');
                new_current += '<input type="hidden" name="' + $element.parent('td').find('input[type="hidden"]').attr('name') + '" value="' + h_value[1].replace('-100x100', '') + '" id="' + $element.parent('td').find('input[type="hidden"]').attr('id') + '" />';
                new_current += '</td>';
                new_current += '<td class="text-right">';
                //new_current += '<input type="text" name="' + $element.parent('td').parent('tr').find('.form-control').attr('name') + '" value="' + $element.parent('td').parent('tr').find('.form-control').attr('value') + '" placeholder="' + $element.parent('td').parent('tr').find('.form-control').attr('placeholder') + '" class="form-control" />';
                new_current += $element.parent('td').parent('tr').find('td').eq(1).html();
                new_current += '</td>';
                new_current += '<td class="text-left">';
                new_current += $element.parent('td').parent('tr').find('td:last-child').html();
                new_current += '</td>';
                $element.parent('td').parent('tr').html(new_current);
                $element.popover('destroy');

                var new_main_src = $element.find('img').attr('src');
                var new_main = '<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail" btn-act="' + $element.attr('btn-act') + '">';
                new_main += '<img src="' + new_main_src + '" alt="" title="" data-placeholder="' + placeholder + '" />';
                new_main += '<input type="hidden" name="image" value="' + $element.parent('td').find('input[type="hidden"]').val() + '" id="input-image" />';
                new_main += '</a>';
                $('#thumb-image').parent('td').html(new_main);
            }
        });

		$('#button-clear').on('click', function() {
			$element.find('img').attr('src', $element.find('img').attr('data-placeholder'));

			$element.parent().find('input').val('');

			$element.popover('destroy');
		});
	});

	// table dropdown responsive fix
	$('.table-responsive').on('shown.bs.dropdown', function(e) {
		var t = $(this),
			m = $(e.target).find('.dropdown-menu'),
			tb = t.offset().top + t.height(),
			mb = m.offset().top + m.outerHeight(true),
			d = 20;
		if (t[0].scrollWidth > t.innerWidth()) {
			if (mb + d > tb) {
				t.css('padding-bottom', ((mb + d) - tb));
			}
		} else {
			t.css('overflow', 'visible');
		}
	}).on('hidden.bs.dropdown', function() {
		$(this).css({'padding-bottom': '', 'overflow': ''});
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			var $this = $(this);
			var $dropdown = $('<ul class="dropdown-menu" />');

			this.timer = null;
			this.items = [];

			$.extend(this, option);

			$this.attr('autocomplete', 'off');

			// Focus
			$this.on('focus', function() {
				this.request();
			});

			// Blur
			$this.on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$this.on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				var value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $this.position();

				$dropdown.css({
					top: pos.top + $this.outerHeight(),
					left: pos.left
				});

				$dropdown.show();
			}

			// Hide
			this.hide = function() {
				$dropdown.hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				var html = '';
				var category = {};
				var name;
				var i = 0, j = 0;

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						// update element items
						this.items[json[i]['value']] = json[i];

						if (!json[i]['category']) {
							// ungrouped items
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						} else {
							// grouped items
							name = json[i]['category'];
							if (!category[name]) {
								category[name] = [];
							}

							category[name].push(json[i]);
						}
					}

					for (name in category) {
						html += '<li class="dropdown-header">' + name + '</li>';

						for (j = 0; j < category[name].length; j++) {
							html += '<li data-value="' + category[name][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[name][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$dropdown.html(html);
			}

			$dropdown.on('click', '> li > a', $.proxy(this.click, this));
			$this.after($dropdown);
		});
	}
})(window.jQuery);
