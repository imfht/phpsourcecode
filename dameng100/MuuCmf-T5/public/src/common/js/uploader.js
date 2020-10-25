;(function (window, $) {
    'use strict';
    //全局定义webuploader对象，引入webuploader
    var WebUploader = null;
 
    /**
     * 建立一个对象，用于实例化的作用，添加一些属性
     */
    function webUploadFunction() {
        this.opction = null;
        this.patchOpction = null;
        this.swf_path = "";//swf的地址
        this.uploader = null;
        WebUploader = window.WebUploader;
    }
 
    /**
     * 初始化全局环境，以及webuploader里面的事件方法。
     * 调用直接使用MUU.init({});进行调用初始化
     * @param opction
     */
    webUploadFunction.prototype.init = function (opction) {
        //判断是不是引用了webuploader的js
        if (!WebUploader) {
            throw new Error('请引入对应的webuploader.js文件，具体下载地址在webuploader官网');
        }
 
        //判断是不是支持浏览器
        if (!WebUploader.Uploader.support()) {
            throw new Error('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        }
        this.opction = opction;
        this.swf_path = this.opction["swf_path"];
        this.patchOpction = this.opction["patchOpction"];
 
        //路径的必传选项
        if (!this.swf_path) {
            throw new Error('webuploader 组件的swf路径为空，请设置路径');
        }
 
        // HOOK 这个必须要再uploader实例化前面，详情见 http://fex.baidu.com/webuploader/document.html#hook
        /*
            add-file            files: File对象或者File数组   用来向队列中添加文件。
            before-send-file    file: File对象    在文件发送之前request，此时还没有分片（如果配置了分片的话），可以用来做文件整体md5验证。
            before-send         block: 分片对象 在分片发送之前request，可以用来做分片验证，如果此分片已经上传成功了，可返回一个rejected promise来跳过此分片上传
            after-send-file     file: File对象    在所有分片都上传完毕后，且没有错误后request，用来做分片验证，此时如果promise被reject，当前文件上传会触发错误。
        */
        //判断是不是分片上传，要是分片上传，则要在初始化之前去做Hook，所以添加了一个判断
        if(this.patchOpction){
            WebUploader.Uploader.register({
                'add-file':'addFile',
                'before-send-file': 'beforeSendFile',
                'before-send': 'beforeSend',
                'after-send-file':'afterSendFile'
            }, {
                addFile:this.patchOpction["addFile"]||function(files){
                    console.log(files);
                },
                beforeSendFile:this.patchOpction["beforeSendFile"]||function (file) {
                    console.log(file);
                },
                beforeSend: this.patchOpction["beforeSend"]||function (block) {
                    console.log(block);
                },
                afterSendFile:this.patchOpction["afterSendFile"]||function(file){
                    console.log(file);
                }
            });
        }
 
        // 实例化
        this.uploader = WebUploader.create({
 
            // 指定Drag And Drop拖拽的容器，如果不指定，则不启动
            dnd: this.opction["dnd"],
 
            //是否禁掉整个页面的拖拽功能，如果不禁用，图片拖进来的时候会默认被浏览器打开。
            disableGlobalDnd: this.opction["disableGlobalDnd"] || false,
 
            //指定监听paste事件的容器，如果不指定，不启用此功能。此功能为通过粘贴来添加截屏的图片。建议设置为document.body
            paste: document.body,
 
            // 指定选择文件的按钮容器，不指定则不创建按钮。
            pick: {
                //指定选择文件的按钮容器，不指定则不创建按钮。注意 这里虽然写的是 id, 但是不是只支持 id, 还支持 class, 或者 dom 节点。
                id: '#' + this.opction["selectFileId"],
 
                //{String} 指定按钮文字。不指定时优先从指定的容器中看是否自带文字。
                innerHTML: this.opction["selectFileLabel"] || '点击选择图片',
 
                // {Boolean} 是否开起同时选择多个文件能力
                multiple: this.opction["selectFileMultiple"] || false
            },
 
            //指定接受哪些类型的文件。 由于目前还有ext转mimeType表，所以这里需要分开指定。
            accept: this.opction["accept"] || {
 
                //文字描述
                title: 'Images',
 
                //允许的文件后缀，不带点，多个用逗号分割。
                extensions: 'gif,jpg,jpeg,bmp,png',
 
                //多个用逗号分割。
                mimeTypes: 'image/*'
            },
 
            //配置生成缩略图的选项。默认如下
            thumb: this.opction["thumb"]||{
                width: 110,
                height: 110,
 
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 70,
 
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: true,
 
                // 是否允许裁剪。
                crop: true,
 
                // 为空的话则保留原有图片格式。
                // 否则强制转换成指定的类型。
                type: 'image/jpeg'
            },
 
            //配置压缩的图片的选项。如果此选项为false, 则图片在上传前不进行压缩。默认如下
            compress: this.opction["compress"] || {
                width: 1600,
                height: 1600,
 
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 90,
 
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: false,
 
                // 是否允许裁剪。
                crop: false,
 
                // 是否保留头部meta信息。
                preserveHeaders: true,
 
                // 如果发现压缩后文件大小比原来还大，则使用原来图片
                // 此属性可能会影响图片自动纠正功能
                noCompressIfLarger: false,
 
                // 单位字节，如果图片大小小于此值，不会采用压缩。
                compressSize: 0
            },
 
            //{Boolean} [可选] [默认值：false] 设置为 true 后，不需要手动调用上传，有文件选择即开始上传。
            auto: this.opction["auto"] || false,
 
            //{Object} [可选] [默认值：html5,flash] 指定运行时启动顺序。默认会想尝试 html5 是否支持，如果支持则使用 html5, 否则则使用 flash.可以将此值设置成 flash，来强制使用 flash 运行时。
            runtimeOrder:this.opction["runtimeOrder"]||"html5,flash",
 
            // {Boolean} [可选] [默认值：false] 是否允许在文件传输时提前把下一个文件准备好。 对于一个文件的准备工作比较耗时，比如图片压缩，md5序列化。 如果能提前在当前文件传输期处理，可以节省总体耗时。
            prepareNextFile: this.opction["prepareNextFile"] || false,
 
            //{Boolean} [可选] [默认值：false] 是否要分片处理大文件上传。
            chunked: this.opction["chunked"] || false,
 
            //{Boolean} [可选] [默认值：5242880] 如果要分片，分多大一片？ 默认大小为5M.
            chunkSize: this.opction["chunkSize"] || 1024 * 1024 * 5,
 
            //{Boolean} [可选] [默认值：2] 如果某个分片由于网络问题出错，允许自动重传多少次？
            chunkRetry: this.opction["chunkRetry"] || 2,
 
            //上传并发数。允许同时最大上传进程数。
            threads:this.opction["threads "]||3,
 
            //设置文件上传域的name。
            fileVal:this.opction["fileVal"]||"file",
 
            //文件上传方式，POST或者GET。
            method:this.opction["method"]||"POST",
 
            //文件上传请求的参数表，每次发送都会发送此对象中的参数。
            formData:this.opction["formData "]||{},
 
            //是否已二进制的流的方式发送文件，这样整个上传内容php://input都为文件内容， 其他参数在$_GET数组中。
            sendAsBinary:this.opction["sendAsBinary"]||false,
 
            //验证文件总数量, 超出则不允许加入队列。
            fileNumLimit: this.opction["fileNumLimit"] || 1,
 
            //验证文件总大小是否超出限制, 超出则不允许加入队列
            fileSizeLimit: this.opction["fileSizeLimit"] || 5 * 1024 * 1024,    // 200 M
 
            //验证单个文件大小是否超出限制, 超出则不允许加入队列。
            fileSingleSizeLimit: this.opction["fileSingleSizeLimit"] || 1 * 1024 * 1024,    // 50 M
 
            //{Boolean} [可选] [默认值：undefined] 去重， 根据文件名字、文件大小和最后修改时间来生成hash Key.
            duplicate:this.opction["duplicate"],
 
            // swf文件路径
            swf: this.swf_path,
 
            //服务器地址
            server: this.opction["server"],
 
        });
 
        //当文件被加入队列之前触发，此事件的handler返回值为false，则此文件不会被添加进入队列。
        this.uploader.on('beforeFileQueued',this.opction["beforeFileQueued"]||function(file){
            console.log(file);
        }),
 
        //当文件被加入队列以后触发。
        this.uploader.on('fileQueued', this.opction["fileQueued"] || function (file) {
            console.log('fileQueued',file);
        });
 
        //当一批文件添加进队列以后触发。
        this.uploader.on('filesQueued', this.opction["filesQueued"] || function (file) {
            console.log('filesQueued',file);
        });
 
        //当文件被移除队列后触发。
        this.uploader.on('fileDequeued', this.opction["fileDequeued"] || function (file) {
            console.log('fileDequeued',file);
        });
 
        //当 uploader 被重置的时候触发。
        this.uploader.on("reset", this.opction["reset"] || function () {
        });
 
        //当开始上传流程时触发。
        this.uploader.on("startUpload", this.opction["startUpload"] || function () {
        });
 
        //当开始上传流程暂停时触发。
        this.uploader.on("stopUpload", this.opction["stopUpload"] || function () {
        });
 
        //当所有文件上传结束时触发。
        this.uploader.on("uploadFinished", this.opction["uploadFinished"] || function () {
        });
 
        //某个文件开始上传前触发，一个文件只会触发一次。
        this.uploader.on("uploadStart", this.opction["uploadStart"] || function (file) {
            console.log("uploadStart",file);
        });
 
        /*当某个文件的分块在发送前触发，主要用来询问是否要添加附带参数，大文件在开起分片上传的前提下此事件可能会触发多次。
            object {Object}
            data {Object}默认的上传参数，可以扩展此对象来控制上传参数。
            headers {Object}可以扩展此对象来控制上传头部。
        */
        this.uploader.on('uploadBeforeSend', this.opction["uploadBeforeSend"] || function (obj, data, headers) {
            console.log('uploadBeforeSend',obj, data, headers);
        });
 
        /*
            当某个文件上传到服务端响应后，会派送此事件来询问服务端响应是否有效。如果此事件handler返回值为false, 则此文件将派送server类型的uploadError事件
            object {Object}
            ret {Object}服务端的返回数据，json格式，如果服务端不是json格式，从ret._raw中取数据，自行解析
        */
        this.uploader.on('uploadAccept', this.opction["uploadAccept"] || function (obj, ret) {
            console.log('uploadAccept',obj,ret);
        });
 
        /* 上传过程中触发，携带上传进度。
            file {File}File对象
            percentage {Number}上传进度
        */
        this.uploader.on('uploadProgress', this.opction["uploadProgress"] || function (file, percentage) {
            console.log('uploadProgress',file, percentage);
        });
 
        /* 当文件上传出错时触发。
            file {File}File对象
            reason {String}出错的code
        */
        this.uploader.on('uploadError', this.opction["uploadError"] || function (file,reason) {
            console.log('uploadError',file, reason ,"上传失败");
        });
 
        /*
            文件上传成功，给item添加成功class, 用样式标记上传成功。
            file {File}File对象
            response {Object}服务端返回的数据
        */
        this.uploader.on('uploadSuccess', this.opction["uploadSuccess"] || function (file,response) {
            console.log('uploadSuccess',file,response, "上传成功");
        });
 
        // 不管成功或者失败，文件上传完成时触发。
        this.uploader.on('uploadComplete', this.opction["uploadComplete"] || function (file) {
            console.log('uploadComplete',file, "上传结束")
        });
 
        /*
            当validate不通过时，会以派送错误事件的形式通知调用者。通过upload.on('error', handler)可以捕获到此类错误，目前有以下错误会在特定的情况下派送错来。
            Q_EXCEED_NUM_LIMIT 在设置了fileNumLimit且尝试给uploader添加的文件数量超出这个值时派送。
            Q_EXCEED_SIZE_LIMIT 在设置了Q_EXCEED_SIZE_LIMIT且尝试给uploader添加的文件总大小超出这个值时派送。
            Q_TYPE_DENIED 当文件类型不满足时触发。
            type {String}错误类型。
         */
        this.uploader.on("error", this.opction["error"] || function (type) {
            console.log("error",type);
        });
        return this.uploader;
    };
 
    /**
     * 在window里面进行实例化处理
     * @type {webUploadFunction}
     */
    window.muuUploader = new webUploadFunction();
})(window, $);