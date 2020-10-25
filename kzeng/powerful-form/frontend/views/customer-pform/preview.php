<!DOCTYPE html>
<html>
<head>
<?php
	use yii\helpers\Html;
?>
<title>页面预览</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css" media="screen">
    <!--
		/* HTML5 display-role reset for older browsers */
		article, aside, details, figcaption, figure,
		footer, header, hgroup, menu, nav, section {
			display: block;
		}
        * {
            margin: 0;
            padding: 0;
        }

        html {
     		overflow-y: -moz-scrollbars-vertical;
     		overflow-y: scroll;
        	}

		a {
			color:inherit;
			}

		a:hover {
			color:black;
			}

        body {
            padding: 0px;
			margin: 0px;
			background-color:#d9d9d9;
			font-family: Arial, sans-serif;
			font-size:0.75em;
			color:#404042;
			min-width:1200px
        }

        header h1 {
        	font-size: 20px;
			line-height: 23px;
			font-weight: bold;
			display: inline;
			width: 190px;
			height: 64px;
			float: left;
			background: url(/uploads/arrow-right.png) right no-repeat;
			padding-right: 20px;
        }

        header {
        	width: 1100px;
			margin: auto;
			margin-top: 15px;
			height: 60px;
        }

        h1 a {
        	font-size: 0.5em;
			font-weight: normal;
			padding-left: 12px;
        }

		/* menu   */
		#screen-options {
			text-align: center;
			height: 60px;
			width: 560px;
			background: url(/uploads/arrow-right.png) right no-repeat;
			float: left;
			margin-left: 65px;
			}

		#screen-options li {
			text-indent:-9999px;
			display: block;
			height: 80px;
			width: 90px;
			padding: 5px;
			float: left;

			opacity: 0.4;

			-webkit-transition: all 0.15s ease-in-out 0s;
			-moz-transition: all 0.15s ease-in-out 0s;
			-o-transition: all 0.15s ease-in-out 0s;
			transition: all 0.15s ease-in-out 0s;
			}

		#screen-options li:hover {
			opacity: 1;
			}

		#screen-options li.active {
			opacity: 1;
				}



		#iphone {
			background: url(/uploads/device-sprite1.png) top no-repeat ;
			background-position: 0 12px;
			}
		#iphone-l {
			background: url(/uploads/device-sprite1.png) no-repeat 0 -93px;
			}
		#ipad {
			background: url(/uploads/device-sprite1.png) no-repeat 0 -197px;
			}
		#ipad-l {
			background: url(/uploads/device-sprite1.png) no-repeat 0 -306px;
			}
		#desktop {
			background: url(/uploads/device-sprite1.png) no-repeat 0 -422px;

			}

		.url-select {
			float:left;
			margin: 11px 0 0 32px;
		}

		input {
			display: inline-block;
			padding: 12px;
			color: gray;
			border: 1px solid #CCC;
			border-radius: 3px;
			}

		#getURL {
			width:140px;
			}

		#button {
			background-color:#404042;
			color: #ddd;

			-webkit-transition: all 0.15s ease-in-out 0s;
			-moz-transition: all 0.15s ease-in-out 0s;
			-o-transition: all 0.15s ease-in-out 0s;
			transition: all 0.15s ease-in-out 0s;
			}

		#button:hover {
			background-color:black;
			color:white;
			}

		#viewport {
		/* initial width only   */
			margin: auto ;
			background-color:#CCCCCC;
			
			margin-top: 48px;
			
			/* trying different values to fix iOS issues with iFrame busting loose and wrecking the joint.   */
			overflow:hidden;
			overflow: scroll;
			-webkit-overflow-scrolling: touch;

			-webkit-transition: all 0.4s ease-in-out 0s;
			-moz-transition: all 0.4s ease-in-out 0s;
			-o-transition: all 0.4s ease-in-out 0s;
			transition: all 0.4s ease-in-out 0s;
			}

		iframe {
			background-color: white;
			position:absolute;
			margin:auto;
			border:none;
			
			}


		/* ////// DEVICES */

		/* /// Details   */
		#device-detail {
			position: relative;
			z-index: 1000;
			text-indent:-9999px;
			display: block;
			margin: auto;

			-webkit-transition: all 0.15s ease-in-out 0s;
			-moz-transition: all 0.15s ease-in-out 0s;
			-o-transition: all 0.15s ease-in-out 0s;
			transition: all 0.15s ease-in-out 0s;
			}

		.iphone-detail {
			top: -53px;
			width: 40px;
			height: 40px;
			border-radius: 20px;
			background-color:#CCCCCC;
			}

		.iphone-l-detail {
			top: -210px;
			left: -270px;
			width: 40px;
			height: 40px;
			border-radius: 20px;
			background-color:#CCCCCC;
			}

		.ipad-detail {
			top: -70px;
			width: 50px;
			height: 50px;
			border-radius: 25px;
			background-color:#CCCCCC;
			}

		.ipad-l-detail {
			top: -512px;
			left: 555px;
			width: 50px;
			height: 50px;
			border-radius: 25px;
			background-color:#CCCCCC;
			}

		.desktop-detail {
			border-radius: 25px;
			background:none;
			border-bottom: 100px solid #404042;
			border-left: 50px solid transparent;
			border-right: 50px solid transparent;
			height: 0;
			width: 100px;
			top: 20px;
			}

		/* /// Devices sizes   */

		/* iphone 320 x 480 */
		#viewport.iphone {
			border-top: 65px solid;
			border-right: 35px solid;
			border-bottom: 65px solid;
			border-left: 35px solid;
			border-radius: 20px;
			}
		.iphone, .iphone iframe {
			width:320px;
			height:480px;
			}
		.iphone-l {
			border-top: 35px solid;
			border-right: 65px solid;
			border-bottom: 35px solid;
			border-left: 65px solid;
			border-radius: 20px;
			}
		.iphone-l, .iphone-l iframe {
			width:480px;
			height:320px;
			}

		/* ipad 1024 x 768 */
		.ipad {
			border: 90px solid;
			border-radius: 20px;
			}
		.ipad, .ipad iframe {
			width:768px;
			height:1024px;
			}
		.ipad-l {
			border: 90px solid;
			border-radius: 20px;
			}
		.ipad-l, .ipad-l iframe {
			width:1024px;
			height:768px;
			}

		/* desktop 1200 x 800 */
		.desktop {
			border: 20px solid;
			border-radius: 10px;
			}
		.desktop, .desktop iframe {
			width:1200px;
			height:800px;
			}
    -->
