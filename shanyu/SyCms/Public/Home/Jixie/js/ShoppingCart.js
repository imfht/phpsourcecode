/// <reference path="../Member/Scripts/jquery-1.8.3.min.js" />

//删除元素
function Remove(obj) {
    // $("#" + obj).parent().css("position", "");
    $("#" + obj).remove();
    window.location.reload();
}

//添加进购物车
function CartAdd(obj, type, producttype, product_id, product_quantityInput, product_number, product_price) {
    $("#cart_info").remove();
    var product_quantity = product_quantityInput.val();
    if (product_id == "" || product_quantity == "") {
        return false;
    }
    if (parseInt(product_quantity) > parseInt(product_number)) {
        $.post("/Member/Ajax/LanguageHandler.ashx", { action: "loadLanguage", langus: "购买商品失败;商品添加到购物车失败;购买数量不能大于库存哦", cache: Math.random() }, function (langu) {
            var HintHtml = '<div id="cart_info" class="ctDia-buyTipBox clearfix" style="width:337px">'
					+ '<a class="ctDia-closeBtn" href="javascript:void(0);" onclick="Remove(\'cart_info\');"></a><span class="ctDia-buyIconStatu ctDia-buyIconFail"></span>'
					+ '<div class="ctDia-buyTxtBox"><div class="ctDia-buyTxtTit">';
            if (type == 1)
                HintHtml += langu.购买商品失败 + '！';
            else
                HintHtml += langu.商品添加到购物车失败 + '！';
            HintHtml += '</div><p class="ctDia-buyTxtIntro">' + langu.购买数量不能大于库存哦 + '！</p>'
					+ '</div>'
					+ '</div>';
            $(obj).after(HintHtml); //添加节点
            //$("#cart_info").parent().css("position", "relative");
        }, "json");
        return false;
    }
    $.ajax({
        type: "post",
        url: "/Ajax/ShoppingCartHandler.ashx",
        data: { action: "cart_product_add", product_id: product_id, product_quantity: product_quantity, producttype: producttype, type: type, product_price: product_price, cache: Math.random() },
        dataType: "json",
        beforeSend: function (XMLHttpRequest) {
            //发送前动作
        },
        success: function (data, textStatus) {
            if (data.msg == 1) {
                if (type == 1) {
                    location.href = "/Member/BuyNow.aspx";
                } else {
                    var HintHtml = '<div id="cart_info" class="ctDia-buyTipBox clearfix" style="width:337px">'
						+ '<a class="ctDia-closeBtn" href="javascript:void(0);" onclick="Remove(\'cart_info\');"></a><span class="ctDia-buyIconStatu ctDia-buyIconSuccess"></span>'
						+ '<div class="ctDia-buyTxtBox">'
						+ '<div class="ctDia-buyTxtTit">' + data.msgTitle + '</div>'
						+ '<p class="ctDia-buyTxtIntro">' + data.msgbox + '</p>'
						+ ' <div class="ctDia-buyTxtHand"><a class="goto-accountBtn" title="' + data.gotoCart + '" href="/Member/Cart.aspx"></a>'
						+ '<a title="' + data.thenStroll + '" href="javascript:void(0);" onclick="Remove(\'cart_info\');" class="goto-stroll">' + data.thenStroll + '</a>'
						+ '</div>'
                        + '</div>'
						+ '</div>';
                    $(obj).after(HintHtml); //添加节点
                    //$("#cart_info").parent().css("position", "relative");
                    $(".login-cart em").html(data.count); //给头部购物车数量赋值
                }
            }
            else if (data.msg == 2) {
                LoadLogin(product_id, product_quantity, producttype);
            }
            else {
                var HintHtml = '<div id="cart_info" class="ctDia-buyTipBox clearfix" style="width:337px">'
					+ ' <a class="ctDia-closeBtn" href="javascript:void(0);" onclick="Remove(\'cart_info\');"></a><span class="ctDia-buyIconStatu ctDia-buyIconWarm"></span>'
					+ '<div class="ctDia-buyTxtBox"><div class="ctDia-buyTxtTit">';
                if (type == 1)
                    HintHtml += data.msgTitle1 + '！';
                else
                    HintHtml += data.msgTitle2 + '！';
                HintHtml += '</div><p class="ctDia-buyTxtIntro">' + data.msgbox + '</p>'
					+ '</div>'
					+ '</div>';
                $(obj).after(HintHtml); //添加节点
                //$("#cart_info").parent().css("position", "relative");
            }
        }
    });
    return false;
}

