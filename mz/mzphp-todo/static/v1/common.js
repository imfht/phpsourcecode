/**
 * window.API.post()
 */
window.API = (function () {
    var that = this;

    /**
     * get api url
     * @param api
     * @returns {string}
     */
    function getApiUrl(api) {
        return DIR + 'index.php?c=api-' + api + '&ajax=1&FORM_HASH=' + FORM_HASH;
    }

    /**
     * process response
     * @param response
     */
    function onResponse(response) {
        if (response.status == 200) {
            var data;
            try {
                data = eval('(' + response.body + ')');
            } catch (e) {
            }
            this(data);
        } else {
            this();
        }
    }

    /**
     * post method
     * @param api
     * @param post
     * @param callback
     */
    that.post = function (api, post, callback) {
        var url = getApiUrl(api), form = post;
        if (typeof post == 'object') {
            form = new FormData();
            for (var field in post) {
                form.append(field, post[field]);
            }
        } else if (post.substring(0, 1) == '#') {
            form = new FormData(document.getElementById(post.substring(1)));
        }
        var responseCall = onResponse.bind(callback);
        Vue.http.post(url, form).then(responseCall, responseCall);
    };
    that.get = function (api, callback) {
        var url = getApiUrl(api);
        var responseCall = onResponse.bind(callback);
        Vue.http.get(url).then(responseCall, responseCall);
    };
    return that;
})();
// auto register component
(function (window) {
    for (var index in window) {
        if (index.search(/^vux/ig) > -1) {
            var componentName = index.replace(/^vux/i, '');
            var firstChar = componentName.substring(0, 1);
            componentName = firstChar + componentName.substring(1).replace(/([A-Z])/g, '-$1');
            Vue.component(componentName.toLocaleLowerCase(), window[index]);
            //console.log('LoadVux:' + componentName.toLocaleLowerCase());
        }
    }
})(window);