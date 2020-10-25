<div id="content_banner" class="widthAuto">
    <div class="swiper-container swiper-container1">
      	<div class="swiper-wrapper">
	      	<?php $banners = $categories[0];?>
			{% for banner in banners.list %}
		        <div class="swiper-slide"><a href="{{ banner.url }}" alt="{{ banner.title }}"> <img src="{{ banner.img_dir }}"> </a></div>
			{% endfor %}
      	</div>
      	<!-- 分页器 -->
	    <div class="pagination"></div>
	  	<!-- 翻页按钮 -->
	    <a class="arrow-left pure-hidden-phone arrow" id="btn1" href="#"></a>
	    <a class="arrow-right pure-hidden-phone arrow" id="btn2" href="#"></a>
    </div>
</div>
<div id="content_body">
    <div id="container" class="widthAuto">
        <div class="container_main">
        	<div class="pure-g-r">
        		<div class="hot_header" style=" margin-right: 3em;">
					<div class="hot_title left">
						<span class="hot_title_big left">热门新闻</span>
						<span class="hot_title_small left">HOT&nbsp&nbspNEWS</span>
					</div>
					<div class="hot_more right">
						<a href="">></a>
					</div>
				</div>
				<div class="hot_container">
					<ul>
					{% for hot in index_hot %}
						<li>
							<a class="article_data" href="article/{{ hot.id }}">
								<input type="hidden" class="time" value="{{ hot.created_at }}">
								<div class="mon"></div>
								<div class="date"></div>
							</a>
							<a class="article_content" href="article/{{ hot.id }}">
								<span class="title">
								{{ hot.title }}
								</span>
								<p>
								{{ hot.description }}word-spacing 
								</p>
							</a>
						</li>
					{% endfor %}	
					</ul>
				</div>
			{% for one in categories %}
	    		{% if one.basic.module == 'article' %}
		    		<div class="pure-u-1-3">
						<div class="l-box">
							<div class="model_header">
								<h3 class="model_name left">
									<span class="model_text">{{ one.basic.name }}</span>
								</h3>
								<div class="model_more right">

									<a href="../category/{{ one.basic.id }}">></a>
									{# {{ link_to("category/{{ one.basic.id }}", "") }} #}
								</div>
							</div>
							<div class="model_body">
								<?php $articleOne = $one->list[0];?>
								{% if articleOne.img_dir is defined %}
								<div class="model_list_picture_word pure-hidden-tablet pure-hidden-phone">
									<div class="left picture">
										<img src="{{ articleOne.img_dir }}">
									</div>
									<div class="left word">
										<p class="word_title">
										<?= $this->tag->linkTo("article/".$articleOne->id, $articleOne->title) ?></p>
										<p class="word_text">
										<?= $this->tag->linkTo("article/".$articleOne->id, $articleOne->description) ?></p>
										{#<a href = "article/{{ articleOne.id }}">
										<p class="word_title">{{ articleOne.title }}</p>
										</a>
										<a href = "article/{{ articleOne.id }}">
										<p class="word_text">{{ articleOne.description }}</p>
										</a>#}
									</div>
								</div>
								{% endif %}
								<div class="clear"></div>
								<div class="model_list_word">
									<ul>
										<?php $articleOne = $one->list[0];?>
										<li class="pure-hidden-desktop">
											<span class="article_info">
												<span class="flag">></span>
											<?= $this->tag->linkTo("article/".$articleOne->id, $articleOne->title) ?>
											</span>
											<span class="article_time">{{ articleOne.created_at  }}</span>
										</li>	    		
										{% for article in one.list %}
										{% if !loop.first %}
										<li>
											<span class="article_info">
												<span class="flag">></span>
											<?= $this->tag->linkTo("article/".$article->id, $article->title) ?>
											</span>
											<span class="article_time">{{ article.created_at  }}</span>
										</li>
										{% endif %}
										{% endfor %}
									</ul>
								</div>
							</div>
						</div>
					</div>
	    		{% elseif one.basic.module == 'album' %}
	    			<div class="pure-u-1-3 ">
						<div class="l-box">
							<div class="model_header">
								<h3 class="model_name left">
									<span class="model_text">{{ one.basic.name }}</span>
								</h3>
								<div class="model_more right">
									{#<?php $this->tag->linkTo("category/".$one->basic->id, "123") ?>#}
									<a href="../category/{{ one.basic.id }}">></a>
								</div>
							</div>
							<div class="model_body">
								<div class="swiper-container swiper-container2">
								    <div class="swiper-wrapper">
									    {% for album in one.list %}
									        <div class="swiper-slide">
									        	<a href="album/{{ album.id }}">
									        		<img src="{{ album.img_dir }}">
									        	</a>
									        </div>
									    {% endfor %}
								    </div>
								    <div class="pagination"></div>
								</div>
							</div>
						</div>
					</div>
	    		{% endif %}
	    	{% endfor %}
	    	</div>
        </div>
        <div id="connection" class="container_main">
            <div class="pure-g-r">
				<div class="pure-u-1-4">
					<h3 class="highlight">在线留言</h3>
					<form>
						<textarea id="message" required="required" placeholder="请在此写下您的留言。。。"></textarea>
						<input id="name" type="text" required="required" placeholder="姓名">
						<input id="email" type="email" required="required" placeholder="邮箱">
						<div class="clear"></div>
						<input id="cancel" type="reset" class="left" value="取消">
						<input id="submit" type="button" class="right" value="提交">
					</form>
				</div>
				<div class="pure-u-1-2">
					<div class="connection_info">
						<h3 class="highlight">联系我们</h3>
						<p>
							电话: {{ setting.phone }}<br>
							传真：{{ setting.fax }} &nbsp&nbsp
							手机：{{ setting.mobile }}<br>
							邮箱: {{ setting.email }} &nbsp&nbsp邮编：{{ setting.post_code }}<br>
							地址：{{ setting.address }}<br>
							
						</p>
						
							<div style="width:400px;height:135px;border:#ccc solid 1px;font-size:12px；margin-top:10px;" id="map"></div>
						
					</div>
				</div>
		        <div class="pure-u-1-4 master_word pure-hidden-phone">
					<p>
						<span class="master_word_title">校长寄语</span><br />
						<span class="picture"></span>
						{{setting.hope}}
					</p>
				</div>
			</div>
        </div>
    </div>
</div>
<script type="text/javascript">
window.onload = function(){  
	var time = $(".time");
	var mon=$(".mon");
	var date=$(".date");
    for (var i = 0; i < time.length; i++) {
        var index1=time[i].value.indexOf("-"); 
        var index2=time[i].value.lastIndexOf("-"); 
        var cha=parseInt(index2)-(parseInt(index1)+1); 
        var month=time[i].value.substr((parseInt(index1)+1),cha); 
        var kg=time[i].value.indexOf(" "); 
        cha=parseInt(kg)-parseInt(index2); 
        var day=time[i].value.substr(parseInt(index2)+1,cha); 
        date[i].innerHTML=day;
        if (month=="01") {mon[i].innerHTML="JAN";};
        if (month=="02") {mon[i].innerHTML="FEB";};
        if (month=="03") {mon[i].innerHTML="MAR";};
        if (month=="04") {mon[i].innerHTML="APR";};
        if (month=="05") {mon[i].innerHTML="MAY";};
        if (month=="06") {mon[i].innerHTML="JUN";};
        if (month=="07") {mon[i].innerHTML="JUL";};
        if (month=="08") {mon[i].innerHTML="AUG";};
        if (month=="09") {mon[i].innerHTML="SEP";};
        if (month=="10") {mon[i].innerHTML="OCT";};
        if (month=="11") {mon[i].innerHTML="NOV";};
        if (month=="12") {mon[i].innerHTML="DEC";};
    };
}
</script>