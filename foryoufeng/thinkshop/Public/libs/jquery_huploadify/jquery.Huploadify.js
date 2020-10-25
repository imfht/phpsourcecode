// 文件上传插件(Bootstrap.Huploadify v2.1)
(function($) {
    $.fn.Huploadify = function(opts) {
        var itemTemp = '<div id="${fileID}" class="uploadify-queue-item"><div class="uploadify-progress"><div class="uploadify-progress-bar"></div></div><span class="up_filename">${fileName}</span><span class="uploadbtn">上传</span><span class="delfilebtn">删除</span></div>';
        var defaults = {
            fileTypeExts: "*.*",
            uploader: "",
            auto: true,
            method: "post",
            multi: false,
            formData: {},
            fileObjName: "file",
            fileSizeLimit: 2048,
            showUploadedPercent: true,
            showUploadedSize: false,
            buttonText: "选择文件",
            removeTimeout: 1000,
            itemTemplate: itemTemp,
            breakPoints: false,
            fileSplitSize: 1024 * 1024,
            getUploadedSize: null,
            saveUploadedSize: null,
            saveInfoLocal: false,
            onUploadStart: null,
            onUploadSuccess: null,
            onUploadComplete: null,
            onUploadError: null,
            onInit: null,
            onCancel: null,
            onSelect: null
        };
        var option = $.extend(defaults, opts);
        var formatFileSize = function(size, byKB) {
            if (size > 1024 * 1024 && !byKB) {
                size = (Math.round(size * 100 / (1024 * 1024)) / 100).toString() + "MB"
            } else {
                size = (Math.round(size * 100 / 1024) / 100).toString() + "KB"
            }
            return size
        };
        var getFile = function(index, files) {
            for (var i = 0; i < files.length; i++) {
                if (files[i].index == index) {
                    return files[i]
                }
            }
            return false
        };
        var getFileTypes = function(str) {
            var result = [];
            var arr1 = str.split(";");
            for (var i = 0, len = arr1.length; i < len; i++) {
                result.push(arr1[i].split(".").pop())
            }
            return result
        };
        var mimetypeMap = {
            zip: ["application/x-zip-compressed"],
            jpg: ["image/jpeg"],
            png: ["image/png"],
            gif: ["image/gif"],
            doc: ["application/msword"],
            xls: ["application/msexcel"],
            docx: ["application/vnd.openxmlformats-officedocument.wordprocessingml.document"],
            xlsx: ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"],
            ppt: ["application/vnd.ms-powerpoint "],
            pptx: ["application/vnd.openxmlformats-officedocument.presentationml.presentation"],
            mp3: ["audio/mp3"],
            mp4: ["video/mp4"],
            pdf: ["application/pdf"]
        };
        var getMimetype = function(name) {
            return mimetypeMap[name]
        };
        var getAcceptString = function(str) {
            var types = getFileTypes(str);
            var result = [];
            for (var i = 0, len = types.length; i < len; i++) {
                var mime = getMimetype(types[i]);
                if (mime) {
                    result.push(mime)
                }
            }
            return result.join(",")
        };
        var sendBlob = function(url, xhr, file, formdata) {
            xhr.open(option.method, url, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            var fd = new FormData();
            fd.append(option.fileObjName, file);
            if (formdata) {
                for (key in formdata) {
                    fd.append(key, formdata[key])
                }
            }
            xhr.send(fd)
        };
        var fileObj = null;
        this.each(function() {
            var _this = $(this);
            var instanceNumber = $(".uploadify").length + 1;
            var inputStr = '<input id="select_btn_' + instanceNumber + '" class="selectbtn" style="display:none;" type="file" name="fileselect[]"';
            inputStr += option.multi ? " multiple" : "";
            inputStr += ' accept="';
            inputStr += getAcceptString(option.fileTypeExts);
            inputStr += '"/>';
            inputStr += '<a id="file_upload_' + instanceNumber + '-button" href="javascript:void(0)" class="uploadify-button btn btn-primary">';
            inputStr += option.buttonText;
            inputStr += "</a>";
            var uploadFileListStr = '<div id="file_upload_' + instanceNumber + '-queue" class="uploadify-queue"></div>';
            _this.append(inputStr + uploadFileListStr);
            fileObj = {
                uploadAllowed: true,
                fileInput: _this.find(".selectbtn"),
                uploadFileList: _this.find(".uploadify-queue"),
                container: _this,
                url: option.uploader,
                fileFilter: [],
                uploadOver: false,
                filter: function(files) {
                    var arr = [];
                    var typeArray = getFileTypes(option.fileTypeExts);
                    if (typeArray.length > 0) {
                        for (var i = 0, len = files.length; i < len; i++) {
                            var thisFile = files[i];
                            if (parseInt(formatFileSize(thisFile.size, true)) > option.fileSizeLimit) {
                                alert("文件" + thisFile.name + "大小超出限制！");
                                continue
                            }
                            if ($.inArray(thisFile.name.split(".").pop().toLowerCase(), typeArray) >= 0 || $.inArray("*", typeArray) >= 0) {
                                arr.push(thisFile)
                            } else {
                                alert("文件" + thisFile.name + "类型不允许！")
                            }
                        }
                    }
                    return arr
                },
                funSelect: function(files) {
                    for (var i = 0, len = files.length; i < len; i++) {
                        var file = files[i];
                        var $html = $(option.itemTemplate.replace(/\${fileID}/g, "fileupload_" + instanceNumber + "_" + file.index).replace(/\${fileName}/g, file.name).replace(/\${fileSize}/g, formatFileSize(file.size)).replace(/\${instanceID}/g, _this.attr("id")));
                        if (option.auto) {
                            $html.find(".uploadbtn").remove()
                        }
                        var initWidth = 0,
                            initFileSize = "0KB",
                            initUppercent = "0%";
                        if (option.breakPoints) {
                            var uploadedSize = this.funGetUploadedSize(file);
                            initWidth = (uploadedSize / file.size * 100) + "%";
                            initFileSize = formatFileSize(uploadedSize);
                            initUppercent = (uploadedSize / file.size * 100).toFixed(2) + "%";
                            $html.find(".uploadify-progress-bar").css("width", initWidth)
                        }
                        this.uploadFileList.append($html);
                        if (option.showUploadedSize) {
                            var num = '<span class="progressnum"><span class="uploadedsize">' + initFileSize + '</span>/<span class="totalsize">${fileSize}</span></span>'.replace(/\${fileSize}/g, formatFileSize(file.size));
                            $html.find(".uploadify-progress").after(num)
                        }
                        if (option.showUploadedPercent) {
                            var percentText = '<span class="up_percent">' + initUppercent + "</span>";
                            $html.find(".uploadify-progress").after(percentText)
                        }
                        option.onSelect && option.onSelect(files);
                        if (option.auto) {
                            this.funUploadFile(file)
                        } else {
                            $html.find(".uploadbtn").on("click", (function(file) {
                                return function() {
                                    fileObj.funUploadFile(file)
                                }
                            })(file))
                        }
                        $html.find(".delfilebtn").on("click", (function(file) {
                            return function() {
                                fileObj.funDeleteFile(file.index)
                            }
                        })(file))
                    }
                },
                onProgress: function(file, loaded, total) {
                    var eleProgress = _this.find("#fileupload_" + instanceNumber + "_" + file.index + " .uploadify-progress");
                    var thisLoaded = loaded;
                    var lastLoaded = eleProgress.attr("lastLoaded") || 0;
                    loaded -= parseInt(lastLoaded);
                    var progressBar = eleProgress.children(".uploadify-progress-bar");
                    var oldWidth = option.breakPoints ? parseFloat(progressBar.get(0).style.width || 0) : 0;
                    var percent = (loaded / total * 100 + oldWidth).toFixed(2);
                    var percentText = percent > 100 ? "99.99%" : percent + "%";
                    if (option.showUploadedSize) {
                        eleProgress.nextAll(".progressnum .uploadedsize").text(formatFileSize(loaded));
                        eleProgress.nextAll(".progressnum .totalsize").text(formatFileSize(total))
                    }
                    if (option.showUploadedPercent) {
                        eleProgress.nextAll(".up_percent").text(percentText)
                    }
                    progressBar.css("width", percentText);
                    if (thisLoaded < option.fileSplitSize) {
                        eleProgress.attr("lastLoaded", thisLoaded)
                    } else {
                        eleProgress.removeAttr("lastLoaded")
                    }
                },
                funGetProgressWidth: function(index) {
                    var eleProgressBar = _this.find("#fileupload_" + instanceNumber + "_" + index + " .uploadify-progress-bar");
                    return eleProgressBar.get(0).style.width || ""
                },
                funGetUploadedSize: function(file) {
                    if (option.getUploadedSize) {
                        return option.getUploadedSize(file)
                    } else {
                        if (option.saveInfoLocal) {
                            return parseInt(localStorage.getItem(file.name)) || 0
                        }
                    }
                },
                funSaveUploadedSize: function(file, value) {
                    if (option.saveUploadedSize) {
                        option.saveUploadedSize(file, value)
                    } else {
                        if (option.saveInfoLocal) {
                            localStorage.setItem(file.name, value)
                        }
                    }
                },
                funGetFiles: function(e) {
                    var files = e.target.files;
                    files = this.filter(files);
                    for (var i = 0, len = files.length; i < len; i++) {
                        this.fileFilter.push(files[i])
                    }
                    this.funDealFiles(files);
                    return this
                },
                funDealFiles: function(files) {
                    var fileCount = _this.find(".uploadify-queue .uploadify-queue-item").length;
                    for (var i = 0, len = files.length; i < len; i++) {
                        files[i].index = ++fileCount;
                        files[i].id = "fileupload_" + instanceNumber + "_" + files[i].index
                    }
                    this.funSelect(files);
                    return this
                },
                funDeleteFile: function(index) {
                    for (var i = 0, len = this.fileFilter.length; i < len; i++) {
                        var file = this.fileFilter[i];
                        if (file.index == index) {
                            if (option.breakPoints) {
                                this.uploadAllowed = false
                            }
                            this.fileFilter.splice(i, 1);
                            _this.find("#fileupload_" + instanceNumber + "_" + index).fadeOut();
                            fileObj.fileInput.val("");
                            option.onCancel && option.onCancel(file);
                            break
                        }
                    }
                    return this
                },
                funUploadFile: function(file) {
                    var xhr = false;
                    var originalFile = file;
                    var thisfile = _this.find("#fileupload_" + instanceNumber + "_" + file.index);
                    var regulateView = function() {
                        if (fileObj.uploadOver) {
                            thisfile.find(".uploadify-progress-bar").css("width", "100%");
                            option.showUploadedSize && thisfile.find(".uploadedsize").text(thisfile.find(".totalsize").text());
                            option.showUploadedPercent && thisfile.find(".up_percent").text("100%")
                        }
                    };
                    try {
                        xhr = new XMLHttpRequest()
                    } catch (e) {
                        xhr = ActiveXobject("Msxml12.XMLHTTP")
                    }
                    if (option.breakPoints) {
                        var fileName = file.name,
                            fileId = file.id,
                            fileIndex = file.index,
                            fileSize = file.size;
                        var uploadedSize = parseInt(this.funGetUploadedSize(originalFile));
                        file = originalFile.slice(uploadedSize, uploadedSize + option.fileSplitSize);
                        file.name = fileName;
                        file.id = fileId;
                        file.index = fileIndex
                    }
                    if (xhr.upload && uploadedSize !== false) {
                        xhr.upload.addEventListener("progress", function(e) {
                            fileObj.onProgress(file, e.loaded, originalFile.size)
                        }, false);
                        xhr.onreadystatechange = function(e) {
                            if (xhr.readyState == 4) {
                                fileObj.uploadOver = true;
                                if (xhr.status == 200) {
                                    var returnData = JSON.parse(xhr.responseText);
                                    if (returnData.success) {
                                        if (option.breakPoints) {
                                            uploadedSize += option.fileSplitSize;
                                            fileObj.funSaveUploadedSize(originalFile, uploadedSize);
                                            if (uploadedSize < fileSize) {
                                                fileObj.uploadOver = false;
                                                if (fileObj.uploadAllowed) {
                                                    file = originalFile.slice(uploadedSize, uploadedSize + option.fileSplitSize);
                                                    file.name = fileName;
                                                    file.id = fileId;
                                                    file.index = fileIndex;
                                                    file.size = fileSize;
                                                    sendBlob(fileObj.url, xhr, file, option.formData)
                                                }
                                            } else {
                                                regulateView()
                                            }
                                        } else {
                                            regulateView()
                                        }
                                    }
                                    if (fileObj.uploadOver) {
                                        option.onUploadSuccess && option.onUploadSuccess(originalFile, xhr.responseText);
                                        setTimeout(function() {
                                            _this.find("#fileupload_" + instanceNumber + "_" + originalFile.index).fadeOut()
                                        }, option.removeTimeout)
                                    }
                                } else {
                                    fileObj.uploadOver && option.onUploadError && option.onUploadError(originalFile, xhr.responseText)
                                }
                                if (fileObj.uploadOver) {
                                    option.onUploadComplete && option.onUploadComplete(originalFile, xhr.responseText);
                                    fileObj.fileInput.val("")
                                }
                            }
                        };
                        option.onUploadStart && option.onUploadStart();
                        option.formData.fileName = originalFile.name;
                        option.formData.lastModifiedDate = originalFile.lastModifiedDate.getTime();
                        fileObj.uploadAllowed = true;
                        sendBlob(this.url, xhr, file, option.formData)
                    }
                },
                init: function() {
                    if (this.fileInput.length > 0) {
                        this.fileInput.change(function(e) {
                            fileObj.funGetFiles(e)
                        })
                    }
                    _this.find(".uploadify-button").on("click", function() {
                        _this.find(".selectbtn").trigger("click")
                    });
                    option.onInit && option.onInit()
                }
            };
            fileObj.init()
        });
        var returnObj = {
            stop: function() {
                fileObj.uploadOver = false;
                fileObj.uploadAllowed = false
            },
            upload: function(fileIndex) {
                if (fileIndex === "*") {
                    for (var i = 0, len = fileObj.fileFilter.length; i < len; i++) {
                        fileObj.funUploadFile(fileObj.fileFilter[i])
                    }
                } else {
                    var file = getFile(fileIndex, fileObj.fileFilter);
                    file && fileObj.funUploadFile(file)
                }
            },
            cancel: function(fileIndex) {
                if (fileIndex === "*") {
                    for (var i = 0, len = fileObj.fileFilter.length; i < len; i++) {
                        fileObj.funDeleteFile(++i)
                    }
                } else {
                    fileObj.funDeleteFile(fileIndex)
                }
            },
            disable: function(instanceID) {
                var parent = instanceID ? $("file_upload_" + instanceID + "-button") : $("body");
                parent.find(".uploadify-button").css("background-color", "#888").off("click")
            },
            ennable: function(instanceID) {
                var parent = instanceID ? $("file_upload_" + instanceID + "-button") : $("body");
                parent.find(".uploadify-button").css("background-color", "#707070").on("click", function() {
                    parent.find(".selectbtn").trigger("click")
                })
            },
            destroy: function() {
                fileObj.container.html("")
            },
            settings: function(name, value) {
                if (arguments.length == 1) {
                    return option[name]
                } else {
                    if (name == "formData") {
                        option.formData = $.extend(option.formData, value)
                    } else {
                        option[name] = value
                    }
                }
            },
            Huploadify: function() {
                var method = arguments[0];
                if (method in this) {
                    Array.prototype.splice.call(arguments, 0, 1);
                    this[method].apply(this[method], arguments)
                }
            }
        };
        return returnObj
    }
})(jQuery);