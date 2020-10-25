var FormValidation = function () {

    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form1 = $('#add_menu');
        var error1 = $('.alert-danger', form1);
        var success1 = $('.alert-success', form1);
        jQuery.validator.addMethod("regex", //addMethod第1个参数:方法名称
            function(value, element, params) { //addMethod第2个参数:验证方法，参数（被验证元素的值，被验证元素，参数）
                if(!value){
                    return true;
                }else{
                    var exp = new RegExp(params); //实例化正则对象，参数为传入的正则表达式
                    return exp.test(value); //测试是否匹配
                }
            },
            "格式错误"); //addMethod第3个参数:默认错误信息
        form1.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                title: {
                    required: true
                },
                sort: {
                    required: true,
                    digits:true
                },
                name: {
                    required: true
                },
                pid: {
                    required: true,
                    digits:true
                }
            },
            messages: { // custom messages for radio buttons and checkboxes
                title: {
                    required: "菜单名称不能为空！"
                },
                sort: {
                    required: "排序不能为空！",
                    digits: "请输入整数"
                },
                name: {
                    required: "链接不能为空！"
                },
                pid:{
                    required:"上级菜单不能为空！",
                    digits: "请输入整数"
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                success1.hide();
                error1.show();
                App.scrollTo(error1, -200);
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error'); // set success class to the control group
            },

            submitHandler: function (form) {
                success1.show();
                error1.hide();
                $.ajax({
                    url : "http://192.168.1.100/tp3.2/index.php/Admin/Menu/add",
                    type : "post",
                    //dataType : "json",
                    data:  {title:$("[name=title]").val(), sort:$("[name=sort]").val(),url:$("[name=url]").val(),pid:$("[name=pid]").val(),status:$("[name=status]").val(),condition:$("[name=condition]").val()},
                    success : function(data) {
                        var data = eval('(' + data + ')');
                        bootbox.alert(data.info);
                    }
                });
            }
        });

    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation1();

        }

    };

}();
