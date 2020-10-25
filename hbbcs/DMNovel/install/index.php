<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <title>安装程序</title>

    <link rel="stylesheet" href="../theme/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="../theme/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" href="./install.css" type="text/css"/>
    <link href="../theme/css/smart_wizard.css" rel="stylesheet" type="text/css"/>
    <link href="../theme/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css"/>

</head>
<body>
<div class="header">
    <h1>DMNovel 安装程序</h1>
</div>

<div class="main">
    <div id="smartwizard">
        <ul>
            <li>
                <a href="#step-1">
                    Step 1<br/>
                    <small>检测目录权限</small>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    Step 2<br/>
                    <small>设置数据库</small>
                </a>
            </li>
            <li>
                <a href="#step-3">
                    Step 3<br/>
                    <small>导入数据库</small>
                </a>
            </li>
            <li>
                <a href="#step-4">
                    Step 4<br/>
                    <small>设置管理员</small>
                </a>
            </li>
            <li>
                <a href="#step-5">
                    Step 5<br/>
                    <small>完成安装</small>
                </a>
            </li>
        </ul>

        <div>
            <div id="step-1" class="">

            </div>
            <div id="step-2" class="">

            </div>
            <div id="step-3" class="">

            </div>
            <div id="step-4" class="">

            </div>
            <div id="step-5" class="">

            </div>
        </div>
    </div>
</div>

<script src="../theme/js/jquery.min.js"></script>
<script src="../theme/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../theme/js/jquery.smartWizard.min.js"></script>
<script type="text/javascript" src="../theme/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'arrows',
            transitionEffect: 'fade',
            useURLhash: false,//根据url哈希选择步骤
            showStepURLhash: false,
            lang: {  // Language variables for button
                next: '下一步',
                previous: '上一步'
            },
            toolbarSettings: {
                toolbarPosition: 'bottom',
                showPreviousButton: false
            },
            anchorSettings: {
                anchorClickable: false
            },
            contentURL: './install.php'
        });

        $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
            if (stepNumber == 0) {
                var siteurl = $('#siteurl').val();
                return postStep('setconfig',{'siteurl':siteurl});
            } else if (stepNumber==1) {
                return postStep('setdatabase',{
                    'db_host':$('#db_host').val(),
                    'db_name':$('#db_name').val(),
                    'db_user':$('#db_user').val(),
                    'db_pass':$('#db_pass').val(),
                    'db_cover':$('#db_cover').val()
                })
            } else if (stepNumber == 3) {
                return postStep('setadmin',{
                    'user_name':$('#user_name').val(),
                    'password':$('#password').val(),
                    're_password':$('#re_password').val()
                });
            }
        });

        function postStep(step,data) {
            var re=true;
            $.ajax({
                url: './install.php?step='+step,
                data: data,
                type: 'post',
                async: false,
                success: function (rdata) {
                    if (rdata) {
                        showMessage(rdata);
                        re = false;
                    }
                }
            });
            return re;
        }

        function showMessage(message,type) {
            type = type?type:'danger';
            $.notify({
                // options
                message: message
            },{
                // settings
                type: type,
                placement: {
                    from: "top",
                    align: "center"
                },
                delay: 5000,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
            });
        }


    });


</script>

</body>
</html>