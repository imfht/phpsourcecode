var FormValidation = function () {

    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form1 = $('#add_user');
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
                username: {
                    minlength: 5,
                    maxlength:18,
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 5,
                    maxlength:18
                },
                confirm_password: {
                    required: true,
                    equalTo:"#password"
                },
               mobile:{
                   regex: "^1[34578][0-9]{9}$"
                }
            },
            messages: { // custom messages for radio buttons and checkboxes
                username: {
                    required: "用户名不能为空！",
                    minlength:"不能少于{0}个字符",
                    maxlength:"不能多于{0}个字符"
                },
                password: {
                    required: "密码不能为空！",
                    minlength: "不能少于{0}个字符",
                    maxlength:"不能多于{0}个字符"
                },
                confirm_password: {
                    required: "确认密码不能为空！",
                    equalTo: "两次密码不一致！"
                },
                email:{
                    required:"邮箱不能为空！",
                    email:"邮箱格式错误！"
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
                    url : "http://192.168.1.102/tp3.2/index.php/Admin/User/add",
                    type : "post",
                    //dataType : "json",
                    data:  {username:$("[name=username]").val(), password:$.md5($("[name=password]").val()),confirm_password:$.md5($("[name=confirm_password]").val()),email:$("[name=email]").val(),status:$("[name=status]").val(),mobile:$("[name=mobile]").val()},
                    success : function(result) {
                        bootbox.alert(result);
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
