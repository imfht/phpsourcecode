/*
 * Laravel-Bootstrap Alerts v0.0.1, jQuery plugin
 *
 * Copyright(c) 2017, Targaryen
 *
 * A jQuery plugin for displaying Laravel-Bootstrap laravelError .
 * Licensed under the MIT License
 */
/**
 * 自动隐藏
 * @param elem
 * @private
 */
const _hideAuto = (elem) => {
    setTimeout(() => {
        elem.addClass('fade');
        elem.remove();
    }, 3000);
};

const _createDom = (message, settings, parent) => {
    let div = $('<div class="alert alert-' + settings.type + '" role="alert">');

    if (settings.dismissible) {
        $(div).addClass('alert-dismissible');
        let button = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close">');
        let span = $('<span aria-hidden="true">').html('&times;');
        $(span).appendTo(button);
        $(button).appendTo(div);
    }

    if ($.isArray(message)) {
        $.each(message, function (key, value) {
            $(div).append(value);
        });
    } else {
        $(div).append(message);
    }

    if (settings.clear) {
        $(parent).empty();
    }

    $(div).prependTo(parent);

    _hideAuto($(div));
};

const show = function (options) {
    let settings = $.extend({
        type: 'info',
        dismissible: true
    }, options);
    let message = '';

    try {
        message = JSON.parse(options.message);
    } catch (error) {
        message = options.message;
    }

    if (message.length === 0) {
        console.log('bootstrapAlert: message is empty');
        return false;
    }

    //noinspection JSJQueryEfficiency
    if ($('#laravelAlertContainer').length < 1) {
        $('body').append('<div class="t-error" id="laravelAlertContainer"></div>');
    }
    //noinspection JSJQueryEfficiency
    let self = $('#laravelAlertContainer');

    if ($.isArray(message) || $.isPlainObject(message)) {
        $.each(message, (key, value) => {
            _createDom(value, settings, self);
        });
    } else {
        _createDom(message, settings, self);
    }

    self.children('.fade-out').remove();

    return this;
};

module.exports = {
    show
};
