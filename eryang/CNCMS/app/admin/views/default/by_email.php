<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('title'); ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=8,IE=9,chrome=1">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="keywords" content="菜鸟CMS"/>
    <meta name="description" content="菜鸟CMS">
    <meta name="author" content="二阳">

    <style type="text/css">
        body {
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }
    </style>
    <!--样式-->
    <link href="/assets/admin/default/css/bootstrap-cerulean.css" rel="stylesheet">
    <link href="/assets/admin/default/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/assets/admin/default/css/charisma-app.css" rel="stylesheet">
    <!--图标-->
    <link rel="shortcut icon" href="/assets/admin/default/img/favicon.ico">
</head>
<body>
<div class="row-fluid">
    <div class="well span5 center login-box">
        <?php

        if ($this -> session -> flashdata('message')) {
            $message = $this -> session -> flashdata('message');
        }

        if ($this -> session -> flashdata('error')) {
            $error = $this -> session -> flashdata('error');
        }
        ?>

        <fieldset>
            <!--标题开始-->
            <div class="row-fluid">
                <div class="span12 center login-header">
                    <h2><?php echo $title; ?></h2>
                </div>
            </div><!--/.标题结束-->

             <div class="span12">
                <div class="content">
                    <p><b><?php echo $email_hint;?></b></p>
                </div>
            </div>

            <div class="center span4">
                <a href="<?php echo $email_url;?>" target="_blank" class="btn btn-large btn-primary">
                    <?php echo lang('receive_email_hint'); ?></a>
            </div>
            <br/>
            <hr/>

            <div class="container span12 ">
                <div style="text-align:left;">
                    <p class="text-left"><?php echo lang('hint');?></p>
                    <br/>
                    <p class="text-left"><?php echo lang('hint_one_row');?></p>
                    <br/>
                    <p class="text-left"><?php echo lang('hint_two_row');?><a href="<?php echo site_url($this -> config -> item('admin_folder').$email_url_a_hint); ?>"><?php echo $email_url_hint;?></a>。
                    </p>
                    <br/>
                    <p class="text-left"><?php echo $hint_three_row;?></p>
                    <br/>
                </div>
            </div>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success" id="success_alert">
                    <a class="close" data-dismiss="alert">×</a>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert" id="error_alert">×</a>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="alert alert-error" id="alert_error" style="display: none;">
            </div>
        </fieldset>
    </div>
</div>
<hr>
<!--底部开始-->
<footer>
    <p class="pull-left"><?php echo lang('write_date'); ?>：<?php echo date('Y') ?></p>
    <p class="pull-right"><?php echo lang('writer'); ?>：<a href="http://weibo.com/513778937?topnav=1&wvr=5" target="_blank">二阳</a></p>
</footer>
<!--/.底部结束-->
</body>
</html>
