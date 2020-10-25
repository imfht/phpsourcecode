
var BoxStateWait    = 0;
var BoxStateActive  = 1;
var BoxStateStopped = 2;

function lcBoxRefresh()
{
    // console.log(lessSession.Get("boxid"));

    if (lessSession.Get("boxid") == null) {
        alert("No Box Found");
        // lcBoxList();
        return;
    }

    // var url = lessfly_api + "/box/cmd?";
    // url += "access_token="+ lessCookie.Get("access_token");
    // url += "&boxid="+ lessSession.Get("boxid");
    // url += "&action=state";

    var url = lessfly_api + "/box/entry";
    url += "?access_token="+ lessCookie.Get("access_token");
    url += "&boxid="+ lessSession.Get("boxid");
    console.log("box refresh:"+ url);

    $.ajax({
        url     : url,
        type    : "GET",
        timeout : 10000,
        success : function(rsp) {

            var rsj = JSON.parse(rsp);

            // console.log("box refresh rsp:"+ rsp);

            if (rsj.status == 200) {
                
                if (rsj.data.hostaddr.length > 0) { // TODO
                    lessSession.Set("box_hostaddr", rsj.data.hostaddr);
                }

                if (rsj.data.hostid.length > 0) {
                    lessSession.Set("hostid", rsj.data.hostid);
                }

                if (rsj.data.state == BoxStateActive) {
                    $("#nav-box-state-msg").text("Active");
                    lcProject.Open();
                }

            } else {
                $("#nav-box-state-msg").text(rsp.message)
            }
        },
        error   : function(xhr, textStatus, error) {
            $("#nav-box-state-msg").text("Connect Failed")
        }
    });
}