//删除购物车商品
function DeleteCart(obj, product_id, cartItemSelector) {
    if (product_id == "") {
        return false;
    }
    $.ajax({
        url: "/Ajax/ShoppingCartHandler.ashx",
        data: { action: "cart_product_delete", product_id: product_id, cache: Math.random() },
        dataType: "json",
        type: "post",
        beforeSend: function (XMLHttpRequest) {
            //发送前动作
        },
        success: function (data) {
            if (data.msg == 1) {
                var $cartItem = $(obj).parents(cartItemSelector + ":first");
                if (product_id == "0") {
                    $cartItem.replaceWith(data.cartMsg);
                }
                else {
                    if ($cartItem.siblings(cartItemSelector).length > 0) {
                        $cartItem.remove();
                    } else {
                        $(".cartItemPlace").remove();
                        $(".cartItemEmpty").show();
                        $("#J_header-cart .header-carthd a em").html(0);
                    }
                }
            } else {
                alert(data.msgbox);
            }
        }
    });
    return false;
}

function LoadLogin(product_id, product_quantity, producttype) {
    $.post("/Member/Ajax/LanguageHandler.ashx", {
        action: "loadLanguage",
        langus: "用户登录;用户名;密码ToeasyDialog;验证码;登录2;注册;忘记密码;看不清换一张;请输入用户名密码和验证码;请输入用户名和密码;请输入用户名和验证码;请输入密码和验证码;请输入用户名;请输入密码;请输入验证码"
    , cache: Math.random()
    }, function (langu) {
        var strhtml = '<div id="easyDialogInfo"><div class="maskstyle" id="overlay"></div>';
        strhtml += '<div class="easyDialogBox" id="easyDialogBox" style="left: 53%;"><div class="PopDiaWrap">';
        strhtml += '<div class="ct-diabox"><div class="ct-diatit">' + langu.用户登录 + '</div><a href="javascript:;" class="PopDiaClose" onclick="$(\'#easyDialogInfo\').remove()"></a>';
        strhtml += '<div class="ct-diabd"><div id="errorMsg"></div>';
        strhtml += '<div class="ct-diaitem clearfix"><span class="paratit">' + langu.用户名 + '：</span>';
        strhtml += '<div class="paraitem"><input type="text" class="dia-input dia-username" maxlength="30" id="txtUserName" onkeydown="replaceclass(this)"/></div></div>';
        strhtml += '<div class="ct-diaitem clearfix"><span class="paratit">' + langu.密码ToeasyDialog + '：</span>';
        strhtml += '<div class="paraitem"><input type="password" class="dia-input dia-pwd" maxlength="30" id="txtPwd" onkeydown="replaceclass(this)"/></div></div>';
        strhtml += '<div class="ct-diaitem clearfix"><span class="paratit">' + langu.验证码 + '：</span>';
        strhtml += '<div class="paraitem"><input type="text" class="dia-input dia-yzminput" maxlength="4" id="txtCode" onkeydown="replaceclass(this)"/>';
        strhtml += '<div class="dia-yzmpic"></div>';
        strhtml += '<span class="dia-gbyzm"></span></div></div>';
        strhtml += '<div class="ct-diaitem clearfix"><span class="paratit">&nbsp;</span>';
        strhtml += '<div class="paraitem" id="subInfo"><input type="submit" class="dia-loginbtn" value="' + langu.登录2 + '" /></div></div>';
        strhtml += '<div class="ct-diaitem clearfix" style="padding:0"><span class="paratit">&nbsp;</span>';
        strhtml += '<div class="paraitem ct-dialoginHand clearfix"><a href="/Member/Register.aspx" class="fl">' + langu.注册 + '</a> <a href="/Member/ForgotPwd.aspx" class="fr">' + langu.忘记密码 + '？</a></div>';
        strhtml += '</div></div></div></div></div></div>';

        $("#easyDialogInfo").remove();
        $("body").append(strhtml);

        // IE6模拟fixed
        var isIE = ! -[1, ], isIE6 = isIE && /msie 6/.test(navigator.userAgent.toLowerCase()); // 判断IE6
        if (isIE6) {
            document.body.style.height = '100%';
            document.getElementById("overlay").style.position = 'absolute';
            document.getElementById("overlay").style.setExpression('top', 'fuckIE6=document.documentElement.scrollTop+"px"');
            document.getElementById("easyDialogBox").style.setExpression('top', 'fuckIE6=document.documentElement.scrollTop+document.documentElement.clientHeight/2-100+"px"');
        } else {
            document.getElementById("easyDialogBox").style.position = 'fixed';
            document.getElementById("easyDialogBox").style.top = '40%';
        }
        $(".dia-yzmpic").html($('<img style="cursor: pointer;" src="/Ajax/ValidateCode.ashx?r=' + Math.random() + '" alt="' + langu.看不清换一张 + '" />').click(function () {
            $(this).attr("src", '/Ajax/ValidateCode.ashx?r=' + Math.random());
        }));
        $(".dia-gbyzm").html($('<span class="dia-gbyzm">' + langu.看不清换一张 + '</span>').click(function () {
            $(".dia-yzmpic img").attr("src", '/Ajax/ValidateCode.ashx?r=' + Math.random());
        })).removeClass("dia-gbyzm");

        $("#subInfo").html($('<input/>').attr("type", "submit").attr("value", langu.登录2).addClass("dia-loginbtn").click(function () {
            var userName = $("#txtUserName").val();
            var pwd = $("#txtPwd").val();
            var code = $("#txtCode").val();
            if ($.trim(userName) == "" && $.trim(pwd) == "" && $.trim(code) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入用户名密码和验证码);
                $("#txtUserName").addClass("loginerror");
                $("#txtPwd").addClass("loginerror");
                $("#txtCode").addClass("loginerror");
                return false;
            }
            if ($.trim(userName) == "" && $.trim(pwd) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入用户名和密码);
                $("#txtUserName").addClass("loginerror");
                $("#txtPwd").addClass("loginerror");
                return false;
            }
            if ($.trim(userName) == "" && $.trim(code) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入用户名和验证码);
                $("#txtUserName").addClass("loginerror");
                $("#txtCode").addClass("loginerror");
                return false;
            }
            if ($.trim(pwd) == "" && $.trim(code) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入密码和验证码);
                $("#txtPwd").addClass("loginerror");
                $("#txtCode").addClass("loginerror");
                return false;
            }
            if ($.trim(userName) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入用户名);
                $("#txtUserName").addClass("loginerror");
                return false;
            }
            if ($.trim(pwd) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入密码);
                $("#txtPwd").addClass("loginerror");
                return false;
            }
            if ($.trim(code) == "") {
                $("#errorMsg").addClass("ct-diaErrorTip").html(langu.请输入验证码);
                $("#txtCode").addClass("loginerror");
                return false;
            }
            $.post("/Member/Ajax/LoginHandler.ashx", {
                action: "loadLogin", userName: userName, pwd: pwd, code: code,
                product_id: product_id, product_quantity: product_quantity, producttype: producttype, cache: Math.random()
            }, function (rs) {
                if (rs.success)
                    location.href = "/Member/BuyNow.aspx";
                else
                    $("#errorMsg").addClass("ct-diaErrorTip").html(rs.message);
            }, "json");
        }));

        $("#overlay").show();
        $("#easyDialogBox").show();
    }, "json");
}


