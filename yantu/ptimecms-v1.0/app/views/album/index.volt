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
        	<div class="pure-g-r" id="page_more_{{category.id}}">
                {% if result is defined %}
                    {% for one in result %}
                        <div class="pure-u-1-3"> 
                            <div class="album album_box">
                                <a href="../album/{{ one.id }}"><img src="{{ one.img_dir }}"></a>
                                <div class="album_info">
                                    <h4>{{ one.title }}</h4>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                {% endif %}
            </div>
			{% if result |length == 15%}
			<div class="page_more" id="{{category.id}}" onclick = "getMore({{ category.id }},'album',1)">
				下一页&nbsp{{ image("img/down_arrow.png") }}
			</div>
            <div class="img_loading">
                {{ image("img/loading.gif")}}
            </div>
			{% endif %}
        </div>
    </div>
</div>