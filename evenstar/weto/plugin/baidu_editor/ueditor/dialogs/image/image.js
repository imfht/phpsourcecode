/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-01-08
 * Time: 下午2:52
 * To change this template use File | Settings | File Templates.
 */
var imageUploader = {},
    flashObj = null,
    postConfig=[];
(function () {
    var g = $G,
        ajax = parent.baidu.editor.ajax,
        maskIframe = g("maskIframe"); //tab遮罩层,用来解决flash和其他dom元素的z-index层级不一致问题
       // flashObj;                   //flash上传对象

    var flagImg = null, flashContainer;
    imageUploader.init = function (opt, callbacks) {
        switchTab("imageTab");
        createAlignButton(["remoteFloat", "localFloat"]);
        createFlash(opt, callbacks);
        var srcImg = editor.selection.getRange().getClosedNode();
        if (srcImg) {
            showImageInfo(srcImg);
            showPreviewImage(srcImg, true);
            var tabElements = g("imageTab").children,
                tabHeads = tabElements[0].children,
                tabBodys = tabElements[1].children;
            for (var i = 0, ci; ci = tabHeads[i++];) {
                if (ci.getAttribute("tabSrc") == "remote") {
                    clickHandler(tabHeads, tabBodys, ci);
                }
            }

        }
        addUrlChangeListener();
        addOKListener();
        addScrollListener();
        $focus(g("url"));
    };
    imageUploader.setPostParams = function(obj,index){
        if(index===undefined){
            utils.each(postConfig,function(config){
                config.data = obj;
            })
        }else{
            postConfig[index].data = obj;
        }
    };

    function insertImage(imgObjs) {
        editor.fireEvent('beforeInsertImage', imgObjs);
        editor.execCommand("insertImage", imgObjs);
    }

    function selectTxt(node) {
        if (node.select) {
            node.select();
        } else {
            var r = node.createTextRange && node.createTextRange();
            r.select();
        }
    }


    /**
     * 延迟加载
     */
    function addScrollListener() {

        g("imageList").onscroll = function () {
            var imgs = this.getElementsByTagName("img"),
                top = Math.ceil(this.scrollTop / 100) - 1;
            top = top < 0 ? 0 : top;
            for (var i = top * 5; i < (top + 5) * 5; i++) {
                var img = imgs[i];
                if (img && !img.getAttribute("src")) {
                    img.src = img.getAttribute("lazy_src");
                    img.removeAttribute("lazy_src");
                }
            }
        }
    }

    /**
     * 绑定确认按钮
     */
    function addOKListener() {
        dialog.onok = function () {
            var currentTab = findFocus("tabHeads", "tabSrc");
            switch (currentTab) {
                case "remote":
                    return insertSingle();
                    break;
                case "local":
                    return insertBatch();
                    break;
                case "imgManager":
                    return insertSearch("imageList");
                    break;
                case "imgSearch":
                    return insertSearch("searchList", true);
                    break;
            }
        };
        dialog.oncancel = function () {
            hideFlash();
        }
    }

    function hideFlash() {
        flashObj = null;
        flashContainer.innerHTML = "";
    }

    /**
     * 将元素id下的所有图片文件插入到编辑器中。
     * @param id
     * @param catchRemote  是否需要替换远程图片
     */
    function insertSearch(id, catchRemote) {
        var imgs = $G(id).getElementsByTagName("img"), imgObjs = [];
        for (var i = 0, ci; ci = imgs[i++];) {
            if (ci.getAttribute("selected")) {
                var url = ci.getAttribute("src", 2).replace(/(\s*$)/g, ""), img = {};
                img.src = url;
                img._src = url;
                imgObjs.push(img);
            }
        }
        insertImage(imgObjs);
        catchRemote && editor.fireEvent("catchRemoteImage");
        hideFlash();
    }

    /**
     * 插入单张图片
     */
    function insertSingle() {
        var url = g("url"),
            width = g("width"),
            height = g("height"),
            border = g("border"),
            vhSpace = g("vhSpace"),
            title = g("title"),
            align = findFocus("remoteFloat", "name"),
            imgObj = {};
        if (!url.value) return;
        if (!flagImg) return;   //粘贴地址后如果没有生成对应的预览图，可以认为本次粘贴地址失败
        if (!checkNum([width, height, border, vhSpace])) return false;
        imgObj.src = url.value;
        imgObj._src = url.value;
        imgObj.width = width.value;
        imgObj.height = height.value;
        imgObj.border = border.value;
        imgObj.floatStyle = align;
        imgObj.vspace = imgObj.hspace = vhSpace.value;
        imgObj.title = title.value;
        imgObj.style = "width:" + width.value + "px;height:" + height.value + "px;";
        insertImage(imgObj);
        editor.fireEvent("catchRemoteImage");
        hideFlash();
    }

    /**
     * 检测传入的所有input框中输入的长宽是否是正数
     * @param nodes input框集合，
     */
    function checkNum(nodes) {
        for (var i = 0, ci; ci = nodes[i++];) {
            if (!isNumber(ci.value) || ci.value < 0) {
                alert(lang.numError);
                ci.value = "";
                ci.focus();
                return false;
            }
        }
        return true;
    }

    /**
     * 数字判断
     * @param value
     */
    function isNumber(value) {
        return /(0|^[1-9]\d*$)/.test(value);
    }

    /**
     * 插入多张图片
     */
    function insertBatch() {
        if (imageUrls.length < 1) return;
        var imgObjs = [],
            align = findFocus("localFloat", "name");

        for (var i = 0, ci; ci = imageUrls[i++];) {
            var tmpObj = {};
            tmpObj.title = ci.title;
            tmpObj.floatStyle = align;
            //修正显示时候的地址数据,如果后台返回的是图片的绝对地址，那么此处无需修正
            tmpObj._src = tmpObj.src = (ci.url.indexOf('http://') == -1 ? editor.options.imagePath : '' ) + ci.url;
            imgObjs.push(tmpObj);
        }
        insertImage(imgObjs);
        hideFlash();
    }

    /**
     * 找到id下具有focus类的节点并返回该节点下的某个属性
     * @param id
     * @param returnProperty
     */
    function findFocus(id, returnProperty) {
        var tabs = g(id).children,
            property;
        for (var i = 0, ci; ci = tabs[i++];) {
            if (ci.className == "focus") {
                property = ci.getAttribute(returnProperty);
                break;
            }
        }
        return property;
    }

    /**
     * 绑定地址框改变事件
     */
    function addUrlChangeListener() {
        var value = g("url").value;
        if (browser.ie) {
            g("url").onpropertychange = function () {
                var v = this.value;
                if (v != value) {
                    createPreviewImage(v);
                    value = v;
                }
            };
        } else {
            g("url").addEventListener("input", function () {
                var v = this.value;
                if (v != value) {
                    createPreviewImage(v);
                    value = v;
                }
            }, false);
        }
    }

    /**
     * 绑定图片等比缩放事件
     * @param percent  缩放比例
     */
    function addSizeChangeListener(percent) {
        var width = g("width"),
            height = g("height"),
            lock = g('lock');
        width.onkeyup = function () {
            if (!isNaN(this.value) && lock.checked) {
                height.value = Math.round(this.value / percent) || this.value;
            }
        };
        height.onkeyup = function () {
            if (!isNaN(this.value) && lock.checked) {
                width.value = Math.round(this.value * percent) || this.value;
            }
        }
    }

    /**
     * 依据url中的地址创建一个预览图片并将对应的信息填入信息框和预览框
     */
    function createPreviewImage(url) {
        if (!url) {
            flagImg = null;
            g("preview").innerHTML = "";
            g("width").value = "";
            g("height").value = "";
            g("border").value = "";
            g("vhSpace").value = "";
            g("title").value = "";
            $focus(g("url"));
            return;
        }
        var img = document.createElement("img"),
            preview = g("preview");

        var imgTypeReg = /\.(png|gif|jpg|jpeg)$/gi, //格式过滤
            urlFilter = "";                                     //地址过滤
        if (!imgTypeReg.test(url) || url.indexOf(urlFilter) == -1) {
            preview.innerHTML = "<span style='color: red'>" + lang.imageUrlError + "</span>";
            flagImg = null;
            return;
        }
        preview.innerHTML = lang.imageLoading;
        img.onload = function () {
            flagImg = this;
            showImageInfo(this);
            showPreviewImage(this,true);
            this.onload = null;
        };
        img.onerror = function () {
            preview.innerHTML = "<span style='color: red'>" + lang.imageLoadError + "</span>";
            flagImg = null;
            this.onerror = null;
        };
        img.src = url;
    }

    /**
     * 显示图片对象的信息
     * @param img
     */
    function showImageInfo(img) {
        if (!img.getAttribute("src") || !img.src) return;
        var wordImgFlag = img.getAttribute("word_img");
        g("url").value = wordImgFlag ? wordImgFlag.replace("&amp;", "&") : (img.getAttribute('_src') || img.getAttribute("src", 2).replace("&amp;", "&"));
        g("width").value = img.width || 0;
        g("height").value = img.height || 0;
        g("border").value = img.getAttribute("border") || 0;
        g("vhSpace").value = img.getAttribute("vspace") || 0;
        g("title").value = img.title || "";
        var align = editor.queryCommandValue("imageFloat") || "none";
        updateAlignButton(align);

        //保存原始比例，用于等比缩放
        var percent = (img.width / img.height).toFixed(2);
        addSizeChangeListener(percent);
    }

    /**
     * 将img显示在预览框，
     * @param img
     * @param needClone  是否需要克隆后显示
     */
    function showPreviewImage(img, needClone) {
        var tmpWidth = img.width, tmpHeight = img.height;
        var maxWidth = 262,maxHeight = 262,
            target = scaling(tmpWidth,tmpHeight,maxWidth,maxHeight);
        target.border = img.border||0;
        target.src = img.src;
        flagImg = true;
        if ((target.width + 2 * target.border) > maxWidth) {
            target.width = maxWidth - 2 * target.border;
        }
        if ((target.height + 2 * target.border) > maxWidth) {
            target.height = maxWidth - 2 * target.border;
        }
        var preview = g("preview");
        preview.innerHTML = '<img src="' + target.src + '" width="' + target.width + '" height="' + target.height + '" border="' + target.border + 'px solid #000" />';
    }

    /**
     * 图片缩放
     * @param img
     * @param max
     */
    function scale(img, max, oWidth, oHeight) {
        var width = 0, height = 0, percent, ow = img.width || oWidth, oh = img.height || oHeight;
        if (ow > max || oh > max) {
            if (ow >= oh) {
                if (width = ow - max) {
                    percent = (width / ow).toFixed(2);
                    img.height = oh - oh * percent;
                    img.width = max;
                }
            } else {
                if (height = oh - max) {
                    percent = (height / oh).toFixed(2);
                    img.width = ow - ow * percent;
                    img.height = max;
                }
            }
        }
    }

    function scaling(width,height,maxWidth,maxHeight){
        if(width<maxWidth && height<maxHeight) return {width:width,height:height};
        var srcRatio = (width/height).toFixed(2),
            tarRatio = (maxWidth/maxHeight).toFixed(2),
            w,h;
        if(srcRatio<tarRatio){
            h = maxHeight;
            w = h*srcRatio;
        }else{
            w = maxWidth;
            h = w/srcRatio;
        }
        return {width:w.toFixed(0),height:h.toFixed(0)}
    }
    /**
     * 创建flash实例
     * @param opt
     * @param callbacks
     */
    function createFlash(opt, callbacks) {
        var i18n = utils.extend({}, lang.flashI18n);
        //处理图片资源地址的编码，补全等问题
        for (var i in i18n) {
            if (!(i in {"lang":1, "uploadingTF":1, "imageTF":1, "textEncoding":1}) && i18n[i]) {
                i18n[i] = encodeURIComponent(editor.options.langPath + editor.options.lang + "/images/" + i18n[i]);
            }
        }
        opt = utils.extend(opt, i18n, false);
        var option = {
            createOptions:{
                id:'flash',
                url:opt.flashUrl,
                width:opt.width,
                height:opt.height,
                errorMessage:lang.flashError,
                wmode:browser.safari ? 'transparent' : 'window',
                ver:'10.0.0',
                vars:opt,
                container:opt.container
            }
        };
        flashContainer = $G(opt.container);
        option = utils.extend(option, callbacks, false);
        flashObj = new baidu.flash.imageUploader(option);
    }

    /**
     * 依据传入的align值更新按钮信息
     * @param align
     */
    function updateAlignButton(align) {
        var aligns = g("remoteFloat").children;
        for (var i = 0, ci; ci = aligns[i++];) {
            if (ci.getAttribute("name") == align) {
                if (ci.className != "focus") {
                    ci.className = "focus";
                }
            } else {
                if (ci.className == "focus") {
                    ci.className = "";
                }
            }
        }
    }

    /**
     * 创建图片浮动选择按钮
     * @param ids
     */
    function createAlignButton(ids) {
        for (var i = 0, ci; ci = ids[i++];) {
            var floatContainer = g(ci),
                nameMaps = {"none":lang.floatDefault, "left":lang.floatLeft, "right":lang.floatRight, "center":lang.floatCenter};
            for (var j in nameMaps) {
                var div = document.createElement("div");
                div.setAttribute("name", j);
                if (j == "none") div.className = "focus";

                div.style.cssText = "background:url(images/" + j + "_focus.jpg);";
                div.setAttribute("title", nameMaps[j]);
                floatContainer.appendChild(div);
            }
            switchSelect(ci);
        }
    }

    function toggleFlash(show) {
        if (flashContainer && browser.webkit) {
            flashContainer.style.left = show ? "0" : "-10000px";
        }
    }

    /**
     * tab点击处理事件
     * @param tabHeads
     * @param tabBodys
     * @param obj
     */
    function clickHandler(tabHeads, tabBodys, obj) {
        //head样式更改
        for (var k = 0, len = tabHeads.length; k < len; k++) {
            tabHeads[k].className = "";
        }
        obj.className = "focus";
        //body显隐
        var tabSrc = obj.getAttribute("tabSrc");
        for (var j = 0, length = tabBodys.length; j < length; j++) {
            var body = tabBodys[j],
                id = body.getAttribute("id");
            body.onclick = function () {
                this.style.zoom = 1;
            };
            if (id != tabSrc) {
                body.style.zIndex = 1;
            } else {
                body.style.zIndex = 200;
                //当切换到本地图片上传时，隐藏遮罩用的iframe
                if (id == "local") {
                    toggleFlash(true);
                    maskIframe.style.display = "none";
                    //处理确定按钮的状态
                    if (selectedImageCount) {
                        dialog.buttons[0].setDisabled(true);
                    }
                } else {
                    toggleFlash(false);
                    maskIframe.style.display = "";
                    dialog.buttons[0].setDisabled(false);
                }
                var list = g("imageList");
                list.style.display = "none";
                //切换到图片管理时，ajax请求后台图片列表
                if (id == "imgManager") {
                    list.style.display = "";
                    //已经初始化过时不再重复提交请求
                    if (!list.children.length) {
                        ajax.request(editor.options.imageManagerUrl, {
                            timeout:100000,
                            action:"get",
                            onsuccess:function (xhr) {
                                //去除空格
                                var tmp = utils.trim(xhr.responseText),
                                    imageUrls = !tmp ? [] : tmp.split("ue_separate_ue"),
                                    length = imageUrls.length;
                                g("imageList").innerHTML = !length ? "&nbsp;&nbsp;" + lang.noUploadImage : "";
                                for (var k = 0, ci; ci = imageUrls[k++];) {
                                    var img = document.createElement("img");

                                    var div = document.createElement("div");
                                    div.appendChild(img);
                                    div.style.display = "none";
                                    g("imageList").appendChild(div);
                                    img.onclick = function () {
                                        changeSelected(this);
                                    };
                                    img.onload = function () {
                                        this.parentNode.style.display = "";
                                        var w = this.width, h = this.height;
                                        scale(this, 100, 120, 80);
                                        this.title = lang.toggleSelect + w + "X" + h;
                                        this.onload = null;
                                    };
                                    img.setAttribute(k < 35 ? "src" : "lazy_src", editor.options.imageManagerPath + ci.replace(/\s+|\s+/ig, ""));
                                    img.setAttribute("_src", editor.options.imageManagerPath + ci.replace(/\s+|\s+/ig, ""));

                                }
                            },
                            onerror:function () {
                                g("imageList").innerHTML = lang.imageLoadError;
                            }
                        });
                    }
                }
                if (id == "imgSearch") {
                    selectTxt(g("imgSearchTxt"));
                }
                if (id == "remote") {
                    $focus(g("url"));
                }
            }
        }

    }

    /**
     * TAB切换
     * @param tabParentId  tab的父节点ID或者对象本身
     */
    function switchTab(tabParentId) {
        var tabElements = g(tabParentId).children,
            tabHeads = tabElements[0].children,
            tabBodys = tabElements[1].children;

        for (var i = 0, length = tabHeads.length; i < length; i++) {
            var head = tabHeads[i];
            if (head.className === "focus")clickHandler(tabHeads, tabBodys, head);
            head.onclick = function () {
                clickHandler(tabHeads, tabBodys, this);
            }
        }
    }

    /**
     * 改变o的选中状态
     * @param o
     */
    function changeSelected(o) {
        if (o.getAttribute("selected")) {
            o.removeAttribute("selected");
            o.style.cssText = "filter:alpha(Opacity=100);-moz-opacity:1;opacity: 1;border: 2px solid #fff";
        } else {
            o.setAttribute("selected", "true");
            o.style.cssText = "filter:alpha(Opacity=50);-moz-opacity:0.5;opacity: 0.5;border:2px solid blue;";
        }
    }

    /**
     * 选择切换，传入一个container的ID
     * @param selectParentId
     */
    function switchSelect(selectParentId) {
        var select = g(selectParentId),
            children = select.children;
        domUtils.on(select, "click", function (evt) {
            var tar = evt.srcElement || evt.target;
            for (var j = 0, cj; cj = children[j++];) {
                cj.className = "";
                cj.removeAttribute && cj.removeAttribute("class");
            }
            tar.className = "focus";

        });
    }
    
})();
