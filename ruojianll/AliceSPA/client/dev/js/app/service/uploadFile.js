/**
 * Created by kunono on 2015/2/5.
 */
app.service('uploadFile',['$upload','common',function($upload,common){
    return{
        upload:upload
    };
    function upload(file,subApi,upload_file_limit_id) {
       return $upload.upload({
            url: common.generateAPI('/upload' + subApi),
            data: {'upload_file_limit_id':upload_file_limit_id},
            file: file
       });
        //    .progress(function (evt) {
        //    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
        //    console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
        //}).success(function (data, status, headers, config) {
        //    console.log(data);
        //});
    }
}]);