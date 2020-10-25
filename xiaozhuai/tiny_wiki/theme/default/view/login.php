<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Enter the password</title>
    <link  href="<?php echo $CONFIG["site_root"]; ?>theme/default/css/style-login.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $CONFIG["site_root"]; ?>theme/default/js/jquery-1.11.2.min.js" type="text/javascript"></script>
</head>
<body>
<form id="book-login" method="post">
    <p class="err-msg" style="display: none;color: #f0f0f0;text-align: center;"><?php echo $ERR_MSG; ?></p>
    <input type="password" name="password" class="placeholder" placeholder="Enter the password">
    <input type="submit" value="GO!">
</form>
<script>
    $(function () {
        if($(".err-msg").html()!=""){
            $(".err-msg").css("display", "block");
        }
    });
</script>
</body>
</html>