</style>

<script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>

<script type="text/javascript">
$(function() {
	var text = "<?= $text ?>";
	//alert(text);

	$('#iphone').click(function() {
		$("#screen-options li").removeClass("active");
		$(this).addClass("active");
		$('#viewport').attr('class', 'iphone');
		$('#device-detail').attr('class', 'iphone-detail');
	});

	$('#iphone-l').click(function() {
		$("#screen-options li").removeClass("active");
		$(this).addClass("active");
		$('#viewport').attr('class', 'iphone-l');
		$('#device-detail').attr('class', 'iphone-l-detail');
	});

	$('#ipad').click(function() {
		$("#screen-options li").removeClass("active");
		$(this).addClass("active");
		$('#viewport').attr('class', 'ipad');
		$('#device-detail').attr('class', 'ipad-detail');
	});

	$('#ipad-l').click(function() {
		$("#screen-options li").removeClass("active");
		$(this).addClass("active");
		$('#viewport').attr('class', 'ipad-l');
		$('#device-detail').attr('class', 'ipad-l-detail');
	});

	$('#desktop').click(function() {
		$("#screen-options li").removeClass("active");
		$(this).addClass("active");
		$('#viewport').attr('class', 'desktop');
		$('#device-detail').attr('class', 'desktop-detail');
	});

	///get url from form field
	function updateframe() {
      var url = $("#getURL").val();
	  //url = "http://pf.mitoto.cn/customer-pform/create?pform_uid=594cd9feac29c";
      //alert(url);

      /// check for http
      if ($("#getURL").val().match(/^http/)) {
            $('#displayframe').attr('src', url);
        }
        else {
        	validURL = 'http://' + url;
        	//alert(validURL);
        	$('#displayframe').attr('src', validURL);
        	return false;
        }

      }

    /// form actions
    $("#button").click(function() {
  		updateframe();
	});

	$('#getURL').keypress(function(e){
      if(e.which == 13){
       updateframe();
       }
      });

    $(function() {
    $('input[type=text]').focus(function() {
      $(this).val('')
      });

 });

    //// Check for url in url
    var givenURL = (RegExp('url=' + '(.+?)(&|$)').exec(location.search)||[,null])[1];
    if (givenURL == null){
    	
		var randomURL = '';

		//var randomhttpURL = (base + randomURL);
		var randomhttpURL = text;
		
    	$('#displayframe').attr('src', randomhttpURL);
         $('#getURL').attr('value', randomURL);
    }else{
      $('#getURL').attr('value',givenURL );
      $('#displayframe').attr('src',givenURL);
    }


});
</script>
</head>

<body>
	<header>

		<h1>
			<?php 
				$backlink = Yii::$app->request->hostInfo . "/admin/pform/index";
			?>
			<a style="font-size:22px" href="<?= $backlink?>" >< 返回超级表单</a>
		</h1>
        <ul id="screen-options">
            <li id="iphone" class="active">iPhone</li>
            <li id="iphone-l" >iPhone landscape</li>
            <li id="ipad" >iPad</li>
            <li id="ipad-l" >iPad landscape</li>
            <li id="desktop" >Desktop</li>
        </ul>
		<!--
        /* <div class="url-select">
            <input type="text" name="load" id="getURL"  placeholder="" />
            <input type="submit" value="预览" id="button" />
        </div> */
		-->
	</header>
	<div id="viewport" class="iphone">
		<iframe id="displayframe" name="displayframe" height="480" width="320" src=""></iframe>

	</div>
	<div id="device-detail" class="iphone-detail">device detail</div>
	

</body>
</html>
