/**
 * Created by kunono on 2015/2/5.
 */

app.service('component_uploadFile',['$rootScope','uploadFile','$q','config','log',function($rootScope,uploadFile,$q,config,log){
    $rootScope.component  = $rootScope.component||{};
    $rootScope.component.uploadFile = {files:[],upload:{},returnFiles:[],appType:'IMAGE',acceptType:'image/*',multiple:true};
    var t = $rootScope.component.uploadFile;
    t.init = function(){
        t.files = [];
        t.multiple = true;
    };
    t.onFileSelect = function(files){
        t.files= t.files||[];
        if(!t.multiple){
            t.files = [];
        }
        if(typeof FileReader != undefined){
            for(var i in files){//遍历选择的文件
                var file = files[i];
                var reader = new FileReader();
                //将文件以Data URL形式读入页面
                reader.readAsDataURL(file);
                reader.t_file = file;//将文件交给reader，防止file引用错误
                reader.onload=function(e){
                    var rd = this;//因为$apply,不能用this
                    $rootScope.$apply(function(scope){
                        rd.t_file.localData=rd.result;//设置数据
                        t.files.push(rd.t_file);//添加到文件列表里
                    });
                }
            }
        }
        else{//不支持预览
            t.files = t.multiple?_.union(t.files,files):files;
        }
    };

    t.remove = function(file){
        t.files = _.filter(t.files,function(param){
            return param != file;
        })
    };

    t.upload = function(upload_file_limit_id){
        var appType = t.appType;
        var defer = $q.defer();
        var res = {};
        if(config.appTypes[appType] == null){
            res.success = false;
            res.errors = ['appTypeWrong'];
            defer.reject(res);
        }

        if(t.files != null && t.files.length > 0){
            var count = 0;
            var promises = [];
            for(var file_index in t.files){
                var file = t.files[file_index];
                t.returnFiles = [];
                promises.push(uploadFile.upload(file,config.appTypes[appType].typeApi,upload_file_limit_id).success(function(data){
                    console.log(data);
                    if(data.success){
                        t.returnFiles = _.union(t.returnFiles,data.files);
                    }
                }));
            }
            t.files=null;
            return $q.all(promises).then(function(success){

                    return {'success':true,'files': t.returnFiles};
                },function(err){
                    return {'success':false};
                }
            )

        }
        else{
            res.success = true;
            res.errors = ['no file selected'];
            res.files = [];
            defer.resolve(res);
        }
        return defer.promise;
    };
    return t;
}]);
