// JavaScript Document

/*!
 * jQuery Cookie Plugin v1.3
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function ($, document, undefined) {

    var pluses = /\+/g;

    function raw(s) {
        return s;
    }

    function decoded(s) {
        return decodeURIComponent(s.replace(pluses, ' '));
    }

    var config = $.cookie = function (key, value, options) {

        // write
        if (value !== undefined) {
            options = $.extend({}, config.defaults, options);

            if (value === null) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = config.json ? JSON.stringify(value) : String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // read
        var decode = config.raw ? raw : decoded;
        var cookies = document.cookie.split('; ');
        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            if (decode(parts.shift()) === key) {
                var cookie = decode(parts.join('='));
                return config.json ? JSON.parse(cookie) : cookie;
            }
        }

        return null;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== null) {
            $.cookie(key, null, options);
            return true;
        }
        return false;
    };

})(jQuery, document);

/* ---------------------------------------------------------------------- */
/* Style Switcher
 /* ---------------------------------------------------------------------- */
(function ($) {

    $.fn.styleSwitcher = function () {

        var ns = $.fn.styleSwitcher;
        var isMobile = Modernizr.touch;
        var status = 'closed';

        var optionName = {
            layout:'layout',
            color:'color',
            wideBgType:'wideBgType',
            wideBgPattern:'wideBgPattern',
            boxedBgType:'boxedBgType',
            boxedBgColor:'boxedBgColor',
            boxedBgPattern:'boxedBgPattern'
        };

        var defaultOptions = {
            layout:'wide',
            color:'orange',
            wideBgType:'pattern',
            wideBgPattern:'none',
            boxedBgType:'pattern',
            boxedBgColor:'grey-bg',
            boxedBgPattern:'boxed-bg-1'
        };

        function getOption(option) {
            if (isMobile && option === optionName.layout) {
                return 'wide';
            } else {
                var val = $.cookie(option);
                return val ? val : defaultOptions[option];
            }
        }

        function setOption(option, value) {
            $.cookie(option, value);
        }

        var layout;

        var wideOptionsDiv = '<div id="wide-options">' +
            '<h3>Predefined Colors</h3>' +
            '<ul id="wide-predefined-colors" class="colors thumbs' + (isMobile ? ' mobile' : '') + '">' +
            '<li><a id="wide-default-color" href="#" data-color="orange" class="orange active" title="Orange"></a></li>' +
            '<li><a href="#" data-color="green" class="green" title="Green"></a></li>' +
            '<li><a href="#" data-color="blue" class="blue" title="Blue"></a></li>' +
            '<li><a href="#" data-color="light-orange" class="light-orange" title="Light Orange"></a></li>' +
            '<li><a href="#" data-color="red" class="red" title="Red"></a></li>' +
            '<li><a href="#" data-color="purple" class="purple" title="Purple"></a></li>' +
            '<li><a href="#" data-color="pink" class="pink" title="Pink"></a></li>' +
            '<li><a href="#" data-color="teal" class="teal" title="Teal"></a></li>' +
            '</ul>' +
            '<h3>Background Patterns</h3>' +
            '<ul id="wide-bg-patterns" class="bg-patterns thumbs' + (isMobile ? ' mobile' : '') + '">' +
            '<li><a href="#" data-bgp="wide-bg-1" class="wide-bg-1"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-2" class="wide-bg-2"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-3" class="wide-bg-3"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-4" class="wide-bg-4"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-5" class="wide-bg-5"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-6" class="wide-bg-6"></a></li>' +
            '<li><a href="#" data-bgp="wide-bg-7" class="wide-bg-7"></a></li>' +
            '</ul>' +
            '</div>';

        var boxedOptionsDiv = '<div id="boxed-options" style="display: none">' +
            '<h3>Predefined Colors</h3>' +
            '<ul id="boxed-predefined-colors" class="colors thumbs' + (isMobile ? ' mobile' : '') + '">' +
            '<li><a id="boxed-default-color" href="#" data-color="orange" class="orange active" title="Orange"></a></li>' +
            '<li><a href="#" data-color="green" class="green" title="Green"></a></li>' +
            '<li><a href="#" data-color="blue" class="blue" title="Blue"></a></li>' +
            '<li><a href="#" data-color="light-orange" class="light-orange" title="Light Orange"></a></li>' +
            '<li><a href="#" data-color="red" class="red" title="Red"></a></li>' +
            '<li><a href="#" data-color="purple" class="purple" title="Purple"></a></li>' +
            '<li><a href="#" data-color="pink" class="pink" title="Pink"></a></li>' +
            '<li><a href="#" data-color="teal" class="teal" title="Teal"></a></li>' +
            '</ul>' +
            '<h3>Background Patterns</h3>' +
            '<ul id="boxed-bg-patterns" class="bg-patterns thumbs' + (isMobile ? ' mobile' : '') + '">' +
            '<li><a id="boxed-default-bg-pattern" href="#" data-bgp="boxed-bg-1" class="boxed-bg-1"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-2" class="boxed-bg-2"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-3" class="boxed-bg-3"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-4" class="boxed-bg-4"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-5" class="boxed-bg-5"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-6" class="boxed-bg-6"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-7" class="boxed-bg-7"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-8" class="boxed-bg-8"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-9" class="boxed-bg-9"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-10" class="boxed-bg-10"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-11" class="boxed-bg-11"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-12" class="boxed-bg-12"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-13" class="boxed-bg-13"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-14" class="boxed-bg-14"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-15" class="boxed-bg-15"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-16" class="boxed-bg-16"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-17" class="boxed-bg-17"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-18" class="boxed-bg-18"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-19" class="boxed-bg-19"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-20" class="boxed-bg-20"></a></li>' +
            '<li><a href="#" data-bgp="boxed-bg-21" class="boxed-bg-21"></a></li>' +
            '</ul>' +
            '<h3>Background Colors</h3>' +
            '<ul id="boxed-bg-colors" class="bg-colors thumbs' + (isMobile ? ' mobile' : '') + '">' +
            '<li><a id="boxed-default-bg-color" href="#" data-bgc="grey-bg" class="grey-bg active" title="Grey"></a></li>' +
            '<li><a href="#" data-bgc="dark-grey-bg" class="dark-grey-bg" title="Dark Grey"></a></li>' +
            '<li><a href="#" data-bgc="orange-bg" class="orange-bg" title="Orange"></a></li>' +
            '<li><a href="#" data-bgc="green-bg" class="green-bg" title="Green"></a></li>' +
            '<li><a href="#" data-bgc="blue-bg" class="blue-bg" title="Blue"></a></li>' +
            '<li><a href="#" data-bgc="light-orange-bg" class="light-orange-bg" title="Light Orange"></a></li>' +
            '<li><a href="#" data-bgc="red-bg" class="red-bg" title="Red"></a></li>' +
            '<li><a href="#" data-bgc="purple-bg" class="purple-bg" title="Purple"></a></li>' +
            '<li><a href="#" data-bgc="pink-bg" class="pink-bg" title="Pink"></a></li>' +
            '<li><a href="#" data-bgc="teal-bg" class="teal-bg" title="Teal"></a></li>' +
            '</ul>' +
            '</div>';

        var layoutSwitcher = '<div>' +
            '<h3>Layout Styles</h3>' +
            '<div id="layout-switcher">' +
            '<a id="layout-wide" class="button active" href="#">Wide</a>' +
            '<a id="layout-boxed" class="button" href="#">Boxed</a>' +
            '</div>' +
            '</div>';

        function loadStyleSwitcher() {
            $('<div id="style-switcher" style="left: -195px">' +
                '<h2>Style Switcher <a href="#"></a></h2>' +
                '<div id="options">' +
                wideOptionsDiv +
                boxedOptionsDiv +
                (isMobile ? '' : layoutSwitcher) +
                '</div>' +
                '<div id="reset"><a href="#" class="button">Reset</a></div>' +
                '</div>').appendTo('body');
            initListeners();
        }

        function applySettings() {
            var layout = getOption(optionName.layout);
            var bgType = getOption(optionName[layout + 'BgType']);
            applyLayout(layout === 'wide' ? 'boxed' : 'wide', layout);
            applyColor(getOption(optionName.color));
            if (bgType === 'pattern') {
                applyBgPattern(getOption(optionName[layout + 'BgPattern']));
            } else {
                applyBgColor(getOption(optionName[layout + 'BgColor']));
            }
        }

        function initListeners() {

            // Style Switcher
            $('#style-switcher h2 a').click(function (e) {
                e.preventDefault();
                var div = $('#style-switcher');
                if (status === 'closed') {
                    status = 'opening';
                    div.animate({
                        left:'0px'
                    }, 100, function () {
                        status = 'opened';
                    });
                } else if (status === 'opened') {
                    status = 'closing';
                    div.animate({
                        left:'-195px'
                    }, 100, function () {
                        status = 'closed';
                    });
                }
            });

            if (!isMobile) {
                $('#layout-wide').bind('click', function () {
                    setOption(optionName.layout, 'wide');
                    applySettings();
                });

                $('#layout-boxed').bind('click', function () {
                    setOption(optionName.layout, 'boxed');
                    applySettings();
                });
            }

            // Color Switcher
            $(".colors li a").click(function (e) {
                e.preventDefault();
                var color = $(this).data('color');
                applyColor(color);
                setOption(optionName['color'], color);
                return false;

            });

            //Bg Pattern Switcher
            $('.bg-patterns li a').click(function (e) {
                e.preventDefault();
                var bgp = $(this).data('bgp');
                applyBgPattern(bgp);
                setOption(optionName[layout + 'BgPattern'], bgp);
                setOption(optionName[layout + 'BgType'], 'pattern');
                return false;
            });

            //Bg Color Switcher
            $('.bg-colors li a').click(function (e) {
                e.preventDefault();
                var bgc = $(this).data('bgc');
                applyBgColor(bgc);
                setOption(optionName[layout + 'BgColor'], bgc);
                setOption(optionName[layout + 'BgType'], 'color');
                return false;
            });

            $('#reset a').click(function (e) {
                reset();
            });
        }


        function applyColor(color) {
            $('#' + layout + '-predefined-colors > li > a').each(function () {
                var $this = $(this);
                if ($this.hasClass(color)) {
                    if (!$this.hasClass('active')) {
                        $this.addClass('active');
                    }
                } else {
                    $this.removeClass('active');
                }
            });
            $("#color-style").attr("href", "./Include/Css/" + color + ".css");
        }

        function applyBgPattern(bgPattern) {
            if (bgPattern === 'none') {
                $('#' + layout + '-bg-patterns').find('a').removeClass('active');
                $('body').css('backgroundImage', 'none');
            } else {
                clearBgColor();
                $('#' + layout + '-bg-patterns > li > a').each(function () {
                    var $this = $(this);
                    if ($this.hasClass(bgPattern)) {
                        $('body').css('backgroundImage', $this.css('backgroundImage'));
                        if (!$this.hasClass('active')) {
                            $this.addClass('active');
                        }
                    } else {
                        $this.removeClass('active');
                    }
                });
            }
        }

        function applyBgColor(bgColor) {
            clearBgPattern();
            var parent = $('#' + layout + '-bg-colors');
            var el = $('a.' + bgColor);
            parent.find('a').removeClass('active');
            el.addClass('active');
            $('body').css('backgroundColor', el.css('backgroundColor'));
        }

        function applyLayout(oldLayout, newLayout) {
            if (layout === newLayout) {
                return;
            }
            layout = newLayout;
            if (!isMobile) {
                $('#' + oldLayout + '-options').hide();
                $('#' + newLayout + '-options').show();

                var wrap = $('#wrap');
                if(newLayout == 'wide'){
                    wrap.css('width', '');
                    wrap.css('maxWidth', '');
                    wrap.css('margin', '');
                    wrap.css('backgroundColor', '');
                    wrap.css('boxShadow', '');
                }else{
                    wrap.css('width', '1020px');
                    wrap.css('maxWidth', '100%');
                    wrap.css('margin', '0 auto');
                    wrap.css('backgroundColor', '#fff');
                    wrap.css('boxShadow', '0 0 8px rgba(0,0,0,0.24)');
                }
                if (newLayout === 'wide') {
                    clearBgColor();
                    $('#layout-boxed').removeClass('active');
                    $('#layout-wide').addClass('active');

                } else {
                    $('#layout-wide').removeClass('active');
                    $('#layout-boxed').addClass('active');
                }
            }
        }

        function reset() {
            var defaultBgType = defaultOptions[layout + 'BgType'];
            setOption(optionName.wideBgType, defaultOptions.wideBgType);
            setOption(optionName.boxedBgType, defaultOptions.boxedBgType);
            resetColor();
            if (defaultBgType === 'pattern') {
                resetBgColor();
                resetBgPattern();
            } else {
                resetBgPattern();
                resetBgColor();
            }
        }

        function clearBgPattern() {
            $('body').css('backgroundImage', 'none');
            $('#' + layout + '-bg-patterns').find('a').removeClass('active');
        }

        function clearBgColor() {
            $('body').css('backgroundColor', '');
            $('#' + layout + '-bg-colors').find('a').removeClass('active');
        }

        function resetColor() {
            var defaultColor = defaultOptions.color;
            setOption(optionName.color, defaultColor);
            $('#' + layout + '-predefined-colors').find('.active').removeClass('active');
            $('#' + layout + '-default-color').addClass('active');
            $("#color-style").attr("href", "./Include/Css/" + defaultColor + ".css");
        }

        function resetBgPattern() {
            clearBgColor();
            setOption(optionName.wideBgPattern, defaultOptions.wideBgPattern);
            setOption(optionName.boxedBgPattern, defaultOptions.boxedBgPattern);
            applyBgPattern(defaultOptions[layout + 'BgPattern']);
        }

        function resetBgColor() {
            clearBgPattern();
            setOption(optionName.boxedBgColor, defaultOptions.boxedBgColor);
            applyBgColor(defaultOptions.boxedBgColor);
        }

        ns.loadStyleSwitcher = function () {
            loadStyleSwitcher();
            return ns;
        };

        ns.applySettings = function () {
            applySettings();
            return ns;
        }

        return ns;

    };


})(jQuery);