var replaceclass = function (input) {
    $(input).removeClass("loginerror");
    $(input).removeClass("login-form-error");
};


function AddEnquiry(ProductId) {
    LoadTableInfo(ProductId);
}

function LoadTableInfo(ProductId) {
    $.post("/Member/Ajax/LanguageHandler.ashx", { action: "loadLanguage", langus: "真实姓名;公司名称;联系手机;联系邮箱;期望价格;了解内容;询价提交", cache: Math.random() }, function (langu) {
        var strhtml = '<div id="easyDialogInfo"><div class="maskstyle" id="overlay"></div><div class="easyDialogBox" id="easyDialogBox">';
        strhtml += '<div class="zdyform"><a href="javascript:;" class="PopDiaClose" onclick="$(\'#easyDialogInfo\').remove()"></a><div class="zdyform-parawrap">';
        strhtml += '<table class="zdyform-tbl">';
        strhtml += '<tr><td class="paratit">' + langu.真实姓名 + '：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtUserName" maxlength="20" onkeydown="replaceclass(this)"/></td>';
        strhtml += '<td class="paratit">' + langu.公司名称 + '：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtCompanyName" maxlength="50" onkeydown="replaceclass(this)"/></td></tr>';
        strhtml += '<tr><td class="paratit">' + langu.联系手机 + '：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtPhone" maxlength="11" onkeydown="replaceclass(this)"/></td>';
        strhtml += '<td class="paratit">' + langu.联系邮箱 + '：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtEmail" maxlength="20" onkeydown="replaceclass(this)"/></td></tr>';
        strhtml += '<tr><td class="paratit">' + langu.期望价格 + '：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtExpectedPrice" maxlength="10" onkeyup="validateNumPriceRW(\'txtExpectedPrice\')" onkeydown="replaceclass(this)"/></td>';
        strhtml += '<td class="paratit">QQ：</td>';
        strhtml += '<td><input type="text" class="inputelem" id="txtQQ" maxlength="20" onkeyup="updateInventory(this)" /></td></tr>';
        strhtml += '<tr><td class="paratit" style="vertical-align: top">' + langu.了解内容 + '：</td>';
        strhtml += '<td colspan="3"><textarea class="areaform" id="txtContent" maxlength="500" onkeydown="replaceclass(this)"></textarea></td></tr>';
        strhtml += '<tr><td class="paratit" style="vertical-align: top">&nbsp;</td>';
        strhtml += '<td colspan="3" id="subInfo"><input type="submit" class="zdyform-subtn" value="' + langu.询价提交 + '" /></td></tr>';
        strhtml += '</table></div></div></div></div>';
        $("#easyDialogInfo").remove();
        $("body").append(strhtml);

        // IE6模拟fixed
        var isIE = ! -[1, ], isIE6 = isIE && /msie 6/.test(navigator.userAgent.toLowerCase()); // 判断IE6
        if (isIE6) {
            document.body.style.height = '100%';
            document.getElementById("overlay").style.position = 'absolute';
            document.getElementById("overlay").style.setExpression('top', 'fuckIE6=document.documentElement.scrollTop+"px"');
            document.getElementById("easyDialogBox").style.setExpression('top', 'fuckIE6=document.documentElement.scrollTop+document.documentElement.clientHeight/2-100+"px"');
        } else {
            document.getElementById("easyDialogBox").style.position = 'fixed';
            document.getElementById("easyDialogBox").style.top = '30%';
        }

        $("#overlay").show();
        $("#easyDialogBox").show();

        $("#subInfo").html($('<input/>').attr("type", "submit").attr("value", langu.询价提交).addClass("zdyform-subtn").click(function () {
            var UserName = document.getElementById("txtUserName");
            var CompanyName = document.getElementById("txtCompanyName");
            var Phone = document.getElementById("txtPhone");
            var Email = document.getElementById("txtEmail");
            var ExpectedPrice = document.getElementById("txtExpectedPrice");
            var QQ = $("#txtQQ").val();
            var Contents = document.getElementById("txtContent");
            if (!checkEnquiry(UserName, "姓名不能为空哦！") || !checkEnquiry(CompanyName, "公司名称不能为空哦！")
                        || !checkEnquiry(Phone, "联系手机不能为空哦！") || !checkEnquiry(Email, "联系邮箱不能为空哦！")
                        || !checkEnquiry(ExpectedPrice, "期望价格不能为空哦！") || !checkEnquiry(Contents, "了解内容不能为空哦！"))
                return false;

            //        if (!/^[\u4E00-\u9FA5]{2,6}(?:·[\u4E00-\u9FA5]{2,6})*$/.test(UserName.value)) {
            //            $("#txtUserName").addClass("loginerror");
            //            //alert("您输入的姓名不正确，请重新输入");
            //            $("#txtUserName").focus();
            //            return false;
            //        }

            //        if (!/^[\u4E00-\u9FA5]{5,50}(?:·[\u4E00-\u9FA5]{5,50})*$/.test(CompanyName.value)) {
            //            $("#txtCompanyName").addClass("loginerror");
            //            // alert("您输入的公司名称不正确，请重新输入");
            //            $("#txtCompanyName").focus();
            //            return false;
            //        }

            if (!/^(13|15|18|14)[0-9]{9}$/.test(Phone.value)) {
                $("#txtPhone").addClass("loginerror");
                //artbox.alert("您输入的联系手机不正确，请重新输入");
                $("#txtPhone").focus();
                return false;
            }

            if (!/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(Email.value)) {
                $("#txtEmail").addClass("loginerror");
                //alert("您输入的联系邮箱不正确，请重新输入");
                $("#txtEmail").focus();
                return false;
            }

            $.post("/Member/Ajax/EnquiryHandler.ashx", {
                action: "addEnquiry", ProductId: ProductId, UserName: UserName.value, CompanyName: CompanyName.value
                        , Phone: Phone.value, Email: Email.value, ExpectedPrice: ExpectedPrice.value, QQ: QQ, Contents: Contents.value, cache: Math.random()
            }, function (data) {
                if (data.success)
                    $("#easyDialogInfo").remove();
                else
                    alert(data.message);
            }, "json");
        }));

    }, "json");
}


