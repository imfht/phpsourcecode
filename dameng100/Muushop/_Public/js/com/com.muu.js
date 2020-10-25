(function($,window,undefined){
    'use strict';
    /* Check jquery */
    if(typeof($) === 'undefined') throw new Error('ZUI requires jQuery');
    // muu shared object
    if(!$.muu) $.muu = function(obj) {
        if($.isPlainObject(obj)) {
            $.extend($.muu, obj);
        }
    };
}(jQuery));