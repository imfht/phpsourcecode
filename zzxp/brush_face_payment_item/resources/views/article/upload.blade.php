
<script type="text/javascript" src="{{asset('/js/jquery.js')}}" ></script>
<script type="text/javascript">

    parent.$('input[name="'+action+'"]').val('');
    var src = "{{!isset($path) ? '' : asset($path)}}";
    var url = "{{!isset($url) ? '' : $url}}";
    var file_name = '{{isset($name) ? $name : ''}}';
    var input_name = '{{isset($input_name) ? $input_name : ''}}';
    var action = '{{isset($action) ? $action: ''}}';
    var message = '{{isset($error) ? $error : ''}}';
    if(message == -1){
        parent.show_message.alert('您选择的文件目前不支持上传，目前仅支持.jpg,.png,.gif,.txt,.7z,.rar,.doc,.xls,.ppt等后缀名的文件。');
    }else if(message == -2){
        parent.show_message.alert('单次上传文件大小不能超过10MB。');
    }else if(message == -3){
        parent.show_message.alert('没有文件被上传。');
    }else{

        if(action == 'file'){
            var img = parent.document.createElement('img');
            img.src = src;
            img.width = "200";
            img.height = "200";
            var input = parent.document.createElement('input');
            input.name = input_name;
            input.value = src;
            input.type = 'hidden';
            // var input1 = parent.document.createElement('input');
            // input1.name = 'aid';
            // input1.value = aid;
            // input1.type = 'hidden';
            var show_frame = parent.document.getElementById(input_name);
            show_frame.innerHTML = '';
            show_frame.appendChild(img);
            show_frame.appendChild(input);

            // show_frame.appendChild(input1);
        }else if(action == 'files'){
            var div = parent.document.createElement('div');
            var img = parent.document.createElement('img');
            img.src = src;
            img.width = "200";
            img.height = "200";
            var input = parent.document.createElement('input');
            input.name = input_name+'[]';
            input.value = src;
            input.type = 'hidden';

            var a = parent.document.createElement('a');
            a.innerHTML = '删除';
            a.className = "delNode"; 
            a.onclick = function(){
                this.parentNode.parentNode.removeChild(this.parentNode);
            }
                       // var input1 = parent.document.createElement('input');
            // input1.name = 'aid';
            // input1.value = aid;
            // input1.type = 'hidden';
            var show_frame = parent.document.getElementById('show_frame');
            show_frame.appendChild(div);
            div.appendChild(img);
            div.appendChild(input);
            div.appendChild(a);

            // show_frame.appendChild(input1);
        }else if(action == 'attach'){
            var input = parent.document.createElement('input');
            input.name = 'attsrc[]';
            input.value = src;
            input.type = 'hidden';
            // var input1 = parent.document.createElement('input');
            // input1.name = 'attid[]';
            // input1.value = aid;
            // input1.type = 'hidden';
            var show_frame = parent.document.getElementById('hidden_id');
            show_frame.appendChild(input);
            // show_frame.appendChild(input1);
            if(getFileTxt(src)){
                parent.addAttach(src);
            }else{
                parent.addFile(src,action);
            }
            var img_frame = parent.document.createElement('img');
            img_frame.width = "300";
            img_frame.height= 200;
            img_frame.src = src;
            // var att_frame = parent.document.getElementById('attach_list');
            // att_frame.appendChild(img_frame);
          
        }else{

            var div = parent.document.createElement('div');
            var img = parent.document.createElement('img');
            img.src = src;
            img.width = "200";
            img.height = "200";
            var input = parent.document.createElement('input');
            input.name = action;
            input.value = src;
            input.type = 'hidden';

            var show_frame = parent.document.getElementById(action);
            show_frame.innerHTML = '';
            show_frame.appendChild(div);
            div.appendChild(img);
            div.appendChild(input);
            div.appendChild(a);
        }
        parent.document.myform.action = parent.oldurl;
        parent.document.myform.target = '_self';
    
    }
    //parent.parent.show_wait(0);
    function getFileTxt(src){
        var pos = src.lastIndexOf('.');
        var txt = src.substr(pos + 1);
        if(txt.toLowerCase() == 'jpg' || txt.toLowerCase() == 'gif' || txt.toLowerCase() == 'png' || txt.toLowerCase() == 'jpeg'){
            return true;
        }
        return false;
    }

</script>
</head>
<body>
</body>
</html>