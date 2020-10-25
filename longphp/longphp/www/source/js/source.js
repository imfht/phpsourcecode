var ywl = {
    load_js_css : function(){
        var load_js = window.document.getElementById('load_js_css');
        var load_js_msg = load_js.getAttribute('str').split(',');
        var load_js_obj = load_js_url = {};
        if(load_js_msg){
            for(var i = 0; i < load_js_msg.length; i++){
                if(i == 0){
                    load_js_url = load_js_obj = load_js_msg[i].replace('http://', '');
                    load_js_url = load_js_url.split('/');
                }else {
                    load_js_obj = load_js_msg[i];
                }
                load_js_obj = load_js_obj.split('/');
                var load_js_css_type = load_js_obj[load_js_obj.length - 1].split('.');
                load_js_css_type = load_js_css_type[load_js_css_type.length - 1];
                var load_js_str = 'http://';
                var last_num = 0;
                for(var iii = 0; iii < load_js_obj.length; iii++){
                    if(load_js_obj[iii] == '..'){
                        last_num++;
                    }
                }
                for(var ii = 0; ii < load_js_url.length - 1; ii++){
                    load_js_str += load_js_url[ii] + '/';
                    if(load_js_url.length - 1 - last_num - ii == 1){
                        break;
                    }
                }
                
                if(i == 0){
                    load_js_str += load_js_obj[load_js_obj.length - 1];
                }else {
                    for(var iii = 0; iii < load_js_obj.length; iii++){
                        if(load_js_obj[iii] != '..'){
                            load_js_str += load_js_obj[iii] + '/';
                        }
                    }
                    load_js_str = load_js_str.substr(0, load_js_str.length - 1);
                }
                
                
                var create_obj = '';
                if(load_js_css_type == 'js'){
                    create_obj = document.createElement('script');
                    create_obj.type = 'text/javascript';
                    create_obj.src = load_js_str;
                }else if(load_js_css_type == 'css'){
                    create_obj = document.createElement('link');
                    create_obj.type = 'text/css';
                    create_obj.rel = 'stylesheet';
                    create_obj.href = load_js_str;
                }
                document.body.appendChild(create_obj);
            }
        }
    }
};