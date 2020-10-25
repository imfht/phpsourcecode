<div id="content_body">
	<div class="widthAuto">
	    <div class="breadcrumb">
	    	您的位置：
	    	<a href="../">首页</a>&nbsp»
	    	{% if parent is defined %}
	    		{% for one in parent %}
	    			<span>{{one.name}}</span>&nbsp»
		    	{% endfor %}
	    	{% endif %}
	    	<span style="color:#009382">{{ category.name }}</span>
	    </div>
	</div>
    <div id="container" class="widthAuto">
        <div class="container_main">
            <div class="pure-g-r">
				<div class="pure-u-2-3 album_cont">
					<div class="l-box">
						<h1 class="article_title">
						{{ result.title }}
						</h1>
						<div class="swiper-container">
				    		<div class="">
								{% for one in pictures %}
									<div class="album_picture">
										<img src="{{ one.dir }}">
									</div>
								{% endfor %}
							</div>
						</div>
					</div>
				</div>
				<div class="pure-u-1-3">
					<div class="l-box brother">
						<div class="model_header">
							<h3 class="model_name left">
								<span class="model_text">相关分类</span>
							</h3>
						</div>
						<div class="pure-g-r">
							{% if brother is defined %}
								{% for one in brother %}
									<div class="pure-u-1-2 ">
										<ul style="padding:0;margin:0.5em 0;">
											<li>
												<?= $this->tag->linkTo("category/".$one->id, $one->name) ?>
											<li>
										</ul>
									</div>
								{% endfor %}
							{% endif %}
						</div>
					</div>
					<div class="l-box">
						<div class="model_header">
							<h3 class="model_name left">
								<span class="model_text">本类热门</span>
							</h3>
						</div>
						<div class="model_body">
							<div class="model_list_word">
								<ul>	    		
									{% for article in hotResult %}
									<li>
										<span class="article_info">
											<?= $this->tag->linkTo("article/".$article->id, $article->title) ?>
										</span>
										<span class="article_time">{{ article.created_at  }}</span>
									</li>
									{% endfor %}
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>

