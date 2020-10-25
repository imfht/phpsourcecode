/**
 * 下拉菜单
 *
 * @author  王帆
 * @version  1.0
 */

layui.define(['jquery'], function (exports) {
    var $ = layui.jquery;

    var dropdown = {
        ANCHOR_POSITIONS: ['top-left', 'top-center', 'top-right', 'right-top', 'right-center', 'right-bottom', 'bottom-left', 'bottom-center', 'bottom-right', 'left-top', 'left-center', 'left-bottom'],
        defaults: {
            anchorPosition: 'center'
        },
        dropdown: function (elem, method, data) {
            switch (method) {
                case 'attach':
                    return $(elem).attr('data-dropdown', data);
                case 'detach':
                    return $(elem).removeAttr('data-dropdown');
                case 'show':
                    return $(elem).click();
                case 'hide':
                    dropdown.hideAll();
                    return $(elem);
                case 'enable':
                    return $(elem).removeClass('dropdown-disabled');
                case 'disable':
                    return $(elem).addClass('dropdown-disabled');
            }
        },
        attachAll: function () {
            $('body').off('click.dropdown').on('click.dropdown', '[data-dropdown]', function (event) {
                dropdown.showDropdown(event);
            });
            $('html').off('click.dropdown').on('click.dropdown', function () {
                dropdown.hideAll();
            });
            $(window).off('resize.dropdown').on('resize.dropdown', function () {
                dropdown.hideAll();
            });
        },
        hideAll: function (notElem) {
            var el = '.dropdown-menu';
            var trigger = '[data-dropdown]';
            if (notElem) {
                el += ':not(' + notElem + ')';
                trigger = '[data-dropdown!="' + notElem + '"]';
            }
            $(el).removeClass('dropdown-opened');
            $(trigger).removeClass('dropdown-open');
        },
        showDropdown: function (event) {
            if (event !== void 0) {
                event.preventDefault();
                event.stopPropagation();
            }
            var $trigger = $(event.currentTarget);
            var $dropdown = $($trigger.data('dropdown'));
            if ($dropdown.length < 1) {
                return console.error('Could not find dropdown: ' + $trigger.data('dropdown'));
            }
            var isOpen = $trigger.hasClass('dropdown-open');  // 是否已展开
            var isDisabled = $trigger.hasClass('dropdown-disabled');  // 是否禁用
            // dropdown.hideAll($trigger.data('dropdown'));
            dropdown.hideAll();
            if (isOpen || isDisabled) {
                return false;
            }

            // 第一次添加小三角箭头
            var hasAnchor = $dropdown.hasClass('dropdown-has-anchor');  // 是否需要箭头
            var $anchor = $dropdown.find('.dropdown-anchor');
            if ($anchor.length < 1 && hasAnchor) {
                $anchor = $('<div class="dropdown-anchor"></div>');
                $dropdown.prepend($anchor);
            }

            var widthTrigger = $trigger.outerWidth();
            var heightTrigger = $trigger.outerHeight();
            var widthDropdown = $dropdown.outerWidth();
            var heightDropdown = $dropdown.outerHeight();

            var topTrigger = $trigger.position().top;
            var leftTrigger = $trigger.position().left;
            if ($trigger.hasClass('dropdown-use-offset')) {  // 绝对定位
                topTrigger = $trigger.offset().top;
                leftTrigger = $trigger.offset().left;
            }

            var bottomTrigger = topTrigger + heightTrigger;
            var rightTrigger = leftTrigger + widthTrigger;

            // 获取配置的方向
            var sAnchor = $trigger.data('anchor');
            var anchorPosition = dropdown.defaults.anchorPosition;
            for (var i = 0; i < dropdown.ANCHOR_POSITIONS.length; i++) {
                var position = dropdown.ANCHOR_POSITIONS[i];
                if (position == sAnchor) {
                    anchorPosition = position;
                    $dropdown.addClass('dropdown-anchor-' + position);
                } else {
                    $dropdown.removeClass('dropdown-anchor-' + position);
                }
            }

            var top = 0;
            var left = 0;
            var positionParts = anchorPosition.split('-');
            var anchorSide = positionParts[0];  // 箭头位置
            var anchorPosition = positionParts[1];  // 箭头方向
            if (anchorSide === 'top' || anchorSide === 'bottom') {
                switch (anchorPosition) {
                    case 'left':
                        left = leftTrigger;
                        break;
                    case 'center':
                        left = leftTrigger - widthDropdown / 2 + widthTrigger / 2;
                        break;
                    case 'right':
                        left = rightTrigger - widthDropdown;
                }
            }
            if (anchorSide === 'left' || anchorSide === 'right') {
                switch (anchorPosition) {
                    case 'top':
                        top = topTrigger;
                        break;
                    case 'center':
                        top = topTrigger - heightDropdown / 2 + heightTrigger / 2;
                        break;
                    case 'bottom':
                        top = topTrigger + heightTrigger - heightDropdown;
                }
            }
            switch (anchorSide) {
                case 'top':
                    top = topTrigger + heightTrigger;
                    break;
                case 'right':
                    left = leftTrigger - widthDropdown;
                    break;
                case 'bottom':
                    top = topTrigger - heightDropdown;
                    break;
                case 'left':
                    left = leftTrigger + widthTrigger;
            }
            var addX = parseInt($dropdown.data('add-x'));  // 偏移距离
            var addY = parseInt($dropdown.data('add-y')); // 偏移距离
            if (!isNaN(addX)) {
                left += addX;
            }
            if (!isNaN(addY)) {
                top += addY;
            }
            var addAnchorX = parseInt($trigger.data('add-anchor-x'));  // 箭头便宜位置
            var addAnchorY = parseInt($trigger.data('add-anchor-y'));  // 箭头便宜位置
            if (!isNaN(addAnchorX)) {
                $anchor.css({
                    'marginLeft': addAnchorX
                });
            }
            if (!isNaN(addAnchorY)) {
                $anchor.css({
                    'marginTop': addAnchorY
                });
            }
            $dropdown.css({
                'top': top,
                'left': left
            });
            $trigger.addClass('dropdown-open');
            $dropdown.addClass('dropdown-opened');
        }
    };

    layui.link(layui.cache.base + '/lib/extend/dropdownMenu/dropdown.css');

    dropdown.attachAll();

    exports('dropdown', dropdown);
});
