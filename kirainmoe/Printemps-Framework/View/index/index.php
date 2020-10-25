<!DOCTYPE HTML PUBLIC>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php $this->data->import(array('indexController-index.css','jquery.min.js')); ?>
	<title>Printemps Framework</title>
</head>
<body>
	<div class="printemps-background"><div class="printemps-img"></div></div>
	<div class="printemps-body">
		<h1 class="printemps" style="display: none;">Printemps Framework</h1>
		<p class="printemps-description" style="display: none;">
			A light, beautiful and lovely PHP Framework just like the spring.
		</p>
		<p class="printemps-menu">
			<a href="#">Document</a>
			<a href="https://github.com/kirainmoe/Printemps-Framework">Star us on Github</a>
			<a href="mailto:minami@kotori.ovh">Contact</a>
		</p>
		<h3 class="printemps-coming-soon" style="display: none;">Meet you next March, Printemps.</h3>
		<div class="printemps-copyright" style="display: none;">
			<p>&copy;2015 Printemps DevTeam. All rights reserved.</p>
		</div>
	</div>
	<script type="text/javascript">
		var printemps = $(".printemps-body").children();
		function fadeInElement(item){
			$(printemps[item]).fadeIn(500,function(){
				if(item<printemps.length-1){
					fadeInElement(item+1);
				}
				else{}
			});
		}
		$(document).ready(function(){
			fadeInElement(0);
		});
	</script>
</body>
</html>