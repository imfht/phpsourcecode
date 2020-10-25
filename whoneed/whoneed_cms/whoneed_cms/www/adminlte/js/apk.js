function _(el){
    return document.getElementById(el);
}
function uploadImage(file_id,url,file_val,show_file,image_domain = ''){
    var xhr = new XMLHttpRequest();
    //定义表单变量
    var file = document.getElementById(file_id).files[0];
    if(file == '' || file == undefined){
        puppet.mesWarn('请选择上传文件');
        return false;
    }
    //新建一个FormData对象
    var formData = new FormData();
    //追加文件数据
    formData.append("file", file);
    //post方式
    xhr.open('POST', url); //第二步骤
    //发送请求
    xhr.send(formData);  //第三步骤
    //ajax返回
    xhr.onreadystatechange = function(event){ //第四步
        if (xhr.readyState == 4 && xhr.status == 200) {
            var result = event.target.responseText;
            var json = JSON.parse(result);
            if(json.error == true){
                puppet.mesWarn(json.msg);
                return false;
            }else{
                $('#'+file_val).val(json.data);
                $('#'+show_file).attr('src',image_domain+json.data);
            }
        }
    };
}
function uploadFile(filename,url){
    var file = _(filename).files[0];
    if(file == '' || file == undefined){
        puppet.mesWarn('请选择上传文件');
        return false;
    }
    $("#progressBar").css('display','block');
    var formdata = new FormData();
    formdata.append(filename, file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", url);
    ajax.send(formdata);
}
function progressHandler(event){
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent)+'%';
}
function completeHandler(event){
    var result = event.target.responseText;
    var json = eval('('+result+')');
    if(json.error == true){
        _("status").style.color = 'red';
        _("status").innerHTML = json.message;
        _("progressBar").value = 0;
    }else{
        _("status").style.color = 'green';
        _("status").innerHTML = "上传成功";
        _("progressBar").value = 100;
        _("video_url").value = json.data;
    }
}
function errorHandler(event){
    _("status").innerHTML = "Upload Failed";
}

function abortHandler(event){
    _("status").innerHTML = "Upload Aborted";
}