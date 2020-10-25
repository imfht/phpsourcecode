/* ============================================================
 * retina-replace.js v1.0
 * http://github.com/leonsmith/retina-replace-js
 * ============================================================
 * Author: Leon Smith
 * Twitter: @nullUK
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */
 (function($) {
    "use strict";
    
    var retinaReplace = function(element, options) {

        this.options = options;
        var $el = $(element);
        var is_image = $el.is('img');
        var normal_url = is_image ? $el.attr('src') : $el.backgroundImageUrl();
        var retina_url = this.options.generateUrl($el, normal_url);

        $('<img/>').attr('src', retina_url).load(function() {

            if (is_image) {
                $el.attr('src', $(this).attr('src'));
            } else {
                $el.backgroundImageUrl($(this).attr('src'));
                $el.backgroundSize($(this)[0].width, $(this)[0].height);
            }

            $el.attr('data-retina', 'complete');
        });
    }

    retinaReplace.prototype = {
        constructor: retinaReplace
    }

    $.fn.retinaReplace = function(option) {
        // Finish if we arn't a retina device
        if (getDevicePixelRatio() <= 1) { return this; }

        return this.each(function() {
            var $this = $(this);
            var data = $this.data('retinaReplace');
            var options = $.extend({}, $.fn.retinaReplace.defaults, $this.data(), typeof option == 'object' && option);
            if (!data) { $this.data('retinaReplace', (data = new retinaReplace(this, options))); }
            if (typeof option == 'string') { data[option](); }
        });
    }
    
    $.fn.retinaReplace.defaults = {
        suffix: '_2x', 
        generateUrl: function(element, url) {
            var dot_index = url.lastIndexOf('.');
            var extension = url.substr(dot_index + 1);
            var file = url.substr(0, dot_index);
            return file + this.suffix + '.' + extension;
        }
    }

    $.fn.retinaReplace.Constructor = retinaReplace;

    // Helper Functions
    var getDevicePixelRatio = function() {
        if (window.devicePixelRatio === undefined) { return 1; }
        return window.devicePixelRatio;
    }

    $.fn.backgroundImageUrl = function(url) {
        return url ? this.each(function () { 
            $(this).css("background-image", 'url("' + url + '")')
        }) : $(this).css("background-image").replace(/url\(|\)|"|'/g, "");
    }

    $.fn.backgroundSize = function(retinaWidth, retinaHeight) {
        var sizeValue = Math.floor(retinaWidth/2) + 'px ' + Math.floor(retinaHeight/2) + 'px';
        $(this).css("background-size", sizeValue);
        $(this).css("-webkit-background-size", sizeValue);
    }

    // Trigger replacement on elements that hav been marked up
    $(function(){
        $("[data-retina='true']").retinaReplace();
    });

})(window.jQuery);