// 表单验证
var checkEnquiry = function (input, msg) {
    if (input.value == '' || input.value == 0) {
        input.className = "loginerror " + input.className;
        //alert(msg);
        input.focus();
        return false;
    } else {
        return true;
    };
};

//不让输入除数字以外字符（第一个字符不能为0） invObj 控件ID
function updateInventory(invObj) {
    var inventory = invObj.value;
    if ((/(^0+)|[^\d]/g).test(inventory)) {
        inventory = inventory.replace(/(^0+)|[^\d]/g, "");
        invObj.value = inventory;
    }
}

//价格区间不让输入除数字以外字符
function validateNumPriceRW(rangePriceId) {
    var priceObj = document.getElementById(rangePriceId);
    var pvalue = priceObj.value;
    if ((/[^0-9\.]*/g).test(pvalue)) {
        pvalue = pvalue.replace(/[^0-9\.]*/g, "");
        priceObj.value = pvalue;
    }
    var fpValue = Number(pvalue);
    if (isNaN(fpValue)) {
        priceObj.value = "";
        return;
    }
    if (fpValue == 0) {
        if (pvalue.length > 1 && !(/\./.test(pvalue))) {
            priceObj.value = "0";
        }
        return;
    }
    if (/\./.test(pvalue)) {
        var integer = pvalue.split(".")[0];
        var decimal = pvalue.split(".")[1];
        pvalue = Number(integer) + "." + decimal;
    } else {
        pvalue = Number(pvalue);
    }
    priceObj.value = pvalue;
}