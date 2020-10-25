<div id="content_body">
    <div class="topic_header widthAuto">
        <h1>{{ result.title }}</h1>
    </div>
    <div class="widthAuto">
        <div class="breadcrumb widthAuto">
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
                <div class="pure-u-2-3">
                    <div class="l-box">
                        <p style="text-indent:1em">{{ result.content }}</p>
                        <div style="text-align:center">
                            <img src="{{ result.img_dir }}">
                        </div>
                    </div>
                </div>
                <div class="pure-u-1-3">
                    {% if brother is defined %}
                    <div class="l-box brother">
                        <div class="model_header">
                            <h3 class="model_name left">
                                <span class="model_text">相关分类</span>
                            </h3>
                        </div>
                        <div class="">
                            <div class="pure-g-r">
                                    {% for one in brother %}
                                        <div class="pure-u-1-2 ">
                                            <ul style="padding:0;margin:0.5em 0;">
                                                <li>
                                                <?= $this->tag->linkTo("category/".$one->id, $one->name) ?>
                                                <li>
                                            </ul>
                                        </div>
                                    {% endfor %}
                            </div>
                        </div>  
                    </div>
                    {% endif %}
                    <div class="l-box">
                        <div class="model_header">
                            <h3 class="model_name left">
                                <span class="model_text">本类热门</span>
                            </h3>
                        </div>
                        <div class="model_body" style="height:14em">
                            <div class="model_list_word">
                                <ul>                
                                    {% for article in hotResult %}
                                    <li>
                                        <span class="article_info">
                                            <?= $this->tag->linkTo("topic/".$article->id, $article->title) ?>
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

