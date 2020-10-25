const ___WOOKTEAM_CONTENT_OBJECT = {
    username: 0,
    interval: function () {
        try {
            var config = JSON.parse(window.localStorage.getItem("__::WookTeam:config"));
            if (typeof config.username === "string") {
                if (config.username !== ___WOOKTEAM_CONTENT_OBJECT.username) {
                    ___WOOKTEAM_CONTENT_OBJECT.username = config.username;
                    chrome.runtime.sendMessage({
                        act: 'config',
                        config: config
                    }, function (response) {
                        //console.log(response);
                    });
                }
            }
        } catch (e) {

        }
    }
};

if (window.localStorage.getItem("__::WookTeam:check") === "success") {
    ___WOOKTEAM_CONTENT_OBJECT.interval();
    setInterval(___WOOKTEAM_CONTENT_OBJECT.interval, 6000);
}