var BoxFs = {
    Get: function(options) {
        // Force options to be an object
        options = options || {};
        
        if (options.path === undefined) {
            // console.log("undefined");
            return;
        }

        if (typeof options.success !== "function") {
            options.success = function(){};
        }
        
        if (typeof options.error !== "function") {
            options.error = function(){};
        }

        var url = "http://"+ lessSession.Get("box_hostaddr") + "/lessfly/v1/fs/get";
        url += "?access_token="+ lessCookie.Get("access_token");
        url += "&path="+ options.path;
        url += "&hostid="+ lessSession.Get("hostid");
        url += "&boxid="+ lessSession.Get("boxid");

        $.ajax({
            url     : url,
            type    : "GET",
            timeout : 10000,
            async   : false,
            success : function(rsp) {
                
                var rsj = JSON.parse(rsp);

                if (rsj === undefined) {
                    options.error(500, "Networking Error"); 
                } else if (rsj.status == 200) {
                    options.success(rsj.data);
                } else {
                    options.error(rsj.status, rsj.message);
                }
            },
            error   : function(xhr, textStatus, error) {
                options.error(textStatus, error);
            }
        });        
    },

    Post: function(options) {

        options = options || {};

        if (typeof options.success !== "function") {
            options.success = function(){};
        }
        
        if (typeof options.error !== "function") {
            options.error = function(){};
        }

        if (options.path === undefined) {
            options.error(400, "path can not be null")
            return;
        }

        if (options.data === undefined) {
            options.error(400, "data can not be null")
            return;
        }

        if (options.encode === undefined) {
            options.encode = "text";
        }

        var req = {
            access_token : lessCookie.Get("access_token"),
            // requestId    : options.requestId,
            data : {
                path     : options.path,
                body     : options.data,
                encode   : options.encode,
                sumcheck : options.sumcheck,
                hostid   : lessSession.Get("hostid"),
                boxid    : lessSession.Get("boxid")
            }
        }

        var url = "http://"+ lessSession.Get("box_hostaddr") + "/lessfly/v1/fs/put";

        $.ajax({
            url     : url,
            type    : "POST",
            timeout : 10000,
            data    : JSON.stringify(req),
            success : function(rsp) {

                var rsj = JSON.parse(rsp);

                if (rsj === undefined) {
                    options.error(500, "Networking Error"); 
                } else if (rsj.status == 200) {
                    options.success(rsj.data);
                } else {
                    options.error(rsj.status, rsj.message);
                }
            },
            error   : function(xhr, textStatus, error) {
                options.error(textStatus, error);
            }
        });
    },

    Rename: function(options) {

        options = options || {};

        if (typeof options.success !== "function") {
            options.success = function(){};
        }
        
        if (typeof options.error !== "function") {
            options.error = function(){};
        }

        if (options.path === undefined) {
            options.error(400, "path can not be null")
            return;
        }

        if (options.pathset === undefined) {
            options.error(400, "file can not be null")
            return;
        }

        var req = {
            access_token : lessCookie.Get("access_token"),
            data : {
                path    : options.path,
                pathset : options.pathset,
                hostid  : lessSession.Get("hostid"),
                boxid   : lessSession.Get("boxid")
            }
        }

        var url = "http://"+ lessSession.Get("box_hostaddr") + "/lessfly/v1/fs/rename";

        $.ajax({
            url     : url,
            type    : "POST",
            timeout : 10000,
            data    : JSON.stringify(req),
            success : function(rsp) {

                var rsj = JSON.parse(rsp);

                if (rsj === undefined) {
                    options.error(500, "Networking Error"); 
                } else if (rsj.status == 200) {
                    options.success(rsj.data);
                } else {
                    options.error(rsj.status, rsj.message);
                }
            },
            error   : function(xhr, textStatus, error) {
                options.error(textStatus, error);
            }
        });
    },

    Del: function(options) {

        options = options || {};

        if (typeof options.success !== "function") {
            options.success = function(){};
        }
        
        if (typeof options.error !== "function") {
            options.error = function(){};
        }

        if (options.path === undefined) {
            options.error(400, "path can not be null")
            return;
        }

        var req = {
            access_token : lessCookie.Get("access_token"),
            data : {
                path    : options.path,
                hostid  : lessSession.Get("hostid"),
                boxid   : lessSession.Get("boxid")
            }
        }

        var url = "http://"+ lessSession.Get("box_hostaddr") + "/lessfly/v1/fs/del";

        $.ajax({
            url     : url,
            type    : "POST",
            timeout : 10000,
            data    : JSON.stringify(req),
            success : function(rsp) {

                var rsj = JSON.parse(rsp);

                if (rsj === undefined) {
                    options.error(500, "Networking Error"); 
                } else if (rsj.status == 200) {
                    options.success(rsj.data);
                } else {
                    options.error(rsj.status, rsj.message);
                }
            },
            error   : function(xhr, textStatus, error) {
                options.error(textStatus, error);
            }
        });
    },

    List: function(options) {
        // Force options to be an object
        options = options || {};
        
        if (options.path === undefined) {
            return;
        }

        if (typeof options.success !== "function") {
            options.success = function(){};
        }
        
        if (typeof options.error !== "function") {
            options.error = function(){};
        }

        var req = {
            access_token : lessCookie.Get("access_token"),
            data : {
                path   : options.path,
                hostid : lessSession.Get("hostid"),
                boxid  : lessSession.Get("boxid")
            }
        }

        var url = "http://"+ lessSession.Get("box_hostaddr") + "/lessfly/v1/fs/list";

        $.ajax({
            url     : url,
            type    : "POST",
            timeout : 10000,
            data    : JSON.stringify(req),
            success : function(rsp) {

                var rsj = JSON.parse(rsp);

                if (rsj === undefined) {
                    options.error(500, "Networking Error"); 
                } else if (rsj.status == 200) {
                    options.success(rsj.data);
                } else {
                    options.error(rsj.status, rsj.message);
                }
            },
            error   : function(xhr, textStatus, error) {
                options.error(textStatus, error);
            }
        });
    }